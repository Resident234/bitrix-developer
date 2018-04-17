<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($arResult);
?>

	<div class="b-popup__title">
		Заказать звонок
	</div>
	<div class="b-form">
		<div class="b-form__row sm-bot-marg">
			<div class="b-form__row__name">
				Как к Вам обращаться?
			</div>
			<?
				if(isset($_REQUEST['name'])) $namelValue=$_REQUEST['name'];
				else if ($arParams["USER_NAME"]) $namelValue = $arParams["USER_NAME"];
				else $namelValue = "";
			?>
			<div class="b-form__row__input email_field<?if($arResult["FIELDS"]["NAME"]["INCORRECT"]) echo ' error'?>">
				<input class="input_name" type="text" value="<?=$namelValue?>" placeholder="Иванов Иван" name="name" />
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["NAME"]["ERROR_MESSAGE"]?>
				</div>

			</div>
		</div>
		<div class="clear"></div>
		<div class="b-form__row sm-bot-marg">
			<div class="b-form__row__name">
				Телефон*
			</div>
			<?
				if(isset($_REQUEST['phone'])) $phoneValue=$_REQUEST['phone'];
				else if ($arParams["USER_PHONE"]) $phoneValue = $arParams["USER_PHONE"];
				else $phoneValue = "";
			?>
			<div class="b-form__row__input email_field<?if($arResult["FIELDS"]["PHONE"]["INCORRECT"]) echo ' error'?>">
				<input class="input_phone" type="text" value="<?=$phoneValue?>" placeholder="" name="phone" />
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["PHONE"]["ERROR_MESSAGE"]?>
				</div>
			</div>
			<?if($USER->IsAuthorized()):?>
				<input type="hidden" name="request_call_e-mail" value="<?=$USER->GetEmail();?>"/>
				<input type="hidden" name="request_call_userId" value="<?=$USER->GetID();?>"/>
			<?endif;?>
		</div>

	</div>
	<p>
		<a class="btn btn_red" href="#" data-manager="<?=$arParams["MANAGER_FOR_ANSWER"]?>" id="btn_request_call">Заказать звонок</a>
	</p>

	<div class="ajax_path" style="display: none">
		<?=$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";?>
	</div>

	<div class="preloader"></div>
