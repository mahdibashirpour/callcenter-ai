<?php

namespace App\Services;

class AudioDurationExtractor
{
    public function extract(string $absolutePath): ?int
    {
        if (! is_file($absolutePath)) {
            return null;
        }

        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'wav' => $this->extractWavDuration($absolutePath),
            default => $this->extractViaFfprobe($absolutePath),
        };
    }

    private function extractWavDuration(string $path): ?int
    {
        $handle = fopen($path, 'rb');

        if (! $handle) {
            return null;
        }

        $header = fread($handle, 44);
        fclose($handle);

        if (strlen($header) < 44 || substr($header, 0, 4) !== 'RIFF' || substr($header, 8, 4) !== 'WAVE') {
            return null;
        }

        $sampleRate = unpack('V', substr($header, 24, 4))[1] ?? 0;
        $byteRate = unpack('V', substr($header, 28, 4))[1] ?? 0;
        $dataSize = unpack('V', substr($header, 40, 4))[1] ?? 0;

        if ($byteRate > 0) {
            return (int) round($dataSize / $byteRate);
        }

        $channels = unpack('v', substr($header, 22, 2))[1] ?? 1;
        $bitsPerSample = unpack('v', substr($header, 34, 2))[1] ?? 16;

        if ($sampleRate > 0 && $channels > 0 && $bitsPerSample > 0) {
            $bytesPerSecond = $sampleRate * $channels * ($bitsPerSample / 8);

            return $bytesPerSecond > 0 ? (int) round($dataSize / $bytesPerSecond) : null;
        }

        return null;
    }

    private function extractViaFfprobe(string $path): ?int
    {
        if (! $this->ffprobeAvailable()) {
            return null;
        }

        $command = sprintf(
            'ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 %s 2>/dev/null',
            escapeshellarg($path),
        );

        $output = shell_exec($command);

        if (! is_string($output) || trim($output) === '') {
            return null;
        }

        $duration = (float) trim($output);

        return $duration > 0 ? (int) round($duration) : null;
    }

    private function ffprobeAvailable(): bool
    {
        static $available = null;

        if ($available === null) {
            $available = is_string(shell_exec('command -v ffprobe 2>/dev/null'));
        }

        return $available;
    }
}
