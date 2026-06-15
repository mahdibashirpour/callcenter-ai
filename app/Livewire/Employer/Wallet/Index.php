<?php

namespace App\Livewire\Employer\Wallet;

use App\Domain\AiUsage\Enums\UsageAggregationPeriod;
use App\Services\AiBillingService;
use App\Services\AiUsageAnalyticsService;
use App\Services\EmployerContext;
use App\Services\WalletService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.employer')]
#[Title('کیف پول هوش مصنوعی')]
class Index extends Component
{
    public function render()
    {
        $organizationId = EmployerContext::organizationId();
        $billing = app(AiBillingService::class);
        $analytics = app(AiUsageAnalyticsService::class);
        $wallet = app(WalletService::class)->forOrganization($organizationId);

        $overview = $billing->walletOverview($organizationId);
        $dailyTrend = $analytics->organizationTrend($organizationId, UsageAggregationPeriod::Daily, 30);
        $monthlyTrend = $analytics->organizationTrend($organizationId, UsageAggregationPeriod::Monthly, 180);

        $lowBalance = $wallet->balance < ($wallet->currency === 'IRR' ? 100_000 : 10);

        return view('livewire.employer.wallet.index', [
            'overview' => $overview,
            'dailyTrend' => $dailyTrend,
            'monthlyTrend' => $monthlyTrend,
            'lowBalance' => $lowBalance,
            'showAiInfrastructure' => \App\Support\AiInfrastructure::isVisible(),
        ]);
    }
}
