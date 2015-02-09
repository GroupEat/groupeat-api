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

    public function presentSummaryForMail()
    {
        return $this->presentDetailsTableForMail()
            . '<br>'
            . $this->presentProductsTableForMail(false);
    }

    public function presentDetailsTable()
    {
        return $this->getDetailsTable();
    }

    public function presentDetailsTableForMail()
    {
        return $this->formatTableForMail($this->getDetailsTable());
    }

    public function presentRawPrice()
    {
        return $this->formatPriceWithCurrency($this->object->rawPrice);
    }

    public function presentReducedPrice()
    {
        return $this->formatPriceWithCurrency($this->object->reducedPrice);
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
