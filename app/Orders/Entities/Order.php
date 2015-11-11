<?php
namespace Groupeat\Orders\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Support\Entities\Abstracts\ImmutableDatedEntity;
use SebastianBergmann\Money\EUR;
use SebastianBergmann\Money\Money;

class Order extends ImmutableDatedEntity
{
    public function getRules()
    {
        return [
            'customerId' => 'required',
            'groupOrderId' => 'required',
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
        return $this->belongsToMany(ProductFormat::class)->withPivot('quantity');
    }

    public function deliveryAddress()
    {
        return $this->hasOne(DeliveryAddress::class);
    }

    public function getDiscountedPriceAttribute()
    {
        return $this->isExternal() ? $this->rawPrice : $this->groupOrder->discountRate->applyTo($this->rawPrice);
    }

    public function isExternal()
    {
        return $this->customer->isExternal;
    }

    protected function getRawPriceAttribute()
    {
        return new EUR($this->attributes['rawPrice']);
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
