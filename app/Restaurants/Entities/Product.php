<?php
namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Abstracts\Entity;

class Product extends Entity
{
    public $timestamps = false;

    protected $fillable = ['typeId', 'name', 'description'];

    public function getRules()
    {
        return [
            'restaurantId' => 'required',
            'typeId' => 'required',
            'name' => 'required|max:40',
            'description' => 'required|max:255',
        ];
    }

    public function type()
    {
        return $this->belongsTo(FoodType::class);
    }

    public function formats()
    {
        return $this->hasMany(ProductFormat::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
