<?php namespace Groupeat\Orders\Presenters;

use Groupeat\Support\Presenters\Presenter;
use HtmlObject\Table;

class Order extends Presenter {

    public function presentHtmlTable()
    {
        $headers = ['quantité', 'pizza', 'taille', 'prix sans réduction'];

        foreach ($this->productFormats as $productFormat)
        {
            $rows[] = [
                $productFormat->pivot->amount,
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
