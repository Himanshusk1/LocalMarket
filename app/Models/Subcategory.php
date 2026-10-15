<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
    ];

    /**
     * Parent category.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Listings under this subcategory.
     */
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
