<?php

namespace App\Jobs;

use App\Models\Report;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $projectId;
    public $userId;
    public $period;

    public function __construct($projectId = null, $userId = null, $period = 30)
    {
        $this->projectId = $projectId;
        $this->userId = $userId;
        $this->period = $period;
    }

    public function handle()
    {
        Log::info('Запущена фоновая генерация отчета', [
            'project_id' => $this->projectId,
            'user_id' => $this->userId,
            'period' => $this->period
        ]);

        $periodStart = Carbon::now()->subDays($this->period);
        $periodEnd = Carbon::now();

        // Получаем проекты для анализа
        $projects = $this->projectId
            ? Project::where('id', $this->projectId)->get()
            : Project::all();

        $reportData = [];

        foreach ($projects as $project) {
            $tasks = $project->tasks()
                ->whereBetween('created_at', [$periodStart, $periodEnd])
                ->get();

            $reportData[] = [
                'project_id' => $project->id,
                'project_name' => $project->name,
                'total_tasks' => $tasks->count(),
                'status_todo' => $tasks->where('status', 'todo')->count(),
                'status_in_progress' => $tasks->where('status', 'in_progress')->count(),
                'status_review' => $tasks->where('status', 'review')->count(),
                'status_done' => $tasks->where('status', 'done')->count(),
                'expired' => $tasks->where('due_date', '<', now())
                    ->where('status', '!=', 'done')
                    ->count(),
                'priority_high' => $tasks->where('priority', 'high')->count(),
                'priority_urgent' => $tasks->where('priority', 'urgent')->count(),
            ];
        }


        $fileName = 'reports/report_' . now()->format('Y-m-d_H-i-s') . '_' . uniqid() . '.json';

        Storage::put($fileName, json_encode($reportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Сохраняем отчет в базу
        $report = Report::create([
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'payload' => $reportData,
            'path' => $fileName,
        ]);

        Log::info('Отчет успешно сгенерирован', [
            'report_id' => $report->id,
            'file_path' => $fileName,
            'projects_analyzed' => count($projects)
        ]);
    }
}

