<?php

namespace App\Livewire\Employee\Calls;

use App\Models\ConversationAnalysis;
use App\Services\EmployeeContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.employee')]
#[Title('تماس‌های من')]
class Index extends Component
{
    use WithPagination;

    public function render()
    {
        $calls = ConversationAnalysis::query()
            ->where('organization_user_id', EmployeeContext::membership()->id)
            ->with('callLog')
            ->latest('analyzed_at')
            ->paginate(12);

        return view('livewire.employee.calls.index', compact('calls'));
    }
}
