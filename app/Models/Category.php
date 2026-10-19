<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Category extends Model
{
    protected $fillable = ['name','slug','description'];

    // auto geberate slug saat create/updatee

    protected static function booted(): void
    {
        static::saving(function ($category){
            if(empty($category->slug)){
                $category->slug = Str::slug($category->name);
            }
        });
    }
    public function products ()
    {
        return $this->hasMany(Product::class);
    }
}
