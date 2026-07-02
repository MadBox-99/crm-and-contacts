<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

final class AggregateModel implements Tool
{
    public function description(): string
    {
        return 'Perform aggregate operations (count, sum, avg, min, max) on a model. Useful for statistics and reports.';
    }

    public function handle(Request $request): string
    {
        $modelKey = (string) $request->string('model');
        $operation = (string) $request->string('operation');
        $column = (string) $request->string('column', 'id');
        $filters = $request['filters'] ?? [];
        $groupBy = $request['group_by'] ?? null;

        $models = ListModels::AVAILABLE_MODELS;

        if (! isset($models[$modelKey])) {
            return json_encode(['error' => sprintf("Unknown model '%s'.", $modelKey)], JSON_THROW_ON_ERROR);
        }

        $allowedOperations = ['count', 'sum', 'avg', 'min', 'max'];
        if (! in_array($operation, $allowedOperations, true)) {
            return json_encode(['error' => 'Invalid operation. Use one of: '.implode(', ', $allowedOperations)], JSON_THROW_ON_ERROR);
        }

        $modelClass = $models[$modelKey]['model'];

        /** @var Builder<Model> $query */
        $query = $modelClass::query();

        foreach ((array) $filters as $field => $value) {
            if (is_string($field) && $value !== null && $value !== '') {
                $query->where($field, $value);
            }
        }

        if ($groupBy !== null && $groupBy !== '') {
            $results = $query
                ->groupBy($groupBy)
                ->selectRaw(sprintf('%s, %s(%s) as result', $groupBy, $operation, $column))
                ->get()
                ->toArray();

            return json_encode([
                'model' => $modelKey,
                'operation' => $operation,
                'column' => $column,
                'grouped_by' => $groupBy,
                'data' => $results,
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        }

        $result = $query->{$operation}($column);

        return json_encode([
            'model' => $modelKey,
            'operation' => $operation,
            'column' => $column,
            'result' => $result,
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    public function schema(JsonSchema $schema): array
    {
        $modelKeys = array_keys(ListModels::AVAILABLE_MODELS);

        return [
            'model' => $schema->string()->description('The model key. One of: '.implode(', ', $modelKeys))->required(),
            'operation' => $schema->string()->description('Aggregate operation: count, sum, avg, min, max')->required(),
            'column' => $schema->string()->description('Column to aggregate (default: id for count)'),
            'filters' => $schema->object()->description('Key-value pairs to filter records'),
            'group_by' => $schema->string()->description('Column to group results by'),
        ];
    }
}
