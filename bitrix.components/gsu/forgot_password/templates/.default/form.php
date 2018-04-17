<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);
//dump($arResult);
?>
<div class="b-form">
    <div class="b-form__row sm-bot-marg b-form__row_xm-width">
        <div class="b-form__row__name">
            Email*
        </div>
        <div class="b-form__row__input email_field<? if ($arResult["FIELDS"]["EMAIL"]["INCORRECT"]) echo ' error' ?>">
            <input class="input_email" type="text" value="<? if (isset($_REQUEST['email'])) echo $_REQUEST['email'] ?>"
                   placeholder="mail@mail.ru"/>

            <div class="b-form__row__input__error">
                <?= $arResult["FIELDS"]["EMAIL"]["ERROR_MESSAGE"] ?>
            </div>
        </div>
    </div>
</div>

<p>
    <a class="btn btn_red btn_restore" href="#">Восстановить пароль</a>
</p>

<input type="hidden" class="ajax_path" value="<?= $componentPath . "/ajax.php"?>" />


<div class="preloader"></div>


