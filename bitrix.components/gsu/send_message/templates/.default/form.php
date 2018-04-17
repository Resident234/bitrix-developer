<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($_SERVER);
?>


<div class="b-popup__title">
	Письмо Yardcompany
</div>
<div class="b-popup__under-title">менеджеру <?=$arResult["MANAGER_NAME"]?> <?=$arResult["MANAGER_LAST_NAME"]?></div>

<div class="b-form">

		<div class="b-form__row sm-bot-marg b-form__row_xm-width">
			<div class="b-form__row__name">
				Как к Вам обаращаться?
			</div>
			<?
				if(isset($_REQUEST['name'])) $nameValue=$_REQUEST['name'];
				else if ($arParams["USER_NAME"]) $nameValue = $arParams["USER_NAME"];
				else $nameValue = "";
			?>
			<div class="b-form__row__input email_field<?if($arResult["FIELDS"]["NAME"]["INCORRECT"]) echo ' error'?>">
				<input class="input_name" type="text" value="<?=$nameValue?>" placeholder="Иванов Иван"  />
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["NAME"]["ERROR_MESSAGE"]?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="b-form__row sm-bot-marg b-form__row_xm-width">
			<div class="b-form__row__name">
				Ваша почта для ответа*
			</div>
			<?
				if(isset($_REQUEST['email'])) $emailValue=$_REQUEST['email'];
				else if ($arParams["USER_EMAIL"]) $emailValue = $arParams["USER_EMAIL"];
				else $emailValue = "";
			?>
			<div class="b-form__row__input email_field<?if($arResult["FIELDS"]["EMAIL"]["INCORRECT"]) echo ' error'?>">
				<input class="input_email" type="text" value="<?=$emailValue?>" placeholder="mail@mail.ru"  />
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["EMAIL"]["ERROR_MESSAGE"]?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="b-form__row sm-bot-marg b-form__row_xm-width">
			<div class="b-form__row__name">
				Текст сообщения
			</div>
			<div class="b-form__row__input email_field<?if($arResult["FIELDS"]["MESSAGE"]["INCORRECT"]) echo ' error'?>">
				<textarea class="input_message"><?if(isset($_REQUEST['message'])) echo $_REQUEST['message']?></textarea>
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["MESSAGE"]["ERROR_MESSAGE"]?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<!-- https://developers.google.com/recaptcha/ - эту ставим капчу-->

		<form id="form_captcha_mess">

			<div class="b-form__row__input<?if(!$arResult["CAPTCHA"]["STATUS"]) echo ' error'?>">

				<div id="captcha_message"></div>

				<div class="b-form__row__input__error">
					<?=$arResult["CAPTCHA"]["ERROR"]?>
				</div>
			</div>
		</form>
</div>
<p>
	<a class="btn btn_red" href="#" id="btn_send_mes">Отправить</a>
</p>

<div class="ajax_path" style="display: none">
	<?=$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";?>
</div>
<div class="managerId" style="display: none">
	<?=$arParams["MANAGER_FOR_ANSWER"]?>
</div>

<div class="preloader"></div>