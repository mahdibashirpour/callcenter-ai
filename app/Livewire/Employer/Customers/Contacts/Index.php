<?php

namespace App\Livewire\Employer\Customers\Contacts;

use App\Models\Customer;
use App\Services\EmployerContext;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.employer')]
#[Title('مخاطبین')]
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
        $organizationId = EmployerContext::organizationId();

        $contacts = Customer::query()
            ->forOrganization($organizationId)
            ->with('company')
            ->when($this->search !== '', function ($query) {
                $term = '%'.$this->search.'%';
                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('company_name', 'like', $term)
                        ->orWhere('phone_number', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhereHas('company', fn ($company) => $company->where('name', 'like', $term));
                });
            })
            ->orderByDesc('last_contact_at')
            ->paginate(15);

        return view('livewire.shared.customers.contacts.index', [
            'contacts' => $contacts,
            'portal' => 'employer',
        ]);
    }
}
