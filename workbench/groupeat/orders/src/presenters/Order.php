<?php namespace Groupeat\Orders\Presenters;

use Groupeat\Support\Presenters\Presenter;
use HtmlObject\Table;

class Order extends Presenter {

    public function presentHtmlTable()
    {
        return (string) $this->getHtmlTable();
    }

    public function presentHtmlTableForEmail()
    {
        $table = (string) $this->getHtmlTable();

        $table = str_replace('<table>', '<table width="100%" style="border-collapse: collapse;">', $table);
        $table = str_replace('<thead>', '<thead style="border-bottom: 2px solid #a0a0a0;">', $table);
        $table = str_replace('<tr>', '<tr style="border-bottom: 1px solid #a0a0a0;">', $table);
        $table = str_replace('<th>', '<th style="text-align: center; padding: 6px;">', $table);
        $table = str_replace('<td>', '<td style="text-align: center; padding: 6px;">', $table);

        return $table;
    }

    public function presentRawPrice()
    {
        return $this->formatPriceWithCurrency($this->object->rawPrice);
    }

    protected function getHtmlTable()
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

}
