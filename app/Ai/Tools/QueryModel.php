<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Campaign;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Interaction;
use App\Models\Invoice;
use App\Models\Opportunity;
use App\Models\Order;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Task;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

final class QueryModel implements Tool
{
    public function description(): string
    {
        return 'Query a database model with optional filters, sorting, and relationship loading. Returns a paginated list of records. Use list_models first to see available models.';
    }

    public function handle(Request $request): string
    {
        $modelKey = (string) $request->string('model');
        $filters = $request['filters'] ?? [];
        $relationships = $request['with'] ?? [];
        $sortBy = (string) $request->string('sort_by', 'id');
        $sortDirection = (string) $request->string('sort_direction', 'desc');
        $limit = min($request->integer('limit', 10), 50);
        $search = $request['search'] ?? null;

        $models = ListModels::AVAILABLE_MODELS;

        if (! isset($models[$modelKey])) {
            return json_encode(['error' => sprintf("Unknown model '%s'. Use list_models to see available models.", $modelKey)], JSON_THROW_ON_ERROR);
        }

        $config = $models[$modelKey];
        $modelClass = $config['model'];
        $allowedRelationships = $config['relationships'];

        /** @var Builder<Model> $query */
        $query = $modelClass::query();

        $validRelationships = array_intersect((array) $relationships, $allowedRelationships);
        if ($validRelationships !== []) {
            $query->with($validRelationships);
        }

        foreach ((array) $filters as $field => $value) {
            if (is_string($field) && $value !== null && $value !== '') {
                $query->where($field, $value);
            }
        }

        if ($search !== null && $search !== '') {
            $query->where(function (Builder $q) use ($search, $modelClass): void {
                $searchable = $this->getSearchableColumns($modelClass);
                foreach ($searchable as $column) {
                    $q->orWhere($column, 'like', sprintf('%%%s%%', $search));
                }
            });
        }

        $query->orderBy($sortBy, $sortDirection);

        $results = $query->limit($limit)->get();

        return json_encode([
            'model' => $modelKey,
            'count' => $results->count(),
            'data' => $results->toArray(),
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    public function schema(JsonSchema $schema): array
    {
        $modelKeys = array_keys(ListModels::AVAILABLE_MODELS);

        return [
            'model' => $schema->string()->description('The model key to query. One of: '.implode(', ', $modelKeys))->required(),
            'filters' => $schema->object()->description('Key-value pairs to filter by exact match (e.g. {"status": "active", "customer_id": 5})'),
            'with' => $schema->array()->items($schema->string())->description('Relationships to eager load'),
            'search' => $schema->string()->description('Search term to filter records by name, title, or similar fields'),
            'sort_by' => $schema->string()->description('Column to sort by (default: id)'),
            'sort_direction' => $schema->string()->description('Sort direction: asc or desc (default: desc)'),
            'limit' => $schema->integer()->description('Number of records to return (max 50, default 10)'),
        ];
    }

    /**
     * @return list<string>
     */
    private function getSearchableColumns(string $modelClass): array
    {
        return match ($modelClass) {
            Customer::class => ['name', 'email', 'phone', 'tax_number'],
            Order::class => ['order_number'],
            Invoice::class => ['invoice_number'],
            Product::class => ['name', 'sku', 'description'],
            Opportunity::class => ['title', 'description'],
            Complaint::class => ['subject', 'description'],
            Campaign::class => ['name'],
            Quote::class => ['quote_number'],
            Task::class => ['title', 'description'],
            Interaction::class => ['subject', 'notes'],
            default => ['name'],
        };
    }
}
