@include('livewire.shared.analysis-detail', [
    'analysis' => $analysis,
    'recordingUrl' => $recordingUrl,
    'recordingExpired' => $recordingExpired ?? false,
    'visibilityMode' => 'full',
    'backUrl' => route('employer.intelligence.index'),
    'queueUrl' => $analysis->call?->processingJob
        ? route('employer.processing-queue.show', $analysis->call->processingJob)
        : null,
])
