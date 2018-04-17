<?
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
?>

<div class="b-popup" id="city_change">
    <div class="b-popup__title">Укажите свой город!</div>
    <div class="b-popup__text">
        <div class="b-popup__city-change">
            <div class="b-popup__city-change__search">
                <input type="text" value="" placeholder="Начните вводить название города">
            </div>
            <div class="b-popup__city-change__list">
                <?foreach($arResult["ITEMS"] as $items):?>
                    <a class="b-popup__city-change__item" data-id="<?=$items["ID"]?>" href="#"><span><?=$items["NAME"]?></span></a>
                <?endforeach;?>
            </div>
        </div>
    </div>
</div>

