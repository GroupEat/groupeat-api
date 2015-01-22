<?php namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Entity;

class ProductFormat extends Entity {

    public $timestamps = false;

    protected $fillable = ['product_id', 'name', 'price'];


    public function getRules()
    {
        return [
            'product_id' => 'required|integer',
            'name' => 'required',
            'price' => 'required|numeric',
        ];
    }

}
