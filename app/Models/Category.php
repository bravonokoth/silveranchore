<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['name', 'slug', 'description', 'image', 'parent_id'];

    // ✅ ADD THIS METHOD - Required for Spatie Media Library
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->singleFile(); // Only one image per category
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function getProductsCountAttribute()
    {
        return $this->products()->count();
    }
}