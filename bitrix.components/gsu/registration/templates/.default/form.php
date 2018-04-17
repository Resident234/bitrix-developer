<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($arResult);
?>

	<div class="b-popup__title">
		Регистрация пользователя
	</div>
	<div class="b-form">
		<div class="b-form__row sm-bot-marg b-form__row_xm-width">
			<div class="b-form__row__name">
				Имя*
			</div>
			<div class="b-form__row__input email_field<?if($arResult["FIELDS"]["NAME"]["INCORRECT"]) echo ' error'?>">
				<input class="input_name" type="text" value="<?if(isset($_REQUEST['name'])) echo $_REQUEST['name']?>" placeholder="Иван" name="name" />
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["NAME"]["ERROR_MESSAGE"]?>
				</div>

			</div>
		</div>
		<div class="clear"></div>

		<div class="b-form__row sm-bot-marg b-form__row_xm-width">
			<div class="b-form__row__name">
				Фамилия
			</div>
			<div class="b-form__row__input email_field<?if($arResult["FIELDS"]["SURNAME"]["INCORRECT"]) echo ' error'?>">
				<input class="input_surname" type="text" value="<?if(isset($_REQUEST['surname'])) echo $_REQUEST['surname']?>" placeholder="Рябушкин" name="surname" />
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["SURNAME"]["ERROR_MESSAGE"]?>
				</div>

			</div>
		</div>
		<div class="clear"></div>

		<div class="b-form__row sm-bot-marg b-form__row_xm-width">
			<div class="b-form__row__name">
				Телефон*
			</div>
			<div class="b-form__row__input email_field<?if($arResult["FIELDS"]["PHONE"]["INCORRECT"]) echo ' error'?>">
				<input class="input_phone" type="text" value="<?if (isset($_REQUEST['phone'])) echo $_REQUEST['phone']?>" placeholder="" name="phone" />
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["PHONE"]["ERROR_MESSAGE"]?>
				</div>
			</div>
		</div>
		<div class="clear"></div>

		<div class="b-form__row sm-bot-marg b-form__row_xm-width">
			<div class="b-form__row__name">
				Почта*
			</div>
			<div class="b-form__row__input email_field<?if($arResult["FIELDS"]["EMAIL"]["INCORRECT"]) echo ' error'?>">
				<input class="input_email" type="text" value="<?if(isset($_REQUEST['email'])) echo $_REQUEST['email']?>" placeholder="mail@mail.ru"  />
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["EMAIL"]["ERROR_MESSAGE"]?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div class="b-auth__line" style="margin-left: -10px;">
			<div class="b-auth__remember">
				<input type="checkbox" id="ch2" name="is_opt" class="registration_is-opt" value="yes" <?if($_REQUEST["is_opt"]=="Y") echo "checked"?>/>
				<label for="ch2">Хочу зарегистрироваться как оптовый покупатель</label>
			</div>
		</div>
	</div>

	<p>
		<a class="btn btn_red btn_registration" href="#">Зарегистрироваться</a>
	</p>

	<div class="ajax_path" style="display: none">
		<?=$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";?>
	</div>

	<div class="preloader"></div>


