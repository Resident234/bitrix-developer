<?
/**
 *
 * Изменения в БД:
 * - Создать почтовое событие и почтовый шаблон
 *
 */

require_once $_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/prolog_before.php";

use \Bitrix\Main\Loader;
use \Bitrix\Main\Context;


Loader::includeModule("iblock");

define(EVENT_NAME, "CHECK_DELAYED_PRODUCTS");
define(LID_ADMUN, "ru");
define(LID_PUBLIC, "ru");

$obEventType = new CEventType;
$obEventType->Add(array(
    "EVENT_NAME"    => EVENT_NAME,
    "NAME"          => "В вашем вишлисте хранятся товары",
    "LID"           => "ru",
    "DESCRIPTION"   => "
        #NAME# - Имя
        #LAST_NAME# - Фамилия
        #PRODUCT_LIST# - Список товаров
        "
));


$LID = LID_PUBLIC;
if(Context::getCurrent()->getRequest()->isAdminSection()) $LID = LID_ADMUN;


$arr["ACTIVE"]      = "Y";
$arr["EVENT_NAME"]  = EVENT_NAME;
$arr["LID"]         = $LID;
$arr["EMAIL_FROM"]  = "null@" . SITE_SERVER_NAME;
$arr["EMAIL_TO"]    = "#EMAIL#";
$arr["BCC"]         = "";
$arr["SUBJECT"]     = "В вашем вишлисте хранятся товары";
$arr["BODY_TYPE"]   = "text";
$arr["MESSAGE"]     = "
Добрый день, #NAME# #LAST_NAME#.\r\nВ вашем вишлисте хранятся товары:\r\n#PRODUCT_LIST#.
";
$obTemplate = new CEventMessage;
$obTemplate->Add($arr);

