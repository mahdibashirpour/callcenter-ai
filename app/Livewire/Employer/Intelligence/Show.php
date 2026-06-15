<?php

namespace App\Livewire\Employer\Intelligence;

use App\Livewire\Concerns\ResolvesRecordingPlayback;
use App\Models\ConversationAnalysis;
use App\Services\EmployerContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.employer')]
#[Title('تحلیل')]
class Show extends Component
{
    use ResolvesRecordingPlayback;

    public ConversationAnalysis $analysis;

    public function mount(ConversationAnalysis $analysis): void
    {
        abort_unless($analysis->organization_id === EmployerContext::organizationId(), 404);
        $this->analysis = $analysis->load([
            'employee.user',
            'call.recording',
            'call.processingJob',
            'callLog',
            'crmSyncs.crmConnection.provider',
        ]);
    }

    public function recordingPlayback(): array
    {
        return $this->recordingPlaybackState(
            $this->analysis->call?->recording,
            $this->analysis->callLog?->recording_url,
        );
    }

    public function render()
    {
        $playback = $this->recordingPlayback();

        return view('livewire.employer.intelligence.show', [
            'recordingUrl' => $playback['url'],
            'recordingExpired' => $playback['expired'],
        ]);
    }
}
