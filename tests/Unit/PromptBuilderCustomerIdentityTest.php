<?php

namespace Tests\Unit;

use App\Application\Llm\Services\PromptBuilder;
use App\Domain\Llm\DTOs\AudioAnalysisRequestData;
use App\Domain\Llm\DTOs\PromptContextData;
use PHPUnit\Framework\TestCase;

class PromptBuilderCustomerIdentityTest extends TestCase
{
    public function test_context_prompt_includes_crm_context_json(): void
    {
        $builder = new PromptBuilder;
        $request = new AudioAnalysisRequestData(
            callId: 1,
            context: new PromptContextData(
                employeeName: 'علی رضایی',
                organizationName: 'میرکو',
            ),
        );

        $prompt = $builder->contextPrompt($request);

        $this->assertStringContainsString('زمینه CRM:', $prompt);
        $this->assertStringContainsString('"current_user_name":"علی رضایی"', $prompt);
        $this->assertStringContainsString('"current_company_name":"میرکو"', $prompt);
    }

    public function test_customer_identity_policy_includes_crm_exclusion_rules(): void
    {
        $policy = PromptBuilder::customerIdentityPolicy();

        $this->assertStringContainsString('customer_identity', $policy);
        $this->assertStringContainsString('Do NOT identify these values as customer information', $policy);
        $this->assertStringContainsString('current CRM user name', $policy);
    }
}
