<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreComplaintRequest;
use App\Http\Requests\Api\V1\UpdateComplaintRequest;
use App\Http\Resources\ComplaintResource;
use App\Models\Complaint;
use App\Models\Customer;
use App\Services\ComplaintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ComplaintController extends Controller
{
    public function __construct(
        private readonly ComplaintService $complaintService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Complaint::class);

        $complaints = Complaint::query()
            ->with(['customer', 'order', 'assignedUser'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->type))
            ->when($request->filled('customer_id'), fn ($query) => $query->where('customer_id', $request->customer_id))
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return ComplaintResource::collection($complaints);
    }

    public function store(StoreComplaintRequest $request): ComplaintResource
    {
        $this->authorize('create', Complaint::class);

        $customer = Customer::query()->findOrFail($request->validated('customer_id'));
        $complaint = $this->complaintService->createComplaint($customer, $request->validated());

        return new ComplaintResource($complaint->load(['customer', 'order']));
    }

    public function show(Complaint $complaint): ComplaintResource
    {
        $this->authorize('view', $complaint);

        return new ComplaintResource($complaint->load(['customer', 'order', 'assignedUser', 'reporter', 'escalations']));
    }

    public function update(UpdateComplaintRequest $request, Complaint $complaint): ComplaintResource
    {
        $this->authorize('update', $complaint);

        $complaint->update($request->validated());

        return new ComplaintResource($complaint->fresh()->load(['customer', 'order']));
    }

    public function destroy(Complaint $complaint): JsonResponse
    {
        $this->authorize('delete', $complaint);

        $complaint->delete();

        return response()->json(['message' => 'Complaint deleted successfully']);
    }
}
