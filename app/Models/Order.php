<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const STATUS_PENDING = 'Chờ duyệt';
    public const STATUS_APPROVED = 'Đã duyệt';
    public const STATUS_SHIPPING = 'Chờ giao hàng';
    public const STATUS_DELIVERED = 'Đã nhận hàng';
    public const STATUS_CANCELED = 'Đã hủy';

    public const PAYMENT_STATUS_PENDING = 'Chưa thanh toán';
    public const PAYMENT_STATUS_SUCCESS = 'Thanh toán thành công';
    public const PAYMENT_STATUS_FAILED = 'Thanh toán thất bại';

    public const PAYMENT_METHOD_COD = 'cod';
    public const PAYMENT_METHOD_VNPAY = 'vnpay';

    public const STATUS_OPTIONS = [
        self::STATUS_PENDING,
        self::STATUS_APPROVED,
        self::STATUS_SHIPPING,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELED,
    ];

    public const PAYMENT_STATUS_OPTIONS = [
        self::PAYMENT_STATUS_PENDING,
        self::PAYMENT_STATUS_SUCCESS,
        self::PAYMENT_STATUS_FAILED,
    ];

    public const PAYMENT_METHOD_LABELS = [
        self::PAYMENT_METHOD_COD => 'COD',
        self::PAYMENT_METHOD_VNPAY => 'VNPAY',
    ];

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
