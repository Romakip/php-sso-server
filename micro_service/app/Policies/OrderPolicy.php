<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(?User $user, Order $order): bool
    {
        return request()->get('user_id') === $order->user_id;
    }

    public function delete(?User $user, Order $order): bool
    {
        return request()->get('user_id') === $order->user_id;
    }
}
