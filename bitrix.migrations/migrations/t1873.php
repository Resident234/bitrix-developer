<?
/**
*
* Изменения в БД:
* - Создать инфоблок статей для доставки
*
*/
require_once $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php";
CModule::IncludeModule('iblock');

global $DB;
$ib = 0;
$res = CIBlock::GetList(
    Array(),
    Array(
        'TYPE'=>'articles',
        'SITE_ID'=>SITE_ID,
        'ACTIVE'=>'Y',
        "CNT_ACTIVE"=>"Y",
        "=CODE"=>'dostavka-i-oplata'
    ), true
);
while($ar_res = $res->Fetch())
{
    $ib = $ar_res['ID'];
}


if($ib == 0)
{
    $arFields = Array(
        "ACTIVE" => "Y",
        "NAME" => "Доставка",
        "IBLOCK_TYPE_ID" => 'articles',
        "SITE_ID" => SITE_ID,
        "CODE" => "dostavka-i-oplata",
        "GROUP_ID" => Array("2" => "R", "1" => "X"),
        "INDEX_ELEMENT" => "N",
        "INDEX_SECTION" => "N"
    );
    $ib = new CIBlock();
    if ($r = $ib->Add($arFields))
    {
        echo 'Iblock add';
    }
    else
    {
        echo 'Error: '.$ib->LAST_ERROR.'<br />';
    }
}
