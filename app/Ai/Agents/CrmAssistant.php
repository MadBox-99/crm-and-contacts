<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Tools\AggregateModel;
use App\Ai\Tools\GetModelDetails;
use App\Ai\Tools\ListModels;
use App\Ai\Tools\QueryModel;
use Laravel\Ai\Attributes\MaxSteps;
use Laravel\Ai\Attributes\MaxTokens;
use Laravel\Ai\Attributes\Model;
use Laravel\Ai\Attributes\Provider;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Promptable;

#[Provider(Lab::Gemini)]
#[Model('gemini-2.5-flash')]
#[MaxSteps(10)]
#[MaxTokens(4096)]
#[Temperature(0.7)]
#[Timeout(120)]
final class CrmAssistant implements Agent, Conversational, HasTools
{
    use Promptable;
    use RemembersConversations;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): string
    {
        return <<<'INSTRUCTIONS'
        You are a CRM assistant for a Sales and Customer Relationship Management system.
        You help team members by answering questions about customers, orders, invoices, products, opportunities, and other business data.

        You have READ-ONLY access to the database through tools. You CANNOT create, update, or delete any records.

        Guidelines:
        - Always start by using list_models to understand what data is available if you're unsure.
        - Use query_model to search and list records with filters.
        - Use get_model_details to get detailed information about a specific record with its relationships.
        - Use aggregate_model for statistics (counts, sums, averages).
        - Present data in a clear, readable format. Use tables when listing multiple records.
        - When presenting monetary values, format them properly with currency.
        - Be concise but thorough in your answers.
        - If asked to modify data, politely explain that you only have read access.
        - Communicate in the same language as the user's message.
        INSTRUCTIONS;
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new ListModels(),
            new QueryModel(),
            new GetModelDetails(),
            new AggregateModel(),
        ];
    }
}
