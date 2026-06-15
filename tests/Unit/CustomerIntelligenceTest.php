<?php

namespace Tests\Unit;

use App\Domain\Call\Enums\ConversationSource;
use App\Domain\Llm\Enums\AnalysisSentiment;
use App\Models\Call;
use App\Models\ConversationAnalysis;
use App\Models\Customer;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\User;
use App\Services\CustomerIntelligenceService;
use App\Services\CustomerPhoneResolver;
use App\Support\CustomerAnalysisVisibility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerIntelligenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_phone_resolver_normalizes_digits(): void
    {
        $resolver = app(CustomerPhoneResolver::class);

        $this->assertSame('989121234567', $resolver->normalize('+98 912-123-4567'));
    }

    public function test_sync_creates_customer_grouped_by_phone(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();
        $employee = OrganizationUser::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'first_name' => 'Sara',
            'last_name' => 'Agent',
            'is_active' => true,
        ]);

        $call = Call::query()->create([
            'organization_id' => $organization->id,
            'organization_user_id' => $employee->id,
            'source' => ConversationSource::Voip,
            'provider_code' => 'novatel',
            'external_call_id' => 'test-call-1',
            'direction' => 'inbound',
            'caller_number' => '09121234567',
            'receiver_number' => '02100000000',
            'status' => 'completed',
            'processing_status' => 'analyzed',
            'started_at' => now()->subDay(),
        ]);

        $analysis = ConversationAnalysis::query()->create([
            'organization_id' => $organization->id,
            'organization_user_id' => $employee->id,
            'call_id' => $call->id,
            'source' => ConversationSource::Voip,
            'llm_provider' => 'openai',
            'model_name' => 'gpt-4o-mini',
            'score' => 85,
            'summary' => 'خلاصه',
            'sentiment' => AnalysisSentiment::Positive,
            'strengths_json' => [],
            'weaknesses_json' => [],
            'next_actions_json' => ['پیگیری هفته آینده'],
            'lead_quality_json' => ['score' => 80, 'level' => 'high', 'reason' => 'test'],
            'customer_identity_json' => [
                'person_name' => 'علی رضایی',
                'company_name' => 'آلفا',
                'email' => 'ali@example.com',
                'confidence' => 0.9,
            ],
            'analyzed_at' => now(),
        ]);

        $customer = app(CustomerIntelligenceService::class)->syncFromAnalysis($analysis);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertSame('علی رضایی', $customer->name);
        $this->assertSame('09121234567', $customer->phone_number);
        $this->assertSame('09121234567', $customer->normalized_phone);
        $this->assertSame(1, $customer->total_calls);
        $this->assertSame($customer->id, $call->fresh()->customer_id);
    }

    public function test_employee_cannot_view_other_employee_performance(): void
    {
        $organization = Organization::factory()->create();
        $analysis = ConversationAnalysis::query()->make([
            'organization_id' => $organization->id,
            'organization_user_id' => 99,
        ]);

        $this->assertFalse(CustomerAnalysisVisibility::canViewEmployeePerformance(1, $analysis, false));
        $this->assertTrue(CustomerAnalysisVisibility::canViewEmployeePerformance(99, $analysis, false));
        $this->assertTrue(CustomerAnalysisVisibility::canViewEmployeePerformance(null, $analysis, true));
    }
}
