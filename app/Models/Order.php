<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ten_khach_hang',
        'so_dien_thoai',
        'dia_chi',
        'tong_tien',
        'phuong_thuc_thanh_toan',
        'trang_thai',
        'payment_status',
        'voucher_id'
    ];

    // Quan hệ với bảng OrderItem
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function user()
{
    return $this->belongsTo(User::class);
}
public function voucher()
{
    return $this->belongsTo(Voucher::class);
}

}
