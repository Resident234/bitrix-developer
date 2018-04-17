<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();

if($arResult["CurrentStep"] == 4)
{

	$arProperties["SIZE1"] = 100;

	$arResult["ADDRESS"] = "";
	if($arResult["DELIVERY"]["ID"] == TO_DOOR_RETAIL_FREE_ID || $arResult["DELIVERY"]["ID"] == TO_DOOR_RETAIL_PAID_ID //значит выбрана доставка до двери
		|| $arResult["DELIVERY"]["ID"] == TO_DOOR_WHOLESALE_PAID_ID || $arResult["DELIVERY"]["ID"] == TO_DOOR_WHOLESALE_FREE_ID)
	{
		$id_prod=0;
		foreach($arResult["ORDER_PROPS_PRINT"] as $prop)
		{
			//код REQ используется только для полей с доставкой
			//они идут по порядку, поэтому получаем ID первого и затем берем значение следующих
			if($prop["CODE"] == "REQ" && $prop["VALUE_FORMATED"])
			{
				$id_prod = $prop["ID"];
				break;
			}
		}
		if($id_prod>0)
		{
			for($i = $id_prod; $i < $id_prod+DELIVERY_TO_DOOR_COUNT; $i++)
			{
				if($arResult["POST"]["ORDER_PROP_$i"])
				{
					if($arResult["ADDRESS"] == "")
						$arResult["ADDRESS"] = $arResult["POST"]["ORDER_PROP_$i"];
					else
					{
						if($i==53 || $i==47)
						{
							$arResult['POST']['ORDER_PROP_'.$i] = "ул. ".$arResult['POST']['ORDER_PROP_'.$i];
						}
						elseif($i==54 || $i==48)
						{
							$arResult['POST']['ORDER_PROP_'.$i] = "д. ". $arResult['POST']['ORDER_PROP_'.$i];
						}
						elseif($i==55 ||$i==49)
						{
							$arResult['POST']['ORDER_PROP_'.$i] = "стр. ".$arResult['POST']['ORDER_PROP_'.$i];
						}
						elseif($i==56 || $i==50)
						{
							$arResult['POST']['ORDER_PROP_'.$i] = "корп. ".$arResult['POST']['ORDER_PROP_'.$i];
						}
						elseif($i==57 || $i==51)
						{
							$arResult['POST']['ORDER_PROP_'.$i] = "кв. ".$arResult['POST']['ORDER_PROP_'.$i];
						}
						$arResult["ADDRESS"] = $arResult["ADDRESS"].", ".$arResult["POST"]["ORDER_PROP_$i"];
					}
				}
			}
		}
		$arResult['DELIVERY_INFO'] = $arResult['ADDRESS'];
	}
	else if($arResult["DELIVERY"]["ID"] == TRANSPORT_COMPANY_ID) //выбрали доставку транспортной компанией
	{
		foreach($arResult["ORDER_PROPS_PRINT"] as $prop)
		{
			if($prop["CODE"] == "REQ" && $prop["VALUE_FORMATED"])
			{
				$arResult["ADDRESS"] = $prop["VALUE_FORMATED"];
			}
		}
		$arResult['DELIVERY_INFO'] = $arResult['ADDRESS'];
	}
	else if($arResult["DELIVERY"]["ID"] == MAIL_ID) //почтой
	{
		$id_prod = 0;
		foreach ($arResult["ORDER_PROPS_PRINT"] as $prop)
		{
			//код REQ используется только для полей с доставкой
			//они идут по порядку, поэтому получаем ID первого и затем берем значение следующих
			if ($prop["CODE"] == "REQ" && $prop["VALUE_FORMATED"])
			{
				$id_prod = $prop["ID"];
				break;
			}
		}
		if($id_prod>0)
		{
			for($i = $id_prod; $i < $id_prod+DELIVERY_MAIL_COUNT; $i++)
			{
				if($arResult["POST"]["ORDER_PROP_$i"])
				{
					if($arResult["ADDRESS"] == "")
						$arResult["ADDRESS"] = $arResult["POST"]["ORDER_PROP_$i"];
					else
					{
						if($i==61)
						{
							$arResult['POST']['ORDER_PROP_'.$i] = "ул. ".$arResult['POST']['ORDER_PROP_'.$i];
						}
						elseif($i==62)
						{
							$arResult['POST']['ORDER_PROP_'.$i] = "д. ". $arResult['POST']['ORDER_PROP_'.$i];
						}
						elseif($i==63)
						{
							$arResult['POST']['ORDER_PROP_'.$i] = "стр. ".$arResult['POST']['ORDER_PROP_'.$i];
						}
						elseif($i==64)
						{
							$arResult['POST']['ORDER_PROP_'.$i] = "корп. ".$arResult['POST']['ORDER_PROP_'.$i];
						}
						elseif($i==65)
						{
							$arResult['POST']['ORDER_PROP_'.$i] = "кв. ".$arResult['POST']['ORDER_PROP_'.$i];
						}
						$arResult["ADDRESS"] = $arResult["ADDRESS"].", ".$arResult["POST"]["ORDER_PROP_$i"];
					}
				}
			}
		}
		$arResult['DELIVERY_INFO'] = $arResult["ADDRESS"];
	}//для остальных адреса доставки не надо
	elseif($arResult["DELIVERY"]["ID"]==6) // самовывоз со склада
	{
		$arResult['DELIVERY_INFO'] = $arResult['DELIVERY']['DESCRIPTION'];
	}
	elseif($arResult['DELIVERY']['ID']==7 || $arResult['DELIVERY']['ID']==14) // ПВЗ
	{
		$arResult['DELIVERY_INFO'] = $_POST['DELIVERY_INFO'];
	}
	//эта часть для 5 шага. Так как к тому времени заказ уже будет оформлен, то в корзине не будет товаров. Поэтому запомним некоторую инфу
	$APPLICATION->set_cookie("COUNT_BASKET", count($arResult["BASKET_ITEMS"]), time()+60*5, "/");
	$APPLICATION->set_cookie("ADDRESS", $arResult["ADDRESS"], time()+60*5, "/");
}
//p($arResult);

?>