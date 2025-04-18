<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'discount', 'type', 'max_usage', 'used', 'start_date', 'end_date'];

    // Kiểm tra voucher có còn hạn hay không
    public function isValid()
    {
        return $this->used < $this->max_usage &&
               (is_null($this->start_date) || now() >= $this->start_date) &&
               (is_null($this->end_date) || now() <= $this->end_date);
    }
    public function orders()
{
    return $this->hasMany(Order::class);
}
public function users()
{
    return $this->belongsToMany(User::class, 'user_voucher')->withTimestamps();
}



}
