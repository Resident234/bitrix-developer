<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
//dump($arResult);
?>

<div class="b-vacancies__item__name"><?= $arResult["COMPANY"]["NAME"]?></div>
<div class="b-vacancies__item__wrap tmp1">
    <div class="b-form">
        <?foreach($arResult["FIELDS"] as $key=>$field):?>
            <div class="<?=$field["CSS_CLASS"]?>">
                <div class="b-form__row__name">
                    <?=$field["NAME"]?>
                </div>
                <div class="b-form__row__input email_field<?=$field["CLASS_ERROR"]?>">
                    <?if($field["TYPE_FIELD"]=="SELECT"):?>
                        <select name="" class="input_<?=$key?>">
                            <?foreach($field["VALUE_LIST"] as $value):?>
                                <option <?if($value==$field["CURRENT_VALUE"]) echo "selected"?>><?=$value?></option>
                            <?endforeach;?>
                        </select>
                    <?elseif($field["TYPE_FIELD"]=="INPUT"):?>
                        <input class="input_<?=$key?> <?=$field["FIELD_ADD_CLASS"]?>"  type="text" value="<?=$field["VALUE"]?>" />
                        <div class="b-form__row__input__error">
                            <?=$field["ERROR_MESSAGE"]?>
                        </div>
                    <?endif;?>
                </div>
            </div>

            <?if($key=="PHONE_ADD"):?>
                <div class="clear"></div>
                <div class="b-form__row">
                    <p>Юридический адрес:</p>
                </div>
                <div class="clear"></div>
            <?elseif($key=="LEGAL_APARTMENT"):?>
                <div class="clear">
                    <div class="b-form__row b-form__row_check">
                        <span style="margin-right:20px;">Адрес доставки:</span>
                        <input type="checkbox" value="1" id="ch3" />
                        <label for="ch3">Совпадает с юридическим</label>
                        <a class="b-form__pers-link" href="#">Взять из моих адресов</a>
                    </div>
                </div>
                <div class="clear"></div>
            <?endif;?>

        <?endforeach;?>

        <div class="clear"></div>
        <div class="btn btn_red save" data-class="<?=$arParams['WRAP_CLASS']?>" data-id="<?=$arParams['ID']?>">Сохранить</div>

        <div class="ajax_submit" style="display: none"><?=$templateFolder."/ajax.php";?></div>
        <div class="delete_path" style="display: none"><?=$arResult["AJAX_DELETE"]?></div>
        <div class="preloader company"></div>
        <div class="success" style="display: none"><?=$arResult["SUCCESS"]?></div>

    </div>
</div>





