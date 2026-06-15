<?php

namespace App\Livewire\Employer\Employees;

use App\Models\OrganizationUser;
use App\Services\EmployerContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.employer')]
#[Title('کارشناسان')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $employees = OrganizationUser::query()
            ->where('organization_id', EmployerContext::organizationId())
            ->with(['user'])
            ->withCount('conversationAnalyses')
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                    ->orWhere('last_name', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('email', 'like', "%{$this->search}%"));
            }))
            ->latest()
            ->paginate(12);

        return view('livewire.employer.employees.index', compact('employees'));
    }
}
