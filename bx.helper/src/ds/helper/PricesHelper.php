<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */


class PricesHelper
{

    /**
     * форматирование цены
     * пример вызова formatCurrency(11800.95, "RUR");
     * @param $fSum
     * @param $strCurrency
     * @return mixed|string
     */
    public static function formatCurrency($fSum, $strCurrency)
    {
        if (!isset($fSum) || strlen($fSum) <= 0)
            return "";

        $arCurFormat = CCurrencyLang::GetCurrencyFormat($strCurrency);


        if (!isset($arCurFormat["DECIMALS"]))
            $arCurFormat["DECIMALS"] = 2;

        $arCurFormat["DECIMALS"] = IntVal($arCurFormat["DECIMALS"]);

        if (!isset($arCurFormat["DEC_POINT"]))
            $arCurFormat["DEC_POINT"] = ".";

        if (!isset($arCurFormat["THOUSANDS_SEP"]))
            $arCurFormat["THOUSANDS_SEP"] = "\\" . "xA0";

        $tmpTHOUSANDS_SEP = $arCurFormat["THOUSANDS_SEP"];
        eval("\$tmpTHOUSANDS_SEP = \"$tmpTHOUSANDS_SEP\";");
        $arCurFormat["THOUSANDS_SEP"] = $tmpTHOUSANDS_SEP;

        if (!isset($arCurFormat["FORMAT_STRING"]))
            $arCurFormat["FORMAT_STRING"] = "#";

        $num = number_format($fSum,
            $arCurFormat["DECIMALS"],
            $arCurFormat["DEC_POINT"],
            $arCurFormat["THOUSANDS_SEP"]);

        return rtrim(rtrim(str_replace("#",
            $num,
            $arCurFormat["FORMAT_STRING"]), '0'), '.');
    }


    /**
     * вычисление скидки
     * @param $actionPrice
     * @param $oldPrice
     * @return mixed
     */
    public static function getSaleValue($actionPrice, $oldPrice)
    {


        //вычислить разницу между старой и акционной ценой в рублях
        $DISCOUNT_DIFF = $oldPrice - $actionPrice;


        //разница между старой и акционной ценой в процентах
        $DISCOUNT_DIFF_PERCENT = round((($oldPrice - $actionPrice) / $oldPrice) * 100);

        $arRes["DISCOUNT_VALUE"] = $actionPrice;
        $arRes["VALUE"] = $oldPrice;

        $arRes["DISCOUNT_DIFF"] = $DISCOUNT_DIFF;
        if ($oldPrice < $actionPrice) {
            //$arRes["DISCOUNT_DIFF_PERCENT"] = "+".abs($DISCOUNT_DIFF_PERCENT);
            $arRes["DISCOUNT_DIFF_PERCENT"] = 0;
            $arRes["DISCOUNT_DIFF"] = 0;
            $arRes["VALUE"] = $actionPrice;

        } else {
            $arRes["DISCOUNT_DIFF_PERCENT"] = "-" . $DISCOUNT_DIFF_PERCENT;
        }


        return $arRes;


    }


}