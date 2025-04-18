<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{  public $timestamps = false; 
    use HasFactory;

    // Đặt tên bảng, nếu tên bảng trong cơ sở dữ liệu không phải là dạng số nhiều của tên model
    protected $table = 'product_sizes';
    protected $primaryKey = 'product_size_id';


    // Đặt các thuộc tính có thể điền vào
    protected $fillable = [
        'product_id',
        'size',
        'quantity',
    ];

    // Định nghĩa mối quan hệ với Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
