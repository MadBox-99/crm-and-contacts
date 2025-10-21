<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class CustomerController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Customer::class);

        $customers = Customer::query()
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', sprintf('%%%s%%', $request->search))
                ->orWhere('email', 'like', sprintf('%%%s%%', $request->search)))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->type))
            ->when($request->boolean('include_inactive'), fn ($query): Builder => $query, fn ($query) => $query->where('is_active', true))
            ->paginate($request->integer('per_page', 15));

        return CustomerResource::collection($customers);
    }

    public function store(Request $request): CustomerResource
    {
        $this->authorize('create', Customer::class);

        $validated = $request->validate([
            'unique_identifier' => ['required', 'string', 'unique:customers'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:individual,company'],
            'tax_number' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $customer = Customer::query()->create($validated);

        return new CustomerResource($customer);
    }

    public function show(Customer $customer): CustomerResource
    {
        $this->authorize('view', $customer);

        return new CustomerResource($customer);
    }

    public function update(Request $request, Customer $customer): CustomerResource
    {
        $this->authorize('update', $customer);

        $validated = $request->validate([
            'unique_identifier' => ['sometimes', 'string', 'unique:customers,unique_identifier,'.$customer->id],
            'name' => ['sometimes', 'string', 'max:255'],
            'type' => ['sometimes', 'in:individual,company'],
            'tax_number' => ['nullable', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $customer->update($validated);

        return new CustomerResource($customer->fresh());
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $this->authorize('delete', $customer);

        $customer->delete();

        return response()->json([
            'message' => 'Customer deleted successfully',
        ]);
    }
}
