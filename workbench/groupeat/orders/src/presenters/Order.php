<?php namespace Groupeat\Orders\Presenters;

use Groupeat\Support\Presenters\Presenter;
use HtmlObject\Table;

class Order extends Presenter {

    public function presentProductsTable($withRawPrice = true)
    {
        return (string) $this->getProductsTable($withRawPrice);
    }

    public function presentProductsTableForMail($withRawPrice = true)
    {
        return $this->formatTableForMail($this->getProductsTable($withRawPrice));
    }

    public function presentProductsListAsPlainText($withRawPrice = true)
    {
        $str = '';

        foreach ($this->productFormats as $productFormat)
        {
            $str .= $productFormat->product->type->label
                . ' '.$productFormat->product->name.' ';

            if ($withRawPrice)
            {
                $str .= '('.$productFormat->price.') ';
            }

            $str .= '-> '.$productFormat->pivot->amount.', ';
        }

        return trim($str, ', ');
    }

    public function presentSummaryForMail()
    {
        return $this->presentDetailsTableForMail()
            . '<br>'
            . $this->presentProductsTableForMail(false);
    }

    public function presentSummaryAsPlainText()
    {
        return $this->presentDetailsAsPlainText()
            . '; '
            . $this->presentProductsListAsPlainText(false);
    }

    public function presentDetailsTable()
    {
        return $this->getDetailsTable();
    }

    public function presentDetailsTableForMail()
    {
        return $this->formatTableForMail($this->getDetailsTable());
    }

    public function presentDetailsAsPlainText()
    {
        $attributes = trans('orders::orders.attributes');

        return $this->presentReference()
            . ', '.mb_ucfirst($attributes['customer']).': '.$this->customer->fullNameWithPhoneNumber
            . ', '.mb_ucfirst($attributes['deliveryAddress']).': '.$this->deliveryAddress
            . ', '.mb_ucfirst($attributes['priceToPay']).': '.$this->presentReducedPrice();
    }

    public function presentRawPrice()
    {
        return $this->formatPrice($this->object->rawPrice);
    }

    public function presentReducedPrice()
    {
        return $this->formatPrice($this->object->discountedPrice);
    }

    private function getProductsTable($withRawPrice = true)
    {
        $keys = ['amount', 'foodType', 'product', 'format'];

        if ($withRawPrice)
        {
            $keys[] = 'rawPrice';
        }

        $headers = $this->translate($keys, trans('restaurants::products.attributes'), true);

        foreach ($this->productFormats as $productFormat)
        {
            $details = [
                $productFormat->pivot->amount,
                $productFormat->product->type->label,
                $productFormat->product->name,
                $productFormat->name
            ];

            if ($withRawPrice)
            {
                $details[] = $productFormat->price;
            }

            $rows[] = $details;
        }

        return Table::create($headers, $rows);
    }

    private function getDetailsTable()
    {
        $keys = ['reference', 'customer', 'deliveryAddress', 'priceToPay'];

        $headers = $this->translate($keys, trans('orders::orders.attributes'), true);

        $rows[] = [
            $this->id,
            $this->customer->fullNameWithPhoneNumber,
            (string) $this->deliveryAddress,
            $this->presentReducedPrice(),
        ];

        return Table::create($headers, $rows);
    }

}
