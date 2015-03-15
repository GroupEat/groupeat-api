<?php
namespace Groupeat\Orders\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Support\Entities\Entity;
use SebastianBergmann\Money\EUR;
use SebastianBergmann\Money\Money;

class Order extends Entity
{
    public $timestamps = false;

    protected $dates = ['created_at'];

    public function getRules()
    {
        return [
            'customer_id' => 'required|integer',
            'group_order_id' => 'required|integer',
            'rawPrice' => 'required|integer',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function groupOrder()
    {
        return $this->belongsTo(GroupOrder::class);
    }

    public function productFormats()
    {
        return $this->belongsToMany(ProductFormat::class)->withPivot('amount');
    }

    public function deliveryAddress()
    {
        return $this->hasOne(DeliveryAddress::class);
    }

    public function getDiscountedPriceAttribute()
    {
        return $this->groupOrder->discountRate->applyTo($this->rawPrice);
    }

    protected function getRawPriceAttribute()
    {
        return new EUR($this->attributes['rawPrice']); // TODO: Don't enforce a default currency
    }

    protected function setRawPriceAttribute(Money $rawPrice)
    {
        $this->attributes['rawPrice'] = $rawPrice->getAmount();
    }

    protected function setCommentAttribute($comment)
    {
        if (empty($comment)) {
            $this->attributes['comment'] = null;
        } else {
            $this->attributes['comment'] = str_limit($comment, 1000);
        }
    }
}
