<?php
namespace Groupeat\Orders\Presenters;

use Groupeat\Support\Presenters\Presenter;
use HtmlObject\Table;

class GroupOrderPresenter extends Presenter
{
    public function presentCreationTime()
    {
        return $this->formatTime($this->createdAt);
    }

    public function presentEndingTime()
    {
        return $this->formatTime($this->endingAt);
    }

    public function presentClosedAtTime()
    {
        return $this->formatTime($this->closedAt);
    }

    public function presentPreparedAtTime()
    {
        return $this->formatTime($this->preparedAt);
    }

    public function presentDiscountRate()
    {
        return $this->object->discountRate->toPercentage().'%';
    }

    public function presentProductsTable()
    {
        return (string) $this->getProductsTable();
    }

    public function presentProductsTableForMail()
    {
        return $this->formatTableForMail($this->getProductsTable());
    }

    protected function getProductsTable()
    {
        $keys = ['quantity', 'foodType', 'product', 'format'];
        $headers = $this->translate($keys, trans('restaurants::products.attributes'), true);

        $quantities = [];
        $productFormats = [];

        foreach ($this->orders as $order) {
            foreach ($order->productFormats as $productFormat) {
                if (empty($productFormats[$productFormat->id])) {
                    $productFormats[$productFormat->id] = $productFormat;
                    $quantities[$productFormat->id] = 0;
                }

                $quantities[$productFormat->id] += $productFormat->pivot->quantity;
            }
        }

        foreach ($productFormats as $id => $productFormat) {
            $rows[] = [
                $quantities[$id],
                $productFormat->product->type->label,
                $productFormat->product->name,
                $productFormat->name,
            ];
        }

        return Table::create($headers, $rows);
    }
}
