<?php
namespace Groupeat\Orders\Presenters;

use Groupeat\Support\Presenters\Presenter;
use HtmlObject\Table;

class OrderPresenter extends Presenter
{
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

        foreach ($this->productFormats as $productFormat) {
            $str .= $productFormat->product->type->label
                .' '.$productFormat->product->name.' ';

            if ($withRawPrice) {
                $str .= '('.$productFormat->price.') ';
            }

            $str .= '-> '.$productFormat->pivot->quantity.', ';
        }

        return trim($str, ', ');
    }

    public function presentSummaryForMail()
    {
        return $this->presentDetailsTableForMail()
            .'<br>'
            .$this->presentProductsTableForMail(false);
    }

    public function presentSummaryAsPlainText()
    {
        return $this->presentDetailsAsPlainText()
            .'; '
            .$this->presentProductsListAsPlainText(false);
    }

    public function presentDetailsTable()
    {
        return $this->getDetailsTable();
    }

    public function presentDetailsTableForMail()
    {
        return $this->formatTableForMail($this->getDetailsTable());
    }

    public function presentDetailsAsPlainText($withCustomer = true)
    {
        $attributes = trans('orders::orders.attributes');

        $str = $this->presentReference();

        if ($withCustomer) {
            $str .= ', '.mb_ucfirst($attributes['customer']).': '.$this->customer->phoneNumber;
        }

        $str .= ', '.mb_ucfirst($attributes['deliveryAddress']).': '.$this->deliveryAddress
            .', '.mb_ucfirst($attributes['priceToPay']).': '.$this->presentDiscountedPrice();

        return $str;
    }

    public function presentDetailsTableForCustomer()
    {
        return $this->getDetailsTable(false);
    }

    public function presentDetailsTableForCustomerForMail()
    {
        return $this->formatTableForMail($this->getDetailsTable(false));
    }

    public function presentDetailsTableForCustomerAsPlainText()
    {
        return $this->presentDetailsAsPlainText(false);
    }

    public function presentRawPrice()
    {
        return $this->formatPrice($this->object->rawPrice);
    }

    public function presentDiscountedPrice()
    {
        return $this->formatPrice($this->object->discountedPrice);
    }

    private function getProductsTable($withRawPrice = true)
    {
        $keys = ['quantity', 'foodType', 'product', 'format'];

        if ($withRawPrice) {
            $keys[] = 'rawPrice';
        }

        $headers = $this->translate($keys, trans('restaurants::products.attributes'), true);

        foreach ($this->productFormats as $productFormat) {
            $details = [
                $productFormat->pivot->quantity,
                $productFormat->product->type->label,
                $productFormat->product->name,
                $productFormat->name,
            ];

            if ($withRawPrice) {
                $details[] = $productFormat->price;
            }

            $rows[] = $details;
        }

        return Table::create($headers, $rows);
    }

    private function getDetailsTable($withCustomer = true)
    {
        $keys[] = 'reference';

        if ($withCustomer) {
            $keys[] = 'customer';
        }

        $keys[] = 'deliveryAddress';
        $keys[] = 'priceToPay';

        $headers = $this->translate($keys, trans('orders::orders.attributes'), true);

        $row[] = $this->id;

        if ($withCustomer) {
            $row[] = $this->customer->phoneNumber;
        }

        $row[] = (string) $this->deliveryAddress;
        $row[] = $this->presentDiscountedPrice();

        return Table::create($headers, [$row]);
    }
}
