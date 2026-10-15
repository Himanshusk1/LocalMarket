<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
    ];

    /**
     * Get route key name for implicit binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Subcategories belonging to this category.
     */
    public function subcategories()
    {
        return $this->hasMany(Subcategory::class)->orderBy('name');
    }

    /**
     * Listings under this category.
     */
    public function listings()
    {
        return $this->hasMany(Listing::class);
    }
}
