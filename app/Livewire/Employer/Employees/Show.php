<?php

namespace App\Livewire\Employer\Employees;

use App\Models\OrganizationUser;
use App\Services\EmployerContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.employer')]
#[Title('کارشناس')]
class Show extends Component
{
    public OrganizationUser $employee;

    public function mount(OrganizationUser $employee): void
    {
        abort_unless($employee->organization_id === EmployerContext::organizationId(), 404);
        $this->employee = $employee->load(['user', 'integrationMeta.integratable', 'conversationAnalyses']);
    }

    public function render()
    {
        return view('livewire.employer.employees.show');
    }
}
