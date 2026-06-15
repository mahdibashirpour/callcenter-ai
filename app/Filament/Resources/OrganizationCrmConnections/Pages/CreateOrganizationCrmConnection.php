<?php

namespace App\Filament\Resources\OrganizationCrmConnections\Pages;

use App\Filament\Resources\OrganizationCrmConnections\OrganizationCrmConnectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOrganizationCrmConnection extends CreateRecord
{
    protected static string $resource = OrganizationCrmConnectionResource::class;

    public function mount(): void
    {
        parent::mount();

        $organizationId = request()->integer('organization_id');

        if ($organizationId) {
            $this->form->fill([
                'organization_id' => $organizationId,
            ]);
        }
    }
}
