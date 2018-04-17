<link rel="stylesheet" type="text/css" href="<?= str_replace($_SERVER['DOCUMENT_ROOT'], "", __DIR__) . "/settings/fields.css" ?>"/>
<?

CJSCore::Init(array("jquery"));

if(!CModule::IncludeModule("iblock"))
	return;

$arTypesEx = CIBlockParameters::GetIBlockTypes();

$arIBlocks = Array();
$db_iblock = CIBlock::GetList(Array("SORT"=>"ASC"), Array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
{
    $arIBlocks[$arRes["ID"]] = $arRes["NAME"];
}


$arComponentParameters = Array(
    "GROUPS" => Array(
        "GROUP_BASE"    => Array (
            "NAME"      => GetMessage("NP_FORMS_GROUP_BASE"),
            "SORT"      => 0
        ),
        "SEND_EMAIL"    => Array (
            "NAME"      => GetMessage("NP_FORMS_GROUP_SEND_EMAIL"),
            "SORT"      => 1
        ),
        "SEND_IBLOCK"   => Array (
            "NAME"      => GetMessage("NP_FORMS_GROUP_SEND_IBLOCK"),
            "SORT"      => 2
        ),
        "FIELDS"   => Array (
            "NAME"      => GetMessage("NP_FORMS_GROUP_FIELDS"),
            "SORT"      => 3
        ),
        "MESSAGES"   => Array (
            "NAME"      => GetMessage("NP_FORMS_GROUP_MESSAGES"),
            "SORT"      => 4
        ),
        "OTHER"   => Array (
            "NAME"      => GetMessage("NP_FORMS_GROUP_OTHER"),
            "SORT"      => 5
        ),
    ),
    "PARAMETERS" => Array (
        
        "NAME" => Array (
            "PARENT"    => "GROUP_BASE",
            "NAME"      => GetMessage("NP_FORMS_BASE_FORM_NAME"),
            "TYPE"      => "STRING",  
            "DEFAULT"   => GetMessage("NP_FORMS_BASE_FORM_NAME_DEFAULT")
        ),
        
        "SEND_EMAIL_ENABLED" => Array (
            "PARENT"    => "SEND_EMAIL",
            "NAME"      => GetMessage("NP_FORMS_SEND_EMAIL_ENABLED"),
            "TYPE"      => "CHECKBOX",
            "DEFAULT"   => "Y",
        ),
        
        "SEND_EMAIL_ADDRESS" => Array (
            "PARENT"    => "SEND_EMAIL",
            "NAME"      => GetMessage("NP_FORMS_SEND_EMAIL_ADDRESS"),
            "TYPE"      => "STRING",
        ),
        
        "SEND_EMAIL_EVENT_NAME" => Array (
            "PARENT"    => "SEND_EMAIL",
            "NAME"      => GetMessage("NP_FORMS_SEND_EMAIL_EVENT_NAME"),
            "TYPE"      => "STRING",
        ),
        
        "SEND_IBLOCK_ENABLED"   => Array (
            "PARENT"    => "SEND_IBLOCK",
            "NAME"      => GetMessage("NP_FORMS_SEND_IBLOCK_ENABLED"),
            "TYPE"      => "CHECKBOX",
            "DEFAULT"   => "N",
        ),
        
        "SEND_IBLOCK_TYPE"   => Array (
            "PARENT"    => "SEND_IBLOCK",
            "NAME"      => GetMessage("NP_FORMS_SEND_IBLOCK_TYPE"),
            "TYPE"      => "LIST",
            "REFRESH"   => "Y",
            "VALUES"    => $arTypesEx,
        ),
        
        "SEND_IBLOCK_ID"   => Array (
            "PARENT"    => "SEND_IBLOCK",
            "NAME"      => GetMessage("NP_FORMS_SEND_IBLOCK_ID"),
            "TYPE"      => "LIST",
            "VALUES"    => $arIBlocks,
        ),
        
        "FIELDS" => array(
            "PARENT" => "FIELDS",
            "REFRESH" => "N",
            "NAME" => GetMessage("NP_FORMS_FIELDS"),
            "TYPE" => "CUSTOM",
            "JS_FILE" => str_replace($_SERVER['DOCUMENT_ROOT'], "", __DIR__) . "/settings/settings.js?hash=" . time(),
            "JS_EVENT" => "OnNextypeFormsEdit",
            "DEFAULT" => "W3sibmFtZSI6Ik5BTUUiLCJsYWJlbCI6Ik5hbWUiLCJ0eXBlIjoidGV4dCIsIm1hc2siOiIiLCJyZXF1aXJlZCI6IiIsImRlZmF1bHQiOiIiLCJ2YWx1ZXMiOiIifV0=",
        ),
        
        "MESSAGE_SUCCESS"   => Array (
            "PARENT"    => "MESSAGES",
            "NAME"      => GetMessage("NP_FORMS_MESSAGES_SUCCESS"),
            "TYPE"      => "STRING",
            "DEFAULT" => GetMessage("NP_FORMS_MESSAGE_SUCCESS_DEFAULT")
        ),
        
        "MESSAGE_ERRORALL"   => Array (
            "PARENT"    => "MESSAGES",
            "NAME"      => GetMessage("NP_FORMS_MESSAGES_ERRORALL"),
            "TYPE"      => "STRING",
        ),
        
        "DECODE_FIELDNAME"   => Array (
            "PARENT"    => "OTHER",
            "NAME"      => GetMessage("NP_FORMS_OTHER_DECODE_FIELDNAME"),
            "TYPE"      => "CHECKBOX",
            "DEFAULT"   => "Y",
        ),
    )
);
?>