<?php
namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Entity;
use SebastianBergmann\Money\EUR;

class ProductFormat extends Entity
{
    public $timestamps = false;

    protected $fillable = ['product_id', 'name', 'price'];

    public function getRules()
    {
        return [
            'product_id' => 'required|integer',
            'name' => 'required',
            'price' => 'required|integer',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected function getPriceAttribute()
    {
        return new EUR($this->attributes['price']);
    }
}
