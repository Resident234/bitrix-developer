<?
    if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
    $this->setFrameMode(true);
?>

    <div class="b-header__basket">
        <a href="<?=BASKET_ADDRESS?>" class="b-header__basket__link-fake"></a>
        <div class="b-header__basket__count"><?=$arResult["COUNT"]?></div>
        <div class="b-header__basket__price">
            <span class="b-header__basket__price-sum"><?=$arResult["GENERAL_COST"]?></span>
            <span class="b-header__basket__price-currency">руб.</span>
        </div>
        <a href="<?=SITE_DIR?>personal/order" class="b-header__basket__link">В корзину</a>
    </div>