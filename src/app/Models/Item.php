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

    public function categories()
    {
        // 中間テーブル名を category_items に指定
        return $this->belongsToMany(Category::class, 'category_items');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        // likesテーブルとの1対多のリレーション
        return $this->hasMany(Like::class);
    }

    // すでに「いいね」しているか判定
    public function isLikedBy($user): bool
    {
        if (!$user) return false;
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
