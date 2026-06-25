<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Payment;
use App\Models\Act;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PaymentTrackingSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        Act::query()->delete();
        Payment::query()->delete();
        Project::query()->delete();
        Client::query()->delete();

        // Create clients (legal entities)
        $clients = [
            [
                'name' => 'ООО "Веб-Студия"',
                'inn' => '1234567890',
                'ogrn' => '1234567890123',
            ],
            [
                'name' => 'ИП Иванов А.А.',
                'inn' => '0987654321',
                'ogrn' => '0987654321098',
            ],
            [
                'name' => 'ЗАО "Маркетинг Про"',
                'inn' => '1122334455',
                'ogrn' => '1122334455667',
            ],
            [
                'name' => 'ООО "ТехноСистемы"',
                'inn' => '5566778899',
                'ogrn' => '5566778899001',
            ],
            [
                'name' => 'ИП Петрова С.И.',
                'inn' => '6677889900',
                'ogrn' => '6677889900112',
            ],
        ];

        $clientIds = [];
        foreach ($clients as $clientData) {
            $client = Client::create($clientData);
            $clientIds[] = $client->id;
        }

        // Create projects
        $projects = [
            [
                'name' => 'Разработка корпоративного сайта',
                'status' => 'active',
            ],
            [
                'name' => 'SEO продвижение интернет-магазина',
                'status' => 'active',
            ],
            [
                'name' => 'Контекстная реклама Яндекс.Директ',
                'status' => 'active',
            ],
            [
                'name' => 'Дизайн мобильного приложения',
                'status' => 'completed',
            ],
            [
                'name' => 'Техническая поддержка сайта',
                'status' => 'active',
            ],
            [
                'name' => 'SMM продвижение в социальных сетях',
                'status' => 'active',
            ],
            [
                'name' => 'Разработка CRM системы',
                'status' => 'active',
            ],
        ];

        $projectIds = [];
        foreach ($projects as $index => $projectData) {
            $project = Project::create([
                ...$projectData,
                'client_id' => $clientIds[$index % count($clientIds)],
            ]);
            $projectIds[] = $project->id;
        }

        // Create payments
        $payments = [
            // Project 1: Разработка корпоративного сайта
            [
                'payment_date' => Carbon::now()->subDays(45),
                'amount' => 150000.00,
                'payment_purpose' => 'Аванс за разработку сайта',
                'service_stage' => 'Аванс 30%',
            ],
            [
                'payment_date' => Carbon::now()->subDays(15),
                'amount' => 250000.00,
                'payment_purpose' => 'Оплата за верстку и дизайн',
                'service_stage' => 'Этап 2: Верстка',
            ],
            [
                'payment_date' => Carbon::now()->subDays(5),
                'amount' => 100000.00,
                'payment_purpose' => 'Оплата за программирование',
                'service_stage' => 'Этап 3: Программирование',
            ],

            // Project 2: SEO продвижение
            [
                'payment_date' => Carbon::now()->subDays(60),
                'amount' => 50000.00,
                'payment_purpose' => 'Аудит сайта и составление ТЗ',
                'service_stage' => 'Начальный аудит',
            ],
            [
                'payment_date' => Carbon::now()->subDays(30),
                'amount' => 75000.00,
                'payment_purpose' => 'Техническая оптимизация сайта',
                'service_stage' => 'Техническая SEO',
            ],
            [
                'payment_date' => Carbon::now()->subDays(10),
                'amount' => 65000.00,
                'payment_purpose' => 'Контентная оптимизация',
                'service_stage' => 'Контентное SEO',
            ],

            // Project 3: Контекстная реклама
            [
                'payment_date' => Carbon::now()->subDays(40),
                'amount' => 40000.00,
                'payment_purpose' => 'Настройка рекламных кампаний',
                'service_stage' => 'Настройка',
            ],
            [
                'payment_date' => Carbon::now()->subDays(20),
                'amount' => 30000.00,
                'payment_purpose' => 'Ведение рекламных кампаний за октябрь',
                'service_stage' => 'Ведение',
            ],
            [
                'payment_date' => Carbon::now()->subDays(5),
                'amount' => 35000.00,
                'payment_purpose' => 'Ведение рекламных кампаний за ноябрь',
                'service_stage' => 'Ведение',
            ],

            // Project 4: Дизайн приложения
            [
                'payment_date' => Carbon::now()->subDays(90),
                'amount' => 80000.00,
                'payment_purpose' => 'Разработка прототипов интерфейса',
                'service_stage' => 'Прототипирование',
            ],
            [
                'payment_date' => Carbon::now()->subDays(60),
                'amount' => 120000.00,
                'payment_purpose' => 'Разработка финального дизайна',
                'service_stage' => 'Дизайн',
            ],

            // Project 5: Техническая поддержка
            [
                'payment_date' => Carbon::now()->subDays(35),
                'amount' => 20000.00,
                'payment_purpose' => 'Техподдержка сайта за сентябрь',
                'service_stage' => 'Ежемесячная поддержка',
            ],
            [
                'payment_date' => Carbon::now()->subDays(5),
                'amount' => 20000.00,
                'payment_purpose' => 'Техподдержка сайта за октябрь',
                'service_stage' => 'Ежемесячная поддержка',
            ],

            // Project 6: SMM продвижение
            [
                'payment_date' => Carbon::now()->subDays(25),
                'amount' => 45000.00,
                'payment_purpose' => 'Ведение соцсетей и контент-план',
                'service_stage' => 'Контент',
            ],
            [
                'payment_date' => Carbon::now()->subDays(5),
                'amount' => 35000.00,
                'payment_purpose' => 'Таргетированная реклама в соцсетях',
                'service_stage' => 'Реклама',
            ],

            // Project 7: CRM система
            [
                'payment_date' => Carbon::now()->subDays(50),
                'amount' => 300000.00,
                'payment_purpose' => 'Аванс за разработку CRM',
                'service_stage' => 'Аванс 40%',
            ],
            [
                'payment_date' => Carbon::now()->subDays(20),
                'amount' => 250000.00,
                'payment_purpose' => 'Оплата за модуль клиентов',
                'service_stage' => 'Модуль клиентов',
            ],
        ];

        $paymentIds = [];
        $projectIndex = 0;
        $paymentCounter = 0;

        foreach ($payments as $paymentData) {
            // Distribute payments across projects
            $projectId = $projectIds[$projectIndex];
            $clientIdForProject = Project::find($projectId)->client_id;
            
            $payment = Payment::create([
                ...$paymentData,
                'project_id' => $projectId,
                'client_id' => $clientIdForProject,
            ]);
            
            $paymentIds[] = $payment->id;
            $paymentCounter++;
            
            // Move to next project after 2-3 payments
            if ($paymentCounter >= rand(2, 3)) {
                $projectIndex = ($projectIndex + 1) % count($projectIds);
                $paymentCounter = 0;
            }
        }

        // Create acts with different statuses
        foreach ($paymentIds as $index => $paymentId) {
            $statusType = $index % 5; // Distribute statuses
            
            $actData = [
                'payment_id' => $paymentId,
                'manager_comment' => $this->getRandomComment(),
            ];
            
            switch ($statusType) {
                case 0: // Not sent
                    $actData['is_sent'] = false;
                    $actData['is_signed'] = false;
                    break;
                case 1: // Sent, awaiting signature (recent)
                    $actData['is_sent'] = true;
                    $actData['sent_at'] = Carbon::now()->subDays(5);
                    $actData['is_signed'] = false;
                    break;
                case 2: // Sent, awaiting signature (old - attention required)
                    $actData['is_sent'] = true;
                    $actData['sent_at'] = Carbon::now()->subDays(45);
                    $actData['is_signed'] = false;
                    break;
                case 3: // Closed
                    $actData['is_sent'] = true;
                    $actData['sent_at'] = Carbon::now()->subDays(30);
                    $actData['is_signed'] = true;
                    $actData['signed_at'] = Carbon::now()->subDays(25);
                    break;
                case 4: // Closed with delay
                    $actData['is_sent'] = true;
                    $actData['sent_at'] = Carbon::now()->subDays(60);
                    $actData['is_signed'] = true;
                    $actData['signed_at'] = Carbon::now()->subDays(40);
                    break;
            }
            
            Act::create($actData);
        }

        $this->command->info('Payment tracking data seeded successfully!');
        $this->command->info('Total clients: ' . count($clientIds));
        $this->command->info('Total projects: ' . count($projectIds));
        $this->command->info('Total payments: ' . count($paymentIds));
        $this->command->info('Total acts: ' . Act::count());
    }

    private function getRandomComment(): string
    {
        $comments = [
            'Клиент запросил дополнительные документы',
            'Акт отправлен по электронной почте',
            'Ожидаем подпись от генерального директора',
            'Документы переданы курьером',
            'Клиент в отпуске, подпись после возвращения',
            'Все документы в порядке',
            'Требуется дополнительное согласование с юристами',
            'Акт подписан, оригинал получен',
            'Ожидаем оплату по акту',
            'Документы переданы в бухгалтерию клиента',
        ];
        
        return $comments[array_rand($comments)];
    }
}