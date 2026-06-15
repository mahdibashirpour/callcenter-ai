<?php

namespace App\Livewire\Employer\Analytics;

use App\Services\AiPerformanceAnalytics;
use App\Services\EmployerContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.employer')]
#[Title('تحلیل‌ها')]
class Index extends Component
{
    public function render()
    {
        $analytics = AiPerformanceAnalytics::forOrganization(EmployerContext::organizationId());

        return view('livewire.employer.analytics.index', [
            'overview' => $analytics->overview(),
            'insights' => $analytics->organizationInsights(),
            'trend' => $analytics->scoreTrend('week', now()->subDays(60)),
        ]);
    }
}
