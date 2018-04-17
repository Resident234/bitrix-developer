<?

	//Создаю поля для каждого адреса
	$arResult["FIELDS"] = $arParams["DESCRIPTION_FIELDS"];
	$arResult["FIELDS_JSON"] = json_encode($arResult["FIELDS"]);


	$arResult["CHECK"]=1;



	//Если мы получили данные
	if($_REQUEST["IS_SUBMIT"]) {


		//Проверяем поля
		foreach($arResult["FIELDS"] as $key=>&$field) {

			$val = $_REQUEST[$key];


			if($field["TYPE_FIELD"]!="SELECT") {
				check($val, $key , $arResult);
				$field["VALUE"] = $val;
			}
			elseif($field["TYPE_FIELD"]=="SELECT") {
				$field["CURRENT_VALUE"] = $val;
			}
		}

	}




	$this->IncludeComponentTemplate();



?>