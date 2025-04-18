<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Tên bảng trong database
    protected $table = 'products';

    // Khóa chính
    protected $primaryKey = 'id';

    // Cho phép Laravel tự động thêm cột `created_at` và `updated_at`
    public $timestamps = true;

    // Các cột có thể điền dữ liệu (mass assignable)
    protected $fillable = [
        'product_name',
        'description',
        'image',
        'price',
        'discount_price',
        'category_id',
        'slug',
        'status',
        'detail',
        'featured'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    

    // Định nghĩa mối quan hệ với bảng Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id', 'id');
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    // Định nghĩa mối quan hệ với ProductSize
    public function sizes()
    {
        return $this->hasMany(ProductSize::class, 'product_id');
    }
}
