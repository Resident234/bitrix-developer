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
<div class="news-wrap__list ajax-list">
    <?foreach($arResult["ITEMS"] as $arItem):?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

        $img = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width'=>375, 'height'=>370), BX_RESIZE_IMAGE_EXACT);
        ?>
        <div class="news-wrap__item js-news-wrap__item ajax-item"
             id="<?=$this->GetEditAreaId($arItem['ID']);?>">
            <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="news-item js-lazy"
               data-src="<?=$img['src']?>">
                <div class="news-item__content-wrap">
                    <div class="news-item__text-wrap">
                        <h4 class="news-item__title"><?=$arItem['NAME']?></h4>
                        <h5 class="news-item__subtitle"><?=$arItem['PROPERTIES']['SUBTITLE']['VALUE']?></h5>
                        <p class="news-item__date"><?=strtolower($arItem['DISPLAY_ACTIVE_FROM'])?></p>
                        <p class="news-item__desc js-news-slider-desc">
                            <?=$arItem['~PREVIEW_TEXT']?>
                        </p>
                    </div>
                    <button class="news-item__button">подробнее</button>
                </div>
            </a>

            <?
            if(!empty($arParams["LIKED_NEWS_IDS"])) {

                $arButtonData = array(
                    "LABEL" => GetMessage("LABEL_LIKE"),
                    "COLOR" => "green"
                );

                if (in_array($arItem['ID'], $arParams["LIKED_NEWS_IDS"])) {
                    $arButtonData = array(
                        "LABEL" => GetMessage("LABEL_DISLIKE"),
                        "COLOR" => "red"
                    );
                }
                ?>
                <div class="news-item__line">
                    <div class="news-item__input-holder">
                        <button class="btn btn_<?= $arButtonData["COLOR"]; ?>
                        btn_block send-btn js-like"
                                data-new-id="<?= $arItem['ID']; ?>"
                                data-iblock-id="<?= $arItem['IBLOCK_ID']; ?>"
                                data-like-label="<?=GetMessage("LABEL_LIKE");?>"
                                data-dislike-label="<?=GetMessage("LABEL_DISLIKE");?>"
                                data-handler-path="<?= $templateFolder . "/like.php"; ?>">
                            <?= $arButtonData["LABEL"]; ?>
                        </button>
                    </div>
                </div>
                <?
            }
            ?>

            <div class="news-item__line js-news-item__line">
                <?=$arItem["PROPERTIES"]["LIKED_USERS"]["VALUE_LOGINS_STR"];?>
            </div>

        </div>
    <?endforeach;?>
</div>
<?=$arResult['NAV_STRING']?>
