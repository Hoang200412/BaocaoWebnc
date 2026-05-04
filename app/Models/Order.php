<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'status',
        'payment_method',
        'payment_status',
        'total_price',
        'shipping_fee',
        'expired_at',
    ];

    // Một đơn hàng có nhiều sản phẩm (order items)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Nếu có liên kết với người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
