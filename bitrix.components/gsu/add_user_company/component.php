<?

if (!\Bitrix\Main\Loader::includeModule("iblock")) {
    $this->AbortResultCache();
    ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALLED"));
    return;
}

$rsUser = CUser::GetByID($USER->GetID());
$arResult["USER"] = $rsUser->Fetch();


//Создаю поля для каждого адреса
$arResult["FIELDS"] = $arParams["DESCRIPTION_FIELDS"];
$arResult["FIELDS_JSON"] = json_encode($arResult["FIELDS"]);





$arResult["CHECK"] = 1;




$arResult["AJAX_ADDRESS"] = $componentPath . "/ajax.php";

//Если мы получили данные
if ($_REQUEST["IS_SUBMIT"]) {


    //Проверяем поля
    foreach ($arResult["FIELDS"] as $key => &$field) {

        $val = $_REQUEST[$key];

        if ($field["TYPE_FIELD"] != "SELECT") {
            check($val, $key, $arResult);
            $field["VALUE"] = $val;
        } elseif ($field["TYPE_FIELD"] == "SELECT") {
            $field["CURRENT_VALUE"] = $val;
        }

    }


    //Если юридический адрес совпадает с адресом доставки, подгружаю еще свойства
    if ($_REQUEST["DELIVERY_EQ_LEGAL"] == "true") {

        $arResult["DELIVERY_EQ_LEGAL"] = true;

    } else {
        $arResult["DELIVERY_EQ_LEGAL"] = false;
        $arResult["COMPANY"]["ADDRESS_DELIVER_ID"] = $_REQUEST["ADDRESS_DELIVER_ID"];
    }






    //Если все верно обновляем
    if ($arResult["CHECK"]) {

        $arResult["GLOBAL_ERRORS"] = array();

        //Заполняем свойства юридического адреса
        $legalAddress = new CIBlockElement;

        $propLegalAddress = array(
            "BIG_ADDRESS" => $arResult["FIELDS"]["LEGAL_BIG_ADDRESS"]["VALUE"],
            "REGION" => $arResult["FIELDS"]["LEGAL_REGION"]["VALUE"],
            "CITY" => $arResult["FIELDS"]["LEGAL_CITY"]["VALUE"],
            "STREET" => $arResult["FIELDS"]["LEGAL_STREET"]["VALUE"],
            "HOME" => $arResult["FIELDS"]["LEGAL_HOME"]["VALUE"],
            "APARTMENT" => $arResult["FIELDS"]["LEGAL_APARTMENT"]["VALUE"],
            "COMMENT2" => $arResult["FIELDS"]["COMMENT2"]["VALUE"],
            "INDEX" => $arResult["FIELDS"]["LEGAL_INDEX"]["VALUE"],

        );


        $arLoadArray = Array(
            "NAME" => $propLegalAddress["INDEX"]." ".$propLegalAddress["BIG_ADDRESS"],
            "IBLOCK_ID" => USERS_ADDRESSES_IBLOCK_ID,
            "ACTIVE" => "Y",
            "PROPERTY_VALUES" => $propLegalAddress,
        );


        //Создаем юридический адрес
        if ($arResult["COMPANY"]["ADDRESS_LEGAL_ID"] = $legalAddress->Add($arLoadArray)) {
            $arResult["SUCCESS"] = 1;
            if ($_REQUEST["DELIVERY_EQ_LEGAL"] == "true") {
                $arResult["COMPANY"]["ADDRESS_DELIVER_ID"] = $arResult["COMPANY"]["ADDRESS_LEGAL_ID"];
            }
        } else {
            $arResult["SUCCESS"] = 0;
            $arResult["GLOBAL_ERRORS"][] = $legalAddress->LAST_ERROR;
        }


        //Если добавление/изменение юр адреса прошло успешно, работаем с адресом доставки
        if ($arResult["SUCCESS"]) {

            $deliverAddress = new CIBlockElement;

            $propDeliverAddress = array(
                "INDEX" => $arResult["FIELDS"]["DELIVERY_INDEX"]["VALUE"],
                "REGION" => $arResult["FIELDS"]["DELIVERY_REGION"]["VALUE"],
                "CITY" => $arResult["FIELDS"]["DELIVERY_CITY"]["VALUE"],
                "STREET" => $arResult["FIELDS"]["DELIVERY_STREET"]["VALUE"],
                "HOME" => $arResult["FIELDS"]["DELIVERY_HOME"]["VALUE"],
                "APARTMENT" => $arResult["FIELDS"]["DELIVERY_APARTMENT"]["VALUE"],
                "COMMENT2" => $arResult["FIELDS"]["COMMENT2"]["VALUE"],
                "BIG_ADDRESS" => $arResult["FIELDS"]["DELIVERY_BIG_ADDRESS"]["VALUE"],
                "USER" => $USER->GetID(),
            );


            $arLoadArray = Array(
                "NAME" => $propDeliverAddress["INDEX"]." ".$propDeliverAddress["BIG_ADDRESS"],
                "ACTIVE" => "Y",
                "IBLOCK_ID" => USERS_ADDRESSES_IBLOCK_ID,
                "PROPERTY_VALUES" => $propDeliverAddress,
            );

            //Обноляем адрес доставки
            if ($arResult["COMPANY"]["ADDRESS_DELIVER_ID"]) {

                $res = $deliverAddress->Update($arResult["COMPANY"]["ADDRESS_DELIVER_ID"], $arLoadArray);

                if ($res) $arResult["SUCCESS"] = 1;
                else {
                    $arResult["GLOBAL_ERRORS"][] = $deliverAddress->LAST_ERROR;
                    $arResult["SUCCESS"] = 0;
                }
            } //Создаем адрес доставки
            else {

                if ($arResult["COMPANY"]["ADDRESS_DELIVER_ID"] = $deliverAddress->Add($arLoadArray)) $arResult["SUCCESS"] = 1;
                else {
                    $arResult["GLOBAL_ERRORS"][] = $deliverAddress->LAST_ERROR;
                    $arResult["SUCCESS"] = 0;
                }
            }


            //Если и доавдение/изменение адреса доставки  успешно, обновляем компанию
            if ($arResult["SUCCESS"]) {

                $company = new CIBlockElement;

                $propCompany = array(
                    "USER" => $USER->GetID(),
                    "OWNERSHIP_FORM" => $arResult["FIELDS"]["OWNERSHIP_FORM"]["CURRENT_VALUE"],
                    "SMALL_NAME" => $arResult["FIELDS"]["SMALL_NAME"]["VALUE"],
                    "FULL_NAME" => $arResult["FIELDS"]["FULL_NAME"]["VALUE"],
                    "INN" => $arResult["FIELDS"]["INN"]["VALUE"],
                    "KPP" => $arResult["FIELDS"]["KPP"]["VALUE"],
                    "HEADER_POST" => $arResult["FIELDS"]["HEADER_POST"]["VALUE"],
                    "HEADER_SURNAME" => $arResult["FIELDS"]["HEADER_SURNAME"]["VALUE"],
                    "HEADER_BASIS" => $arResult["FIELDS"]["HEADER_BASIS"]["VALUE"],
                    "PHONE_GENERAL" => $arResult["FIELDS"]["PHONE_GENERAL"]["VALUE"],
                    "PHONE_ADD" => $arResult["FIELDS"]["PHONE_ADD"]["VALUE"],
                    "ADDRESS_LEGAL" => $arResult["COMPANY"]["ADDRESS_LEGAL_ID"],
                    "ADDRESS_DELIVERY" => $arResult["COMPANY"]["ADDRESS_DELIVER_ID"],
                    "COMMENT1" => $arResult["FIELDS"]["COMMENT1"]["VALUE"]

                );

                $kpp = "Не указан";
                if ($arResult["FIELDS"]["KPP"]["VALUE"]) $kpp = $arResult["FIELDS"]["KPP"]["VALUE"];

                $inn = "Не указан";
                if ($arResult["FIELDS"]["INN"]["VALUE"]) $inn = $arResult["FIELDS"]["INN"]["VALUE"];

                $arResult["COMPANY"]["NAME"] =
                    $propCompany["SMALL_NAME"]
                    . ", ИНН: " . $inn
                    . ", КПП: " . $kpp;

                $arLoadArray = Array(
                    "NAME" => $arResult["COMPANY"]["NAME"],
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => USERS_COMPANY_IBLOCK_ID,
                    "PROPERTY_VALUES" => $propCompany,
                );

                $res = $company->Add($arLoadArray);

                if ($res) $arResult["SUCCESS"] = 1;
                else {
                    $arResult["GLOBAL_ERRORS"][] = $company->LAST_ERROR;
                    $arResult["SUCCESS"] = 0;
                }
            } else  $arResult["SUCCESS"] = 0;
        } else  $arResult["SUCCESS"] = 0;
    } else  $arResult["SUCCESS"] = 0;





} else  $arResult["SUCCESS"] = 0;




