<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($arResult);
?>

	<div class="b-popup__title">
		Создание пароля
	</div>
	<div class="b-form">
		<div class="b-form__row sm-bot-marg">
			<div class="b-form__row__name">
				Введите пароль
			</div>
			<div class="b-form__row__input email_field <?if($arResult["STATUS"]["ERROR"]) echo "error";?>">
				<input class="input_password" type="password" name="password" />
				<div class="b-form__row__input__error">

				</div>

			</div>
		</div>
		<div class="clear"></div>
		<div class="b-form__row sm-bot-marg">
			<div class="b-form__row__name">
				Повторите пароль
			</div>
			<div class="b-form__row__input email_field <?if($arResult["STATUS"]["ERROR"]) echo "error";?>">
				<input class="input_password_repeat" type="password" name="password_repeat" />
				<div class="b-form__row__input__error">
					<?=$arResult["STATUS"]["MESSAGE"]?>
				</div>
			</div>
		</div>

	</div>
	<p>
		<a class="btn btn_red" href="#" id="btn_edit_password">Создать пароль</a>
	</p>

	<div class="id" style="display: none"><?=$arParams["USER_ID"]?></div>
	<div class="confirm_code" style="display: none"><?=$arParams["CONFIRM_CODE"]?></div>

	<div class="ajax_path" style="display: none">
		<?=$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";?>
	</div>

	<div class="preloader"></div>
