<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'image_path',
        'condition',
        'name',
        'brand_name',
        'description',
        'price',
    ];

    // 中間テーブルcategory_itemのリレーション
    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    // Usersテーブルとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Commentsテーブルとのリレーション
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // likesテーブルとのリレーション
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // すでに「いいね」しているか判定
    public function isLikedBy($user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }

    // ItemsテーブルとSoldItemsテーブルのリレーション
    public function soldItem()
    {
        return $this->hasOne(SoldItem::class, 'item_id');
    }

    // 商品が売り切れかどうかを判定するメソッド
    public function isSold()
    {
        return $this->soldItem && $this->soldItem->status === 'paid';
    }

    // 商品名で部分一致検索
    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('items.name', 'like', '%' . $keyword . '%');
        }
        return $query;
    }
}
