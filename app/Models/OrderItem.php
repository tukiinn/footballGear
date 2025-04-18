<?php
namespace App\Models;

use Faker\Calculator\Ean;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
   
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'gia',
        'so_luong',
        'thanh_tien',
        'size'
    ];

    // Quan hệ với bảng Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Quan hệ với bảng DienThoai
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
