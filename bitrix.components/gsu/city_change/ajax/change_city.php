<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json');?>
<?

$res = "";

$APPLICATION->set_cookie("CITY_ID", $_POST["CITY_ID"], time()+60*60*24*30, "/");
$res["CITY_ID"] = $_POST["CITY_ID"];

if (CModule::IncludeModule('sale')) {
    $db_vars = CSaleLocation::GetList(array("CITY_NAME_LANG"=>"ASC"), array("LID" => LANGUAGE_ID, "CITY_ID"=>$_POST["CITY_ID"]), false, false, array());
    if ($vars = $db_vars->Fetch()) {
        $res["CITY"] = $vars["CITY_NAME"];
    }
}

echo json_encode($res);
?>