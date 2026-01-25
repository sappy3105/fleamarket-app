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

    // 中間テーブル名を category_items に指定
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_items');
    }

    // Usersテーブルとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Commentsテーブルとのリレーション(1対多)
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // likesテーブルとの1対多のリレーション
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

    // 商品名で部分一致検索
    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('items.name', 'like', '%' . $keyword . '%');
        }
        return $query;
    }
}
