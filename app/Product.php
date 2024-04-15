<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'price',
        'name',
        'description',
    ];

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }
}
