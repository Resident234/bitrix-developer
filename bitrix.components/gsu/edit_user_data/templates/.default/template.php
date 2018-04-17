<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($arResult);
?>

	<div class="b-form__row">
		<div class="b-form__row__name">
			Фамилия
		</div>
		<?
			if(isset($_REQUEST['surname'])) $surnameValue=$_REQUEST['surname'];
			else if ($arResult["USER"]["LAST_NAME"]) $surnameValue =$arResult["USER"]["LAST_NAME"];
			else $surnameValue = "";
		?>
		<div class="b-form__row__input email_field<?=$arResult["FIELDS"]["SURNAME"]["CLASS_ERROR"]?>">
			<input type="text" class="input_surname" value="<?=$surnameValue?>"  />
			<div class="b-form__row__input__error">
				<?=$arResult["FIELDS"]["SURNAME"]["ERROR_MESSAGE"]?>
			</div>
		</div>
	</div>
	<div class="b-form__row">
		<div class="b-form__row__name">
			Имя*
		</div>
		<?
			if(isset($_REQUEST['name'])) $nameValue=$_REQUEST['name'];
			else if ($arResult["USER"]["NAME"]) $nameValue =$arResult["USER"]["NAME"];
			else $nameValue = "";
		?>
		<div class="b-form__row__input email_field<?=$arResult["FIELDS"]["NAME"]["CLASS_ERROR"]?>">
			<input class="input_name" type="text" value="<?=$nameValue?>"  />
			<div class="b-form__row__input__error">
				<?=$arResult["FIELDS"]["NAME"]["ERROR_MESSAGE"]?>
			</div>
		</div>
	</div>
	<div class="b-form__row">
		<div class="b-form__row__name">
			Отчество
		</div>
		<?
			if(isset($_REQUEST['secondname'])) $secondnameValue=$_REQUEST['secondname'];
			else if ($arResult["USER"]["SECOND_NAME"]) $secondnameValue =$arResult["USER"]["SECOND_NAME"];
			else $secondnameValue = "";
		?>
		<div class="b-form__row__input email_field<?=$arResult["FIELDS"]["SECOND_NAME"]["CLASS_ERROR"]?>">
			<input class="input_secondname" type="text" value="<?=$secondnameValue?>"  />
			<div class="b-form__row__input__error">
				<?=$arResult["FIELDS"]["SECOND_NAME"]["ERROR_MESSAGE"]?>
			</div>
		</div>
	</div>
	<div class="b-form__row">
		<div class="b-form__row__name">
			Электронная почта*
		</div>
		<?
			if(isset($_REQUEST['email'])) $emailValue=$_REQUEST['email'];
			else if ($arResult["USER"]["EMAIL"]) $emailValue =$arResult["USER"]["EMAIL"];
			else $emailValue = "";
		?>
		<div class="b-form__row__input email_field<?=$arResult["FIELDS"]["EMAIL"]["CLASS_ERROR"]?>">
			<input class="input_email" type="text"  value="<?=$emailValue?>" placeholder="mail@mail.ru"  />
			<div class="b-form__row__input__error">
				<?=$arResult["FIELDS"]["EMAIL"]["ERROR_MESSAGE"]?>
			</div>
		</div>
	</div>
	<div class="b-form__row">
		<div class="b-form__row__name">
			Мобильный Телефон*
		</div>
		<?
			if(isset($_REQUEST['phone'])) $phoneValue=$_REQUEST['phone'];
			else if ($arResult["USER"]["PERSONAL_PHONE"]) $phoneValue =$arResult["USER"]["PERSONAL_PHONE"];
			else $phoneValue = "";
		?>
		<div class="b-form__row__input email_field<?=$arResult["FIELDS"]["PHONE"]["CLASS_ERROR"]?>">
			<input type="text" class="input_phone" value="<?=$phoneValue?>"  />
			<div class="b-form__row__input__error">
				<?=$arResult["FIELDS"]["PHONE"]["ERROR_MESSAGE"]?>
			</div>
		</div>
	</div>

	<?//dump($arResult["USER"])?>
	<?for($i=0;$i<3;$i++):?>
		<?
			if(!($_REQUEST["other_phones"]["phone".$i]||$arResult["USER"]["UF_PHONE_NUMBERS"][$i])) $class=" hidden";
			else $class=""
		?>
		<div class="b-form__row <?=$class?>" data-id="<?=$i?>" >
			<div class="b-form__row__name">
				Телефон
			</div>
			<?
				if(isset($_REQUEST["other_phones"]["phone".$i])) $phoneValue=$_REQUEST["other_phones"]["phone".$i];
				else if ($arResult["USER"]["UF_PHONE_NUMBERS"][$i]) $phoneValue =$arResult["USER"]["UF_PHONE_NUMBERS"][$i];
				else $phoneValue = "";
			?>
			<div class="b-form__row__input email_field<?=$arResult["FIELDS"]["PHONE".$i]["CLASS_ERROR"]?>">
				<input type="text" class="input_phone p<?=$i?>" value="<?=$phoneValue?>"  />
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["PHONE.$i"]["ERROR_MESSAGE"]?>
				</div>
			</div>
		</div>
	<?endfor;?>

	<div class="clear"></div>
	<div class="b-form__row">
		<a class="btn btn_red" id="save_changes" href="#">Сохранить изменения</a>
	</div>

	<div class="b-form__row">
		<a class="btn" id="add_phone" href="#">+ Добавить телефон</a>
	</div>

	<div class="clear"></div>

	<div class="ajax_path" style="display: none">
		<?=$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";?>
	</div>

	<div class="preloader edit"></div>

	<div class="success" style="display: none"><?=$arResult["SUCCESS"]?></div>

	<div class="b-popup only-title" id="success_save">
		<div class="b-popup__title title_center">Данные сохранены!</div>
	</div>
	<input type="hidden" id="dadata_fio" value="<?=$componentPath."/dadata_fio.php";?>"/>
	<input type="hidden" id="dadata_mail" value="<?=$componentPath."/dadata_mail.php";?>"/>
	<input type="hidden" id="dadata_phone" value="<?=$componentPath."/dadata_phone.php";?>"/>



