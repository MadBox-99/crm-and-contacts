<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

final class GetModelDetails implements Tool
{
    public function description(): string
    {
        return 'Get detailed information about a specific record by its ID, including requested relationships. Use list_models first to see available models and their relationships.';
    }

    public function handle(Request $request): string
    {
        $modelKey = (string) $request->string('model');
        $id = $request->integer('id');
        $relationships = $request['with'] ?? [];

        $models = ListModels::AVAILABLE_MODELS;

        if (! isset($models[$modelKey])) {
            return json_encode(['error' => sprintf("Unknown model '%s'. Use list_models to see available models.", $modelKey)], JSON_THROW_ON_ERROR);
        }

        $config = $models[$modelKey];
        $modelClass = $config['model'];
        $allowedRelationships = $config['relationships'];

        $validRelationships = array_intersect((array) $relationships, $allowedRelationships);

        $record = $modelClass::with($validRelationships)->find($id);

        if ($record === null) {
            return json_encode(['error' => sprintf('Record not found: %s #%d', $modelKey, $id)], JSON_THROW_ON_ERROR);
        }

        return json_encode([
            'model' => $modelKey,
            'data' => $record->toArray(),
        ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    }

    public function schema(JsonSchema $schema): array
    {
        $modelKeys = array_keys(ListModels::AVAILABLE_MODELS);

        return [
            'model' => $schema->string()->description('The model key. One of: '.implode(', ', $modelKeys))->required(),
            'id' => $schema->integer()->description('The record ID to retrieve')->required(),
            'with' => $schema->array()->items($schema->string())->description('Relationships to include'),
        ];
    }
}
