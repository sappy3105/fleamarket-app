<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'payment_method',
        'stripe_checkout_id',
    ];

    // 商品へのリレーション
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
