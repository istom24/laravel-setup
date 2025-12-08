<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TaskFlowSeeder extends Seeder
{
    private array $usersData = [
        ['name' => 'Безпалий М. С.',     'email' => 'user1@example.com'],
        ['name' => 'Бойко Б. В.',        'email' => 'user2@example.com'],
        ['name' => 'Брежко С. А.',       'email' => 'user3@example.com'],
        ['name' => 'Гармаш Д. С.',       'email' => 'user4@example.com'],
        ['name' => 'Гвоздь А. В.',       'email' => 'user5@example.com'],
        ['name' => 'Гончаров А. Д.',     'email' => 'user6@example.com'],
        ['name' => 'Заруднєв Н. А.',     'email' => 'user7@example.com'],
        ['name' => 'Істоміна Я. А.',     'email' => 'user8@example.com'],
    ];
    private array $projectsData = [
        [
            'name' => 'Математична статистика',
            'description' => 'Проєкт включає аналіз даних, побудову статистичних моделей...'
        ],
        [
            'name' => 'Математичні методи теорії штучного інтелекту',
            'description' => 'Створення інтелектуальних алгоритмів...'
        ],
        [
            'name' => 'Методи оптимізації',
            'description' => 'Реалізація задач оптимізації:пошук найкращих рішень...'
        ],
        [
            'name' => 'Механіка роботів',
            'description' => 'Моделювання та аналіз кінематики і динаміки роботів...'
        ],
        [
            'name' => 'Програмування та підтримка Web застосунків',
            'description' => 'Розробка веб-платформи мові програмування PHP...'
        ],
        [
            'name' => 'Дисципліна вільного вибору 1',
            'description' => 'Проєкт залежить від обраної дисципліни: дизайн, аналітика...'
        ],
        [
            'name' => 'Проєктна робота',
            'description' => 'Комплексний проєкт, що об’єднує знання з різних дисциплін...'
        ],
        [
            'name' => 'Теорія управління',
            'description' => 'Моделювання керованих систем, побудова регуляторів...'
        ],
    ];
    private array $taskTitles = [
        'Побудувати статистичну модель та виконати аналіз даних',
        'Реалізувати алгоритм машинного навчання для класифікації',
        'Налаштувати оптимізаційний алгоритм для пошуку найкращого рішення',
        'Створити модель руху робота та прорахувати кінематику',
        'Розробити контролер на основі ПІД-регулятора',
        'Створити систему прогнозування на основі статистичних методів',
        'Реалізувати нейронну мережу для розпізнавання даних',
        'Виконати оптимізацію параметрів моделі ШІ',
        'Створити симуляцію роботи керованої системи',
        'Розробити модуль обробки сигналів для системи управління',
        'Створити веб-інтерфейс для відображення аналітики',
        'Реалізувати серверну логіку для роботи AI-моделі',
        'Інтегрувати обчислювальний модуль оптимізації в веб-систему',
        'Розробити REST API для роботи з науковими даними',
        'Підготувати технічну документацію для системи керування',
    ];

    private array $commentMessages = [
        'Статистична модель побудована коректно, результати співпадають з еталонними.',
        'Потрібно уточнити вибірку даних, статистичні показники виходять некоректні.',
        'Оптимізаційний алгоритм працює стабільно, знайдено найкраще рішення.',
        'Алгоритм машинного навчання навчається, але точність потребує покращення.',
        'Кінематична модель робота побудована правильно, рух прогнозується точно.',
        'Регулятор працює, але система має невелике перерегулювання — потрібно підібрати параметри.',
        'Нейронна мережа дає гарні результати, можна запускати на повній вибірці.',
        'Виявлено проблему при інтеграції модуля оптимізації з основною системою.',
        'Симуляція керованої системи пройшла без помилок, поведінка стабільна.',
        'Потрібно оновити документацію після змін у моделі та методах обчислень.',
    ];

    private array $roles = ['owner', 'member', 'viewer'];

    private array $statuses = ['todo', 'in_progress', 'review', 'done'];

    private array $priorities = ['low', 'medium', 'high', 'urgent'];

    public function run(): void
    {
        $users = collect();

        foreach ($this->usersData as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);
            $users->push($user);
        }

        $projects = collect();

        foreach ($this->projectsData as $index => $projectData) {
            $project = Project::create([
                'owner_id' => $users[$index]->id,
                'name' => $projectData['name'],
                'description' => $projectData['description'],
            ]);
            $projects->push($project);
        }

        foreach ($projects as $project) {
            $project->users()->attach($project->owner_id, ['role' => 'owner']);

            $randomUsers = $users->where('id', '!=', $project->owner_id)
                ->random(rand(3, 5));

            foreach ($randomUsers as $user) {
                $project->users()->attach($user->id, [
                    'role' => $this->roles[array_rand($this->roles)]
                ]);
            }

            for ($i = 0; $i < rand(5, 8); $i++) {
                $task = Task::create([
                    'project_id' => $project->id,
                    'author_id' => $project->owner_id,
                    'assignee_id' => $users->random()->id,
                    'title' => $this->taskTitles[array_rand($this->taskTitles)],
                    'description' => 'Детальний опис задачі. Необхідно виконати до вказаного терміну.',
                    'status' => $this->statuses[array_rand($this->statuses)],
                    'priority' => $this->priorities[array_rand($this->priorities)],
                    'due_date' => now()->addDays(rand(1, 30)),
                ]);

                for ($j = 0; $j < rand(2, 4); $j++) {
                    Comment::create([
                        'task_id' => $task->id,
                        'author_id' => $users->random()->id,
                        'body' => $this->commentMessages[array_rand($this->commentMessages)],
                        'created_at' => now()->subDays(rand(0, 7)),
                    ]);
                }
            }
        }

        for ($i = 0; $i < 5; $i++) {
            $startDate = now()->subDays(rand(30, 60));
            $endDate = $startDate->copy()->addDays(rand(7, 30));

            Report::create([
                'period_start' => $startDate,
                'period_end' => $endDate,
                'payload' => json_encode([
                    'tasks_completed' => rand(5, 50),
                    'tasks_pending' => rand(1, 20),
                    'total_users' => rand(5, 15),
                    'generated_at' => now()->toDateTimeString(),
                ]),
                'path' => '/reports/report_' . ($i + 1) . '.pdf',
                'created_at' => $endDate,
            ]);
        }
    }
}
