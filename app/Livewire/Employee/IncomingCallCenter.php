<?php

namespace App\Livewire\Employee;

use App\Services\CallClaimingService;
use App\Services\EmployeeContext;
use Livewire\Attributes\On;
use Livewire\Component;

class IncomingCallCenter extends Component
{
    public ?array $incomingCall = null;

    public ?array $intelligence = null;

    public bool $showPopup = false;

    public bool $showPanel = false;

    public ?string $claimError = null;

    public int $organizationId = 0;

    public function mount(): void
    {
        $this->organizationId = EmployeeContext::organizationId();
    }

    #[On('incoming-call-received')]
    public function onIncomingCall(mixed $payload = null): void
    {
        $data = is_array($payload) ? $payload : [];
        if (isset($data['session_id'])) {
            $this->incomingCall = $data;
            $this->showPopup = true;
            $this->showPanel = false;
            $this->claimError = null;
        }
    }

    #[On('incoming-call-claimed')]
    public function onCallClaimed(mixed $payload = null): void
    {
        $data = is_array($payload) ? $payload : [];
        $sessionId = $data['session_id'] ?? null;
        $claimedBy = $data['claimed_by_organization_user_id'] ?? null;

        if ($this->incomingCall && ($this->incomingCall['session_id'] ?? null) == $sessionId) {
            if ($claimedBy !== EmployeeContext::membership()->id) {
                $this->showPopup = false;
                $this->incomingCall = null;
            }
        }
    }

    public function acceptCall(CallClaimingService $claiming): void
    {
        if (! $this->incomingCall) {
            return;
        }

        try {
            $session = $claiming->claim(
                (int) $this->incomingCall['session_id'],
                EmployeeContext::membership()->id,
            );

            $this->intelligence = $session->intelligencePayload();
            $this->showPopup = false;
            $this->showPanel = true;
            $this->claimError = null;
        } catch (\Throwable $e) {
            $this->claimError = $e->getMessage();
            $this->showPopup = false;
        }
    }

    public function dismissPopup(): void
    {
        $this->showPopup = false;
        $this->incomingCall = null;
    }

    public function closePanel(): void
    {
        $this->showPanel = false;
        $this->intelligence = null;
        $this->incomingCall = null;
    }

    public function render()
    {
        $organization = EmployeeContext::organization();

        return view('livewire.employee.incoming-call-center', [
            'hasCrm' => $organization->crmConnections()->where('is_active', true)->exists(),
            'hasVoip' => $organization->voipConnections()->where('is_active', true)->exists(),
        ]);
    }
}
