<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    /**
     */
    protected $fillable = [
        'unit_code',
        'name',
        'description',
        'stock',
        'status',
        'price_per_day',
    ];

    /**
     * Nilai default untuk atribut.
     */
    protected $attributes = [
        'status' => 'available',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_unit');
    }

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }
}
