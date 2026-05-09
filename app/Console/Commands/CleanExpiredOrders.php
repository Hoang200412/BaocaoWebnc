<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Laravel\Prompts\info;
use Laravel\Schedule\Attributes\AsSchedule;

#[AsSchedule('everyFifteenMinutes')] // ← Gắn lịch tại đây
class CleanExpiredOrders extends Command
{
    protected $signature = 'orders:clean-expired';
    protected $description = 'Xoá các đơn hàng chưa thanh toán sau 15 phút';

    public function handle(): void  
    {
        $expiredOrders = Order::where('payment_status', Order::PAYMENT_STATUS_PENDING)
            ->where('created_at', '<', now()->subMinutes(15))
            ->get();

        foreach ($expiredOrders as $order) {
            $order->items()->delete();
            $order->delete();
        }

        info("Đã xoá {$expiredOrders->count()} đơn hàng hết hạn.");
    }
}
