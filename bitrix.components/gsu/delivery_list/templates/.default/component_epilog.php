<?php
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$isAjaxRequest = (isset($_REQUEST['is_ajax']) ) ? true : false;
// Если это аякс
if ($isAjaxRequest) {
    // забираем вывод в переменную
    $content = ob_get_contents();
    // закрываем стек буфферизации
    ob_end_clean();
    // перезапускаем буффер
    $APPLICATION->RestartBuffer();
    // забираем данные из обозначенного стека
    list(, $content_html) = explode('<!--js-template-->', $content);
    // отправляем данные
    echo $content_html;
    // служебный эпилог
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
    // выход
    exit();
}