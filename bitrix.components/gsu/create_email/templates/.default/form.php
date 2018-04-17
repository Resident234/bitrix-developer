<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($arResult);
?>

	<div class="b-popup__title">
		Установка Email
	</div>
	<div class="b-popup__under-title">К сожалению, нам не удалось получить<br /> Ваш email из социальной сети,<br />
	поэтому Вам необходимо<br />создать его вручную</div>
	<div class="b-form">
		<div class="b-form__row sm-bot-marg">
			<div class="b-form__row__name">
				Введите email
			</div>
			<div class="b-form__row__input email_field<?if($arResult["FIELDS"]["EMAIL"]["INCORRECT"]) echo ' error'?>">
				<input class="input_email" type="text" value="<?if(isset($_REQUEST['email'])) echo $_REQUEST['email']?>" placeholder="mail@mail.ru"  />
				<div class="b-form__row__input__error">
					<?=$arResult["FIELDS"]["EMAIL"]["ERROR_MESSAGE"]?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<p>
		<a class="btn btn_red" href="#" id="btn_create_email">Установить Email</a>
	</p>

	<div class="id" style="display: none"><?=$arParams["USER_ID"]?></div>
	<div class="confirm_code" style="display: none"><?=$arParams["CONFIRM_CODE"]?></div>

	<div class="ajax_path" style="display: none">
		<?=$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";?>
	</div>

	<div class="preloader"></div>
