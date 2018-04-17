<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */

use Bitrix\Main\Loader;

class CurrencyHelper
{
    /**
     * Returns right currency word
     * @param int $count
     * @param string $form1
     * @param string $form2_4
     * @param string $form5_0
     * @param string $currency
     * @return string
     *
     * пример вызова
     * for($i = 0; $i < 10; $i++){
     * Debug( CurrencyHelper::getFormattedEndingCurrency(rand(0, 1000)) );
     * }
     */
    public static function getFormattedEndingCurrency($count, $currency = "RUB", $form1 = "рубль", $form2_4 = "рубля", $form5_0 = "рублей")
    {
        Loader::includeModule('currency');

        $strForm = CommonHelper::getFormattedEnding($count, $form1, $form2_4, $form5_0);

        $number = CCurrencyLang::CurrencyFormat($count, $currency, false);

        return $number . " " . $strForm;

    }





}