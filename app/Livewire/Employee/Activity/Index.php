<?php

namespace App\Livewire\Employee\Activity;

use App\Models\ConversationAnalysis;
use App\Services\EmployeeContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.employee')]
#[Title('فعالیت')]
class Index extends Component
{
    public function render()
    {
        $membershipId = EmployeeContext::membership()->id;

        return view('livewire.employee.activity.index', [
            'recentCalls' => ConversationAnalysis::query()
                ->where('organization_user_id', $membershipId)
                ->with('callLog')
                ->latest('analyzed_at')
                ->limit(10)
                ->get(),
            'feedback' => ConversationAnalysis::query()
                ->where('organization_user_id', $membershipId)
                ->whereNotNull('overall_evaluation')
                ->latest('analyzed_at')
                ->limit(5)
                ->get(),
        ]);
    }
}