foreach($arResult["FIELDS"] as $key=>&$field) {

    if($key=="OWNERSHIP_FORM"){
        $field["REQUIRED"]=0;
        $field["TYPE_FIELD"]="HIDDEN";
        $field["CSS_CLASS"]=$field["CSS_CLASS"]." hidden";

    }elseif($key=="SMALL_NAME"){
        $field["REQUIRED"]=0;
        $field["CSS_CLASS"]="b-form__row b-form__row-full";
        $field["NAME"]="Введите название, ИНН или адрес";

    }elseif($key=="LEGAL_CITY" || $key=="LEGAL_STREET" || $key=="LEGAL_HOME" || $key=="LEGAL_APARTMENT"){
        $field["REQUIRED"]=0;
        $field["CSS_CLASS"]=$field["CSS_CLASS"]." hidden";
    }elseif($key=="DELIVERY_REGION"){


    }elseif($key=="DELIVERY_INDEX" || $key=="DELIVERY_CITY" || $key=="DELIVERY_STREET" || $key=="DELIVERY_HOME" || $key=="DELIVERY_APARTMENT" || $key=="DELIVERY_FLOOR" || $key=="DELIVERY_ELEVATOR" || $key=="DELIVERY_PASS") {
        $field["REQUIRED"]=0;
        $field["CSS_CLASS"]=$field["CSS_CLASS"]." hidden";
    }elseif($key=="COMMENT1" || $key=="COMMENT2"){
        $field["TYPE_FIELD"]="TEXTAREA";

    }


}



$this->IncludeComponentTemplate();
?>