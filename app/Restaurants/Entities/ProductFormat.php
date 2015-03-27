<?php
namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Abstracts\Entity;
use SebastianBergmann\Money\EUR;

class ProductFormat extends Entity
{
    public $timestamps = false;

    protected $fillable = ['productId', 'name', 'price'];

    public function getRules()
    {
        return [
            'productId' => 'required',
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
