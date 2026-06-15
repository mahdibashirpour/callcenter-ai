<?php

namespace App\Livewire\Employee\Performance;

use App\Models\ConversationAnalysis;
use App\Services\EmployeeContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.employee')]
#[Title('عملکرد من')]
class Index extends Component
{
    public function render()
    {
        $membershipId = EmployeeContext::membership()->id;
        $query = ConversationAnalysis::query()->where('organization_user_id', $membershipId);

        return view('livewire.employee.performance.index', [
            'currentScore' => $query->clone()->latest('analyzed_at')->value('score'),
            'weeklyScore' => round((float) $query->clone()->where('analyzed_at', '>=', now()->subWeek())->avg('score'), 1),
            'monthlyScore' => round((float) $query->clone()->whereMonth('analyzed_at', now()->month)->avg('score'), 1),
            'bestScore' => $query->clone()->max('score'),
            'totalAnalyzed' => $query->clone()->count(),
            'trend' => $query->clone()->latest('analyzed_at')->limit(10)->get(),
        ]);
    }
}
