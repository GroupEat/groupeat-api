<?php namespace Groupeat\Support\Presenters;

use Carbon\Carbon;
use Robbo\Presenter\Presenter as BasePresenter;

class Presenter extends BasePresenter {

    public function __toString()
    {
        return $this->object->__toString();
    }

    protected function formatPrice($price, $decimalSeparator = ',', $thousandsSeparator = ' ')
    {
        return number_format($price, 2, $decimalSeparator, $thousandsSeparator);
    }

    protected function formatPriceWithCurrency(
        $price,
        $decimalSeparator = ',',
        $thousandsSeparator = ' ',
        $currency = '€',
        $after = true
    )
    {
        $formattedPrice = $this->formatPrice($price, $decimalSeparator, $thousandsSeparator);

        return $after ? $formattedPrice.' '.$currency : $currency.' '.$formattedPrice;
    }

    protected function formatTime(Carbon $time, $hoursSuffix = '\h', $withSeconds = false)
    {
        $format = 'H'.$hoursSuffix.'i';

        if ($withSeconds)
        {
            $format .= ':s';
        }

        return $time->format($format);
    }

}
