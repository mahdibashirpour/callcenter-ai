<?php

namespace App\Infrastructure\Crm\Adapters;

use App\Domain\Crm\DTOs\ContactData;
use App\Domain\Crm\DTOs\LeadData;
use App\Domain\Crm\DTOs\SyncData;
use App\Domain\Crm\DTOs\TaskData;
use App\Domain\Crm\Enums\CrmProviderCode;
use App\Domain\Crm\ValueObjects\CrmOperationResult;
use App\Infrastructure\Crm\Clients\DidarApiClient;

class DidarCrmAdapter extends AbstractCrmAdapter
{
    private ?DidarApiClient $client = null;

    public function getProviderCode(): CrmProviderCode
    {
        return CrmProviderCode::Didar;
    }

    public function testConnection(): CrmOperationResult
    {
        $response = $this->client()->post('contact/search', [
            'Criteria' => [],
            'From' => 0,
            'Limit' => 1,
        ]);

        if ($response->failed()) {
            return $this->parseHttpFailure(
                message: $response->json('Message') ?? $response->json('Error') ?? $response->body(),
                data: $response->json(),
            );
        }

        return $this->parseResponse($response->json() ?? [], 'Didar CRM connection successful.');
    }

    public function createLead(LeadData $lead): CrmOperationResult
    {
        $response = $this->client()->post('deal/save', $this->mapLeadPayload($lead));

        if ($response->failed()) {
            return $this->parseHttpFailure(
                message: $response->json('Message') ?? $response->json('Error') ?? $response->body(),
                data: $response->json(),
            );
        }

        return $this->parseResponse($response->json() ?? [], 'Lead created in Didar CRM.');
    }

    public function updateLead(string $externalId, LeadData $lead): CrmOperationResult
    {
        $payload = $this->mapLeadPayload($lead);
        $payload['Id'] = $externalId;

        $response = $this->client()->post('deal/save', $payload);

        if ($response->failed()) {
            return $this->parseHttpFailure(
                message: $response->json('Message') ?? $response->json('Error') ?? $response->body(),
                data: $response->json(),
            );
        }

        return $this->parseResponse($response->json() ?? [], 'Lead updated in Didar CRM.');
    }

    public function getLead(string $externalId): CrmOperationResult
    {
        $response = $this->client()->post('deal/search', [
            'Criteria' => ['Id' => $externalId],
            'From' => 0,
            'Limit' => 1,
        ]);

        if ($response->failed()) {
            return $this->parseHttpFailure(
                message: $response->json('Message') ?? $response->json('Error') ?? $response->body(),
                data: $response->json(),
            );
        }

        $body = $response->json() ?? [];
        $list = $body['Response']['List'] ?? $body['Response'] ?? [];

        if (empty($list)) {
            return CrmOperationResult::failure('Lead not found in Didar CRM.', data: $body);
        }

        $item = is_array($list) && isset($list[0]) ? $list[0] : $list;

        return CrmOperationResult::success(
            externalId: (string) ($item['Id'] ?? $externalId),
            data: $item,
            message: 'Lead retrieved from Didar CRM.',
        );
    }

    public function createContact(ContactData $contact): CrmOperationResult
    {
        $response = $this->client()->post('contact/save', $this->mapContactPayload($contact));

        if ($response->failed()) {
            return $this->parseHttpFailure(
                message: $response->json('Message') ?? $response->json('Error') ?? $response->body(),
                data: $response->json(),
            );
        }

        return $this->parseResponse($response->json() ?? [], 'Contact created in Didar CRM.');
    }

    public function createTask(TaskData $task): CrmOperationResult
    {
        $response = $this->client()->post('activity/save', [
            'Title' => $task->title,
            'Description' => $task->description,
            'DueDate' => $task->dueAt,
            'RelatedId' => $task->relatedExternalId,
            'Assignee' => $task->assignee,
        ]);

        if ($response->failed()) {
            return $this->parseHttpFailure(
                message: $response->json('Message') ?? $response->json('Error') ?? $response->body(),
                data: $response->json(),
            );
        }

        return $this->parseResponse($response->json() ?? [], 'Task created in Didar CRM.');
    }

    public function sync(SyncData $sync): CrmOperationResult
    {
        $endpoint = match ($sync->entity) {
            'contacts' => 'contact/search',
            'leads', 'deals' => 'deal/search',
            default => 'contact/search',
        };

        $response = $this->client()->post($endpoint, [
            'Criteria' => $sync->filters,
            'From' => 0,
            'Limit' => 100,
        ]);

        if ($response->failed()) {
            return $this->parseHttpFailure(
                message: $response->json('Message') ?? $response->json('Error') ?? $response->body(),
                data: $response->json(),
            );
        }

        $body = $response->json() ?? [];
        $totalCount = $body['Response']['TotalCount'] ?? count($body['Response']['List'] ?? []);

        return CrmOperationResult::success(
            data: $body['Response'] ?? $body,
            message: "Synced {$totalCount} records from Didar CRM.",
        );
    }

    private function client(): DidarApiClient
    {
        return $this->client ??= new DidarApiClient(
            credentials: $this->config->credentials,
            settings: $this->config->settings,
        );
    }

    private function mapLeadPayload(LeadData $lead): array
    {
        return array_filter([
            'Title' => $lead->title,
            'FirstName' => $lead->firstName,
            'LastName' => $lead->lastName,
            'Email' => $lead->email,
            'MobilePhone' => $lead->phone,
            'CompanyName' => $lead->company,
            'Description' => $lead->description,
            'Source' => $lead->source,
            'Fields' => $lead->customFields ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }

    private function mapContactPayload(ContactData $contact): array
    {
        return array_filter([
            'FirstName' => $contact->firstName,
            'LastName' => $contact->lastName,
            'Email' => $contact->email,
            'MobilePhone' => $contact->phone,
            'CompanyName' => $contact->company,
            'Fields' => $contact->customFields ?: null,
        ], fn ($value) => $value !== null && $value !== '');
    }
}
