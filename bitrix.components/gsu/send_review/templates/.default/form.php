<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($_SERVER);
?>


<div class="b-popup__title">
	Отзыв
</div>
<div class="b-popup__under-title"><?=htmlspecialchars_decode($arParams["PRODUCT_NAME"])?></div>

<div class="b-form">

		<div class="b-form__row sm-bot-marg b-form__row_xm-width">
			<div class="b-form__row__email">
				Как к Вас зовут?
			</div>
			<?
				if(isset($_REQUEST['name'])) $namelValue=$_REQUEST['name'];
				else if ($arParams["USER_NAME"]) $namelValue = $arParams["USER_NAME"];
				else $namelValue = "";
			?>
			<div class="b-form__row__input email_field<?if($arResult["FIELDS"]["NAME"]["INCORRECT"]) echo ' error'?>">
				<input class="input_name" type="text" value="<?=$namelValue?>" placeholder="Иванов Иван"  />
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["NAME"]["ERROR_MESSAGE"]?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="b-form__row sm-bot-marg b-form__row_xm-width">
			<div class="b-form__row__email">
				Ваш Email*
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
			<div class="b-form__row__email">
				Текст отзыва
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
		<input type="hidden" class="product-id" value="<?=$arParams["PRODUCT_ID"]?>"/>
		<input type="hidden" class="product-name" value="<?=$arParams["PRODUCT_NAME"]?>"/>
		<input type="hidden" class="user-name" value="<?=$arParams["USER_NAME"]?>"/>
		<input type="hidden" class="user-email" value="<?=$arParams["USER_EMAIL"]?>"/>
</div>
<p>
	<a class="btn btn_red" href="#" id="btn_send_mes">Отправить</a>
</p>

<div class="ajax_path" style="display: none">
	<?=$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";?>
</div>

<div class="preloader"></div>