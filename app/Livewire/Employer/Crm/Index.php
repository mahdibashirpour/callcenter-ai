<?php

namespace App\Livewire\Employer\Crm;

use App\Enums\IntegrationSetupStatus;
use App\Services\EmployerContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.employer')]
#[Title('CRM')]
class Index extends Component
{
    public function render()
    {
        $organization = EmployerContext::organization();
        $readiness = EmployerContext::integrationReadiness();
        $isComplete = $readiness->crmStatus === IntegrationSetupStatus::Complete;

        return view('livewire.employer.crm.index', [
            'connections' => $isComplete
                ? $organization->crmConnections()->with('provider')->get()
                : collect(),
            'integrationReadiness' => $readiness->toArray(),
            'isComplete' => $isComplete,
        ]);
    }
}
