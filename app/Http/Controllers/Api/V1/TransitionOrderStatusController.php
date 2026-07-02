<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

final class TransitionOrderStatusController extends Controller
{
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    public function __invoke(Request $request, Order $order): OrderResource|JsonResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(OrderStatus::class)],
        ]);

        try {
            $order = $this->orderService->transitionStatus(
                $order,
                OrderStatus::from($validated['status']),
            );

            return new OrderResource($order);
        } catch (InvalidArgumentException $invalidArgumentException) {
            return response()->json(['message' => $invalidArgumentException->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
