<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($arResult);
?>
<?if($USER->IsAuthorized()&&$arResult["IS_OPT_USER"]):?>
	<div class="b-cabinet">
		<div class="b-vacancies ">
			<?foreach($arResult["ADDRESSES"] as $address):?>
				<div class="b-vacancies__item ">


					<?$APPLICATION->IncludeComponent(
						"peppers:edit_user_addresses",
						"",
						Array(
							"NAME" => $address["NAME"],
							"ID" => $address["ID"],
							"INDEX" => $address["INDEX"],
							"REGION" => $address["REGION"],
							"CITY" => $address["CITY"],
							"STREET" => $address["STREET"],
							"HOME" => $address["HOME"],
							"APARTMENT" => $address["APARTMENT"],
							"FLOOR" => $address["FLOOR"],
							"ELEVATOR" => $address["ELEVATOR"],
							"PASS" => $address["PASS"],
						)
					);?>

				</div>
			<?endforeach;?>
			<div class="b-vacancies__item new hidden opened">
				<?$APPLICATION->IncludeComponent(
					"peppers:add_user_addresses",
					"",
					Array(

					)
				);?>
			</div>
		</div>

		<p>
			<a class="btn add_address" href="#">+ Добавить адрес</a>
		</p>

	</div>


	<div class="b-popup only-title" id="success_save">
		<div class="b-popup__title title_center">Данные сохранены!</div>
	</div>
<?else:?>
	<div class="b-cabinet__title">Вы не авторизованы или у Вас нет прав на просмотр этого раздела</div>
<?endif;?>


