<?php

/*
 * This file is part of the IfmbBundle package.
 *
 * (c) Samuel Gordalina <https://github.com/gordalina/IfmbBundle>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gordalina\Bundle\IfmbBundle\Service;

class RefMb
{
    /**
     * @param  mixed $entity
     * @param  mixed $subEntity
     * @param  mixed $order
     * @param  mixed $value
     * @return array
     */
    public function generate($entity, $subEntity, $order, $value)
    {
        $entity = (string) $entity;

        if (strlen($entity) !== 5) {
            throw new \InvalidArgumentException(sprintf('Entity "%s" must be 5 digits in length', $entity));
        }

        if (strlen($subEntity) !== 3) {
            throw new \InvalidArgumentException(sprintf('Sub-entity "%s" must be 3 digits in length', $subEntity));
        }

        $order = sprintf("0000%s", $order);
        $value = sprintf("%01.2f", $value);
        $value = $this->formatNumber($value);

        if ($value < 1) {
            throw new \InvalidArgumentException(sprintf('Value "%s" must be at least 1,00 EUR', $value));
        } elseif ($value > 999999.99) {
            throw new \InvalidArgumentException(sprintf('Value "%s" must be at most 999 999,99 EUR', $value));
        }

        $checksumStr = '';

        if (strlen($subEntity) === 1) {
            $order = substr($order, (strlen($order) - 6), strlen($order));
            $checksumStr = sprintf('%05u%01u%06u%08u', $entity, $subEntity, $order, round($value*100));
        } elseif (strlen($subEntity) == 2) {
            $order = substr($order, (strlen($order) - 5), strlen($order));
            $checksumStr = sprintf('%05u%02u%05u%08u', $entity, $subEntity, $order, round($value*100));
        } else {
            $order = substr($order, (strlen($order) - 4), strlen($order));
            $checksumStr = sprintf('%05u%03u%04u%08u', $entity, $subEntity, $order, round($value*100));
        }

        $checksum = 0;
        $checksumMatrix = array(3, 30, 9, 90, 27, 76, 81, 34, 49, 5, 50, 15, 53, 45, 62, 38, 89, 17, 73, 51);

        for ($i = 0; $i < 20; $i++) {
            $digest = substr($checksumStr, 19-$i, 1);
            $checksum += ($digest%10)*$checksumMatrix[$i];
        }

        $checksum %= 97;
        $checksumDigits = sprintf('%02u', 98-$checksum);

        $reference = sprintf(
            '%s %s %s%s',
            substr($checksumStr, 5, 3),
            substr($checksumStr, 8, 3),
            substr($checksumStr, 11, 1),
            $checksumDigits
        );

        $valueStr = number_format($value, 2, ',', ' ');

        return array(
            'entity' => $entity,
            'reference' => $reference,
            'value' => $valueStr
        );
    }

    /**
     * From Ifthensoftware manual
     *
     * @param  integer $number
     * @return string
     */
    private function formatNumber($number)
    {
        $verifySepDecimal = number_format(99, 2);

        $valorTmp = $number;

        $sepDecimal = substr($verifySepDecimal, 2, 1);

        $hasSepDecimal = true;

        $i=(strlen($valorTmp)-1);

        for ($i; $i!=0; $i-=1) {
            if (substr($valorTmp, $i, 1) == "." || substr($valorTmp, $i, 1) == ",") {
                $hasSepDecimal = true;
                $valorTmp = trim(substr($valorTmp, 0, $i)) . "@" . trim(substr($valorTmp, 1+$i));
                break;
            }
        }

        if ($hasSepDecimal != true) {
            $valorTmp = number_format($valorTmp, 2);

            $i=(strlen($valorTmp)-1);

            for ($i; $i!=1; $i--) {
                if (substr($valorTmp, $i, 1) == "." || substr($valorTmp, $i, 1) == ",") {
                    $hasSepDecimal = true;
                    $valorTmp = trim(substr($valorTmp, 0, $i)) ."@". trim(substr($valorTmp, 1+$i));
                    break;
                }
            }
        }

        for ($i=1; $i!=(strlen($valorTmp)-1); $i++) {
            if (substr($valorTmp, $i, 1) == "." || substr($valorTmp, $i, 1) == "," || substr($valorTmp, $i, 1) == " ") {
                $valorTmp = trim(substr($valorTmp, 0, $i)).trim(substr($valorTmp, 1+$i));
                break;
            }
        }

        if (strlen(strstr($valorTmp, '@')) > 0) {
            $valorTmp = trim(substr($valorTmp, 0, strpos($valorTmp, '@'))).trim($sepDecimal).trim(substr($valorTmp, strpos($valorTmp, '@')+1));
        }

        return $valorTmp;
    }
}
