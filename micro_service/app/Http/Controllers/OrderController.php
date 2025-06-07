<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderCreateRequest;
use App\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): JsonResponse
    {
        $orders = Order::where('user_id', $request->get('user_id'))->get();

        return response()->json(['orders' => $orders], Response::HTTP_OK);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json(['order' => $order], Response::HTTP_OK);
    }


    public function store(OrderCreateRequest $request): JsonResponse
    {
        $dataNewOrder = $request->validated();
        $order = Order::create($dataNewOrder);

        return response()->json(['order' => $order,], Response::HTTP_CREATED);
    }

    public function destroy(Order $order): JsonResponse
    {
        $order->delete();

        return response()->json(['message' => 'Заказ успешно удалён'], Response::HTTP_OK);
    }
}
