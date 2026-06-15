<?php

namespace App\Livewire\Employer\Customers;

use App\Models\Customer;
use App\Services\EmployerContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.employer')]
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
        $customers = Customer::query()
            ->forOrganization(EmployerContext::organizationId())
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('company_name', 'like', $term)
                        ->orWhere('phone_number', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->orderByDesc('last_contact_at')
            ->paginate(15);

        return view('livewire.employer.customers.index', [
            'customers' => $customers,
        ]);
    }
}
