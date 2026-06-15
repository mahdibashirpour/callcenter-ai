<?php

namespace Tests\Unit;

use App\Domain\Crm\DTOs\CallIntelligenceSyncData;
use App\Domain\Crm\DTOs\ContactData;
use App\Domain\Crm\DTOs\LeadData;
use App\Domain\Crm\DTOs\SyncData;
use App\Domain\Crm\DTOs\TaskData;
use App\Infrastructure\Crm\Adapters\AbstractCrmAdapter;
use App\Domain\Crm\Enums\CrmProviderCode;
use App\Domain\Crm\ValueObjects\CrmOperationResult;
use PHPUnit\Framework\TestCase;

class CrmCallIntelligenceSyncTest extends TestCase
{
    public function test_sync_prioritizes_high_lead_score_with_shorter_due_date(): void
    {
        $adapter = new class extends AbstractCrmAdapter
        {
            /** @var list<TaskData> */
            public array $tasks = [];

            public function getProviderCode(): CrmProviderCode
            {
                return CrmProviderCode::Didar;
            }

            public function testConnection(): CrmOperationResult
            {
                return CrmOperationResult::success();
            }

            public function createLead(LeadData $lead): CrmOperationResult
            {
                return CrmOperationResult::success();
            }

            public function updateLead(string $externalId, LeadData $lead): CrmOperationResult
            {
                return CrmOperationResult::success();
            }

            public function getLead(string $externalId): CrmOperationResult
            {
                return CrmOperationResult::success();
            }

            public function createContact(ContactData $contact): CrmOperationResult
            {
                return CrmOperationResult::success();
            }

            public function createTask(TaskData $task): CrmOperationResult
            {
                $this->tasks[] = $task;

                return CrmOperationResult::success();
            }

            public function sync(SyncData $sync): CrmOperationResult
            {
                return CrmOperationResult::success();
            }
        };

        $data = new CallIntelligenceSyncData(
            organizationId: 1,
            connectionId: 1,
            analysisId: 10,
            callId: 20,
            organizationUserId: 3,
            summary: 'خلاصه تماس',
            score: 85,
            sentiment: 'positive',
            strengths: [],
            weaknesses: [],
            nextActions: ['تماس مجدد با مشتری'],
            customerPhone: '09120000000',
            leadQuality: [
                'score' => 82,
                'level' => 'high',
                'reason' => 'تمایل بالا به خرید',
                'buying_intent_signals' => ['پرسش قیمت'],
            ],
            concerns: [
                ['type' => 'price', 'text' => 'نگرانی از قیمت', 'severity' => 'medium'],
            ],
        );

        $result = $adapter->syncCallIntelligence($data);

        $this->assertTrue($result->success);
        $this->assertSame(82, $result->data['lead_score']);
        $this->assertSame('high', $result->data['lead_level']);
        $this->assertCount(1, $adapter->tasks);
        $this->assertStringContainsString('[لید بالا]', $adapter->tasks[0]->title);
        $this->assertStringContainsString('نگرانی از قیمت', $adapter->tasks[0]->description);
        $this->assertSame(82, $adapter->tasks[0]->metadata['sales_priority']);
        $this->assertNotNull($adapter->tasks[0]->dueAt);
    }
}
