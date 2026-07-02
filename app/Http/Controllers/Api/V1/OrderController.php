<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Requests\Api\V1\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Order::class);

        $orders = Order::query()
            ->with(['customer', 'quote'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->status))
            ->when($request->filled('customer_id'), fn ($query) => $query->where('customer_id', $request->customer_id))
            ->latest('order_date')
            ->paginate($request->integer('per_page', 15));

        return OrderResource::collection($orders);
    }

    public function store(StoreOrderRequest $request): OrderResource
    {
        $this->authorize('create', Order::class);

        $customer = Customer::query()->findOrFail($request->validated('customer_id'));
        $order = $this->orderService->createOrder(
            $customer,
            $request->validated('items'),
            $request->safe()->except(['items', 'customer_id']),
        );

        return new OrderResource($order->load(['customer', 'orderItems']));
    }

    public function show(Order $order): OrderResource
    {
        $this->authorize('view', $order);

        return new OrderResource($order->load(['customer', 'orderItems', 'quote', 'invoices', 'shipments']));
    }

    public function update(UpdateOrderRequest $request, Order $order): OrderResource
    {
        $this->authorize('update', $order);

        $order = $this->orderService->updateOrder($order, $request->validated());

        return new OrderResource($order->load(['customer', 'orderItems']));
    }

    public function destroy(Order $order): JsonResponse
    {
        $this->authorize('delete', $order);

        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }
}
