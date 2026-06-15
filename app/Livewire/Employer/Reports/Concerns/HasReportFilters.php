<?php

namespace App\Livewire\Employer\Reports\Concerns;

use App\DTOs\ReportFilter;
use App\Enums\ReportDatePreset;
use App\Services\EmployerContext;
use Livewire\Attributes\Url;

trait HasReportFilters
{
    #[Url(as: 'preset')]
    public string $datePreset = 'last_30';

    #[Url(as: 'from')]
    public ?string $customFrom = null;

    #[Url(as: 'to')]
    public ?string $customTo = null;

    /** @var list<int|string> */
    #[Url(as: 'employees')]
    public array $selectedEmployeeIds = [];

    #[Url(as: 'compare')]
    public bool $compareMode = false;

    public function updatedDatePreset(): void
    {
        //
    }

    public function updatedCustomFrom(): void
    {
        $this->datePreset = ReportDatePreset::Custom->value;
    }

    public function updatedCustomTo(): void
    {
        $this->datePreset = ReportDatePreset::Custom->value;
    }

    public function updatedSelectedEmployeeIds(): void
    {
        $this->selectedEmployeeIds = array_values(array_filter(
            array_map('intval', $this->selectedEmployeeIds),
        ));
    }

    public function clearEmployeeFilter(): void
    {
        $this->selectedEmployeeIds = [];
        $this->compareMode = false;
    }

    public function toggleEmployee(int $employeeId): void
    {
        if (in_array($employeeId, $this->selectedEmployeeIds, true)) {
            $this->selectedEmployeeIds = array_values(array_diff($this->selectedEmployeeIds, [$employeeId]));
        } else {
            $this->selectedEmployeeIds[] = $employeeId;
        }
    }

    protected function reportFilter(): ReportFilter
    {
        $preset = ReportDatePreset::tryFrom($this->datePreset) ?? ReportDatePreset::Last30;

        return ReportFilter::make(
            organizationId: EmployerContext::organizationId(),
            preset: $preset,
            customFrom: $this->customFrom ? \Carbon\Carbon::parse($this->customFrom) : null,
            customTo: $this->customTo ? \Carbon\Carbon::parse($this->customTo) : null,
            employeeIds: array_map('intval', $this->selectedEmployeeIds),
            compareMode: $this->compareMode,
        );
    }
}
