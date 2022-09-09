<?php
declare(strict_types=1);

namespace Brodaty\Application;

use DOMDocument;

class SalaryHelper
{
    public static function getSalaryFromRSSOffer(\Laminas\Feed\Reader\Entry\Atom $rssOffer)
    {
        $content = $rssOffer->getContent();
        $salary = self::getBetween($content, "Salary:</b> ", "<br");

        return $salary[0];
    }

    static function getBetween($content, $start, $end): array
    {
        $n = explode($start, $content);
        $result = Array();
        foreach ($n as $val) {
            $pos = strpos($val, $end);
            if ($pos !== false) {
                $result[] = substr($val, 0, $pos);
            }
        }
        return $result;
    }
}