<?php namespace Groupeat\Support\Presenters;

use Carbon\Carbon;
use Robbo\Presenter\Presenter as BasePresenter;

class Presenter extends BasePresenter {

    public function __toString()
    {
        return $this->object->__toString();
    }

    public function presentReference()
    {
        return '('.trans('support::general.referenceAbbreviation', ['reference' => $this->object->id]).')';
    }

    protected function formatPrice($price, $decimalSeparator = ',', $thousandsSeparator = ' ')
    {
        return formatPrice($price, $decimalSeparator, $thousandsSeparator);
    }

    protected function formatPriceWithCurrency(
        $price,
        $decimalSeparator = ',',
        $thousandsSeparator = ' ',
        $currency = '€',
        $after = true
    )
    {
        return formatPriceWithCurrency($price, $decimalSeparator, $thousandsSeparator, $currency, $after);
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

    protected function formatTableForMail($table)
    {
        $table = (string) $table;

        $cellStyle = 'text-align: center; padding: 6px;';

        $styles = [
            'table' => 'border-collapse: collapse;',
            'thead' => 'border-bottom: 2px solid #a0a0a0;',
            'tr' => 'border-bottom: 1px solid #a0a0a0;',
            'th' => $cellStyle,
            'td' => $cellStyle,
        ];

        foreach ($styles as $tag => $style)
        {
            $table = str_replace("<$tag>", '<'.$tag.' style="'.$style.'">', $table);
        }

        return str_replace('<table style', '<table width="100%" style', $table);
    }

    protected function translate(array $keys, $translations, $ucfirst = false)
    {
        return array_map(function($key) use ($translations, $ucfirst)
        {
            return $ucfirst ? mb_ucfirst($translations[$key]) : $translations[$key];
        },
            $keys);
    }

}
