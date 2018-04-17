<?
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 17.02.2017
 * Time: 13:45
 */
require_once($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_admin_before.php");

/**
 *  достать следующий набор переменных
 *  lang:'ru',
 *  action: 'sitemap_run',
 *  ID: ID, - идентификатор настроек генерации карты
 *  value: value,
 *  pid: pid,
 *  NS: NS,
 *  sessid: BX.bitrix_sessid()
 */
define('ADMIN_MODULE_NAME', 'seo');

use Bitrix\Main;
use Bitrix\Main\Text\Converter;
use Bitrix\Main\Localization\Loc;
use Bitrix\Seo\SitemapTable;
use Bitrix\Seo\SitemapRuntime;
use Bitrix\Seo\SitemapRuntimeTable;
use Bitrix\Main\IO;
use Bitrix\Main\SiteTable;
use Bitrix\Seo\RobotsFile;
use Bitrix\Seo\SitemapIndex;

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/seo/admin/seo_sitemap.php');


if (!$USER->CanDoOperation('seo_tools'))
{
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

if (!Main\Loader::includeModule('seo')) {
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
    ShowError(Loc::getMessage("SEO_ERROR_NO_MODULE"));
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
}


$lang = LANGUAGE_ID;
$action = 'sitemap_run';

if(!Main\Loader::includeModule('seo'))
{
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
    ShowError(Loc::getMessage("SEO_ERROR_NO_MODULE"));
    require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
}

//достаём идентификатор настроек генерации карты
//достаём список настроек генерации карты
$tableID = "tbl_sitemap";

$map = SitemapTable::getMap();
unset($map['SETTINGS']);

$sitemapList = SitemapTable::getList(array(
    'order' => array("ID" => "ASC"),
    "select" => array_keys($map),
));
$data = new CAdminResult($sitemapList, $tableID);

while($sitemap = $data->NavNext())
{
    //берём идентификатор первой попавшейся активной настройки.
    //будем исходить из предположения, что активная настройка будет только одна.
    if($sitemap['ACTIVE'] == "Y"){
        $ID = intval($sitemap['ID']); //идентификатор настроек генерации карты
        break;
    }

}

$value = 0;
$pid = "";
$NS = "";
$sessid = bitrix_sessid();

SitemapHelper::run($lang, $action, $ID, $value, $pid, $NS, $sessid);




?>