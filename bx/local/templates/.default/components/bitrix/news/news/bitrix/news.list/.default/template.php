<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="news-wrap__list ajax-list preloader-container">
    <div id="win8_wrapper" class="js-preloader-Wait">
        <div class="windows8">
            <div class="wBall" id="wBall_1">
                <div class="wInnerBall"></div>
            </div>
            <div class="wBall" id="wBall_2">
                <div class="wInnerBall"></div>
            </div>
            <div class="wBall" id="wBall_3">
                <div class="wInnerBall"></div>
            </div>
            <div class="wBall" id="wBall_4">
                <div class="wInnerBall"></div>
            </div>
            <div class="wBall" id="wBall_5">
                <div class="wInnerBall"></div>
            </div>
        </div>
    </div>

    <?foreach($arResult["ITEMS"] as $arItem):?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));


        $strNewDate = ($arItem["DISPLAY_ACTIVE_FROM"]) ? $arItem["DISPLAY_ACTIVE_FROM"] : $arItem["DATE_CREATE"];

        //echo "<pre>";
        //print_r($arItem);
        //echo "</pre>";
        ?>
        <div class="news-wrap__item ajax-item" id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <div class="news-item">
                <div class="news-item__content-wrap">
                    <div class="news-item__text-wrap">
                        <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="news-item__title"><?=$arItem['NAME']?></a>
                        <p class="news-item__date">
                            <?=strtolower($strNewDate)?>
                        </p>
                        <p class="news-item__desc">
                            <?=$arItem['~PREVIEW_TEXT']?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    <?endforeach;?>
</div>
<?=$arResult['NAV_STRING']?>
