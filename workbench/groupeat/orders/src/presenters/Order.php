<?php namespace Groupeat\Orders\Presenters;

use Groupeat\Support\Presenters\Presenter;
use HtmlObject\Table;

class Order extends Presenter {

    public function presentHtmlTable()
    {
        $attributes = trans('restaurants::products.attributes');

        foreach (['amount', 'foodType', 'product', 'format', 'rawPrice'] as $key)
        {
            $headers[] = mb_ucfirst($attributes[$key]);
        }

        foreach ($this->productFormats as $productFormat)
        {
            $rows[] = [
                $productFormat->pivot->amount,
                $productFormat->product->type->label,
                $productFormat->product->name,
                $productFormat->name,
                $productFormat->price
            ];
        }

        return Table::create($headers, $rows);
    }

    public function presentRawPrice()
    {
        return $this->formatPriceWithCurrency($this->object->rawPrice);
    }

}
