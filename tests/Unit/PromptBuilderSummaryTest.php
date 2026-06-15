<?php

namespace Tests\Unit;

use App\Application\Llm\Services\PromptBuilder;
use App\Domain\Llm\DTOs\AudioAnalysisRequestData;
use App\Domain\Llm\DTOs\PromptContextData;
use PHPUnit\Framework\TestCase;

class PromptBuilderSummaryTest extends TestCase
{
    public function test_summary_policy_requires_detailed_business_summary(): void
    {
        $policy = PromptBuilder::summaryPolicy();

        $this->assertStringContainsString('Generate a detailed business summary in Persian', $policy);
        $this->assertStringContainsString('why the customer contacted us', $policy);
        $this->assertStringContainsString('what follow-up actions are required', $policy);
        $this->assertStringContainsString('Prefer a comprehensive summary over a very short summary', $policy);
        $this->assertStringContainsString('۱ تا ۳ پاراگراف', $policy);
        $this->assertStringContainsString('رونوشت را تکرار نکنید', $policy);
    }

    public function test_context_prompt_requests_detailed_summary(): void
    {
        $builder = new PromptBuilder;
        $request = new AudioAnalysisRequestData(callId: 1);

        $prompt = $builder->contextPrompt($request);

        $this->assertStringContainsString('خلاصه (summary) باید مفصل و کسب‌وکاری باشد', $prompt);
    }
}
