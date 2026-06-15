<?php

namespace App\Livewire\Employee\Customers;

use App\Models\Customer;
use App\Services\EmployeeContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.employee')]
#[Title('مشتریان')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $organizationId = EmployeeContext::membership()->organization_id;

        $customers = Customer::query()
            ->forOrganization($organizationId)
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('company_name', 'like', $term)
                        ->orWhere('phone_number', 'like', $term);
                });
            })
            ->orderByDesc('last_contact_at')
            ->paginate(15);

        return view('livewire.employee.customers.index', [
            'customers' => $customers,
        ]);
    }
}
