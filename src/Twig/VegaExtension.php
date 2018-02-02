<?php


namespace Vega\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class VegaExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('nice_number', [$this, 'niceNumberFilter'])
        ];
    }

    public function niceNumberFilter($number)
    {
        if ($number > 1000000000) {
            $number = round($number / 1000000000, 1) . 'b';
        } elseif ($number > 1000000) {
            $number = round($number / 1000000, 1) . 'm';
        } elseif ($number > 1000) {
            $number = round($number / 1000, 1) . 'k';
        }

        return $number;
    }
}
