<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Task;
use App\Models\Report;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GenerateReport extends Command
{
    protected $signature = 'app:generate-report
                            {--project= : ID проекту (опціонально)}
                            {--period=30 : Період у днях}
                            {--export : Експорт у файл}
                            {--silent : Тихий режим без таблиць}';

    protected $description = 'Генерує звіт за задачами у проектах';

    public function handle()
    {
        SchedulerLog::add('app:generate-report', 'started', 'Початок генерації звіту');
        $projectId = $this->option('project');
        $period = (int)$this->option('period');
        $export = $this->option('export');
        $silent = $this->option('silent');

        if (!$silent) {
            $this->info('Генерація звіту...');
        }

        $periodStart = Carbon::now()->subDays($period);
        $periodEnd = Carbon::now();

        if ($projectId) {
            $projects = Project::where('id', $projectId)->get();
            if ($projects->isEmpty()) {
                $this->error("Проект з ID {$projectId} не знайдено.");
                return 1;
            }
        } else {
            $projects = Project::all();
        }

        if (!$silent) {
            $this->line("Період: {$periodStart->format('d.m.Y')} - {$periodEnd->format('d.m.Y')}");
            $this->line("Проектів для аналізу: " . count($projects));
        }

        $reportData = [];

        foreach ($projects as $project) {
            $projectStats = $this->getProjectStatistics($project, $periodStart, $periodEnd);
            $reportData[] = $projectStats;

            if (!$silent) {
                $this->line("\nПроект: {$project->name} ({$project->id})");
                $this->table(
                    ['Статистика', 'Значення'], // Виправлено розрив рядка
                    [
                        ['Всього задач', $projectStats['total_tasks']],
                        ['В очікуванні', $projectStats['status_todo']],
                        ['В роботі', $projectStats['status_in_progress']],
                        ['На перевірці', $projectStats['status_review']],
                        ['Виконано', $projectStats['status_done']],
                        ['Протерміновано', $projectStats['expired']],
                        ['Високий пріоритет', $projectStats['priority_high']],
                        ['Терміновий пріоритет', $projectStats['priority_urgent']],
                    ]
                );
            }
        }

        $report = Report::create([
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'payload' => $reportData,
            'path' => null,
        ]);

        if ($export) {
            $fileName = "reports/report_{$report->id}_" . now()->format('Y-m-d_H-i-s') . '.json';
            Storage::put($fileName, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $report->update(['path' => $fileName]);

            if (!$silent) {
                $this->info("Звіт збережено у файл: storage/app/{$fileName}");
            }
        }

        if (!$silent) {
            $this->info("Звіт #{$report->id} успішно створено!");
            $this->info("Проаналізовано проектів: " . count($projects));
        }

        SchedulerLog::add('app:generate-report', 'completed', "Звіт #{$report->id} створено");
        return 0;
    }

    private function getProjectStatistics(Project $project, Carbon $start, Carbon $end): array
    {
        $tasks = $project->tasks()
            ->whereBetween('created_at', [$start, $end])
            ->get();

        return [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'period_start' => $start->toDateTimeString(),
            'period_end' => $end->toDateTimeString(),
            'total_tasks' => $tasks->count(),
            'status_todo' => $tasks->where('status', 'todo')->count(),
            'status_in_progress' => $tasks->where('status', 'in_progress')->count(),
            'status_review' => $tasks->where('status', 'review')->count(),
            'status_done' => $tasks->where('status', 'done')->count(),
            'expired' => $tasks->where('due_date', '<', now())->where('status', '!=', 'done')->count(),
            'priority_low' => $tasks->where('priority', 'low')->count(),
            'priority_medium' => $tasks->where('priority', 'medium')->count(),
            'priority_high' => $tasks->where('priority', 'high')->count(),
            'priority_urgent' => $tasks->where('priority', 'urgent')->count(),
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
