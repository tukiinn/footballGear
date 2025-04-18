<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Category extends Eloquent

{
    protected $connection = null;
    use HasFactory;

    // Tên bảng trong database
    protected $table = 'categories';

    // Khóa chính
    protected $primaryKey = 'id';

    // Cho phép Laravel tự động thêm cột `created_at` và `updated_at`
    public $timestamps = true;

    // Các cột có thể điền dữ liệu (mass assignable)
    protected $fillable = [
        'category_name',
        'description',
        'image',
        'slug',
        'status',
        'sort_order',
        'parent_id',
    ];

    /**
     * Quan hệ với danh mục con (nhiều con - one-to-many)
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Quan hệ với danh mục cha (một cha - belongs to)
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Lấy các sản phẩm thuộc danh mục
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function subCategories()
    {
        return $this->hasMany(Category::class, 'parent_id'); // Giả sử trường 'parent_id' là trường lưu danh mục cha
    }

    // Phương thức kiểm tra xem danh mục có danh mục con hay không
    public function hasSubCategories()
    {
        return $this->subCategories()->exists();
    }
}
