<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
?>
<?$APPLICATION->IncludeComponent(
    "custom:locations",
    "",
    array(
        'IBLOCK_TYPE' => "news",
        'IBLOCK_ID' => 1,
        'HL_ID' => 3,
        'COOKIE_TILE' => time() + 60 * 60 * 24 * 10,
        'IBLOCK_CODE' => "",
        'SHOW_NAV' => 'N',
        'COUNT' => 1000,
        'SORT_FIELD' => 'ID',
        'SORT_DIRECTION' => 'ASC',
        'CACHE_TYPE' => 'N',//на время тестиование кэширование отключаю
        'CACHE_TIME' => 60,
        'FILTER' => array()
    ),
    false
);?>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>