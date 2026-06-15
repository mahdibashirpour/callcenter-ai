<?php

namespace App\Domain\Llm\DTOs;

readonly class LlmSettings
{
    public function __construct(
        public ?string $defaultModel = null,
        public ?string $transcriptionModel = null,
        public ?float $temperature = null,
        public ?int $maxOutputTokens = null,
        public ?string $promptVersion = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            defaultModel: $data['default_model'] ?? null,
            transcriptionModel: $data['transcription_model'] ?? null,
            temperature: isset($data['temperature']) ? (float) $data['temperature'] : null,
            maxOutputTokens: isset($data['max_output_tokens']) ? (int) $data['max_output_tokens'] : null,
            promptVersion: $data['prompt_version'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'default_model' => $this->defaultModel,
            'transcription_model' => $this->transcriptionModel,
            'temperature' => $this->temperature,
            'max_output_tokens' => $this->maxOutputTokens,
            'prompt_version' => $this->promptVersion,
        ], fn ($value) => $value !== null);
    }
}
