<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
header('Content-Type: application/json');?>
<?

$res = "";

if (!CModule::IncludeModule("sale")) return;

$db_vars = CSaleLocation::GetList(array("CITY_NAME_LANG"=>"ASC"), array("LID" => LANGUAGE_ID, "~CITY_NAME"=>'%'.$_POST["INPUT"].'%'), false, array("nTopCount"=>27), array());
if($db_vars->SelectedRowsCount())
{
    while ($vars = $db_vars->Fetch()) {
        $res = $res.'<a class="b-popup__city-change__item" data-id="'.$vars["CITY_ID"].'" href="#"><span>'.$vars["CITY_NAME"].'</span></a>';
    }
}else {
    $res = '<div class="b-popup__city-error"><span>По вашему запросу города не найдены</span></div>';
}

echo json_encode($res);
?>