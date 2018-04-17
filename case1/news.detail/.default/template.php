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
<h1 class="page-title page-title_mb page-title_reverse container">
    <?=$arResult['NAME']?>
    <br>
    <span class="page-title__span"><?=$arResult['PROPERTIES']['SUBTITLE']['VALUE']?></span>
</h1>

<div class="news-inner-top">


    <? if(!$arResult['SLIDER_PINS'] && $arResult["PREVIEW_PICTURE"]["SRC"]){ ?>
        <div class="news-inner-top__image-block">
            <div class="news-inner-top__image-block-inner">
                <div class="news-inner-top__image-block-inner2 js-lazy"
                     data-src="<?=$arResult["PREVIEW_PICTURE"]["SRC"];?>"></div>
            </div>
        </div>
    <? } ?>
    
    <? if($arResult['SLIDER_PINS']){ ?>
    <div class="news-inner-top__image-block js-cards-container" id="news-inner-slider">
        <? $i=0; ?>
        <?foreach($arResult['SLIDER_PINS'] as $arSlide):?>
            <div class="news-inner-top__image-block-slide">
                <div class="news-inner-top__image-block-inner">
                    <div class="news-inner-top__image-block-inner2" style="background-image: url(<?=CFile::GetPath($arSlide['PICTURE'])?>)">
                        <?foreach($arSlide['PINS'] as $arPin):?>
                            <?list($left, $top) = explode(';', $arPin['PROPERTY_COORDS_VALUE'])?>
                            <div class="point js-one-point" style="top: <?=$top?>%; left: <?=$left?>%;"
                                 data-item="<?=$arPin['PROPERTY_ITEM_VALUE']?>"></div>
                        <?endforeach;?>
                    </div>
                </div>
            </div>
        <?endforeach;?>
    </div>
    <? } ?>


    <?
    if($arResult['PROPERTIES']['DESIGNER']) {


        $resDesigner = CIBlockElement::GetList(array(),
            array('IBLOCK_ID' => $arResult['PROPERTIES']['DESIGNER']["LINK_IBLOCK_ID"],
                "ID" => $arResult['PROPERTIES']['DESIGNER']["VALUE"]),
            array("ID", "IBLOCK_ID", "NAME", "DATE_ACTIVE_FROM", "PREVIEW_TEXT",
                "DETAIL_PICTURE",
                "PROPERTY_OCCUPATION", "PROPERTY_COMPANY", "PROPERTY_COUNTRY"));
        while ($arDesigner = $resDesigner->GetNext()) {

            ?>

            <div class="designer-slider swiper-container" id="designer-slider">
                <div class="swiper-wrapper">

                    <div class="swiper-slide">
                        <div class="designer-slider__item container">
                            <div class="designer-slider__text-part">
                                <h2 class="designer-slider__name"><?= $arDesigner['NAME'] ?></h2>
                                <p class="designer-slider__post">
                                    <?= $arDesigner['PROPERTY_OCCUPATION_VALUE'] ?>
                                </p>
                                <p class="designer-slider__company">
                                    <? if ($arDesigner['PROPERTY_COMPANY_VALUE']):?>
                                        <?= $arDesigner['PROPERTY_COMPANY_VALUE'] ?>,
                                    <?endif; ?>
                                    <?= $arDesigner['PROPERTY_COUNTRY_VALUE'] ?>
                                </p>
                                <p class="designer-slider__blockquote">
                                    <?= $arDesigner['PREVIEW_TEXT'] ?>
                                </p>

                            </div>
                            <? if ($arDesigner['DETAIL_PICTURE']):?>
                                <div class="designer-slider__photo-part">
                                    <img class="designer-slider__photo-img"
                                         src="<?= CFile::GetPath($arDesigner['DETAIL_PICTURE']) ?>"
                                         alt="<?= $arDesigner['NAME'] ?>">
                                </div>
                            <?endif; ?>
                        </div>
                    </div>


                </div>

            </div>

            <?
        }


    }
    ?>


    <? if($arResult['PREVIEW_TEXT']){ ?>
    <h1 class="page-title page-title_mb page-title_reverse container">
        <span class="page-title__span"><?=$arResult['PREVIEW_TEXT']?></span>
    </h1>
    <? } ?>

    <div class="news-inner-top__text-block container">
        <?=$arResult['~DETAIL_TEXT']?>
    </div>

    <?foreach($arResult['ITEMS'] as $arItem):
        $img = CFile::ResizeImageGet($arItem['DETAIL_PICTURE'], array('width'=>368, 'height'=>400));
        ?>
        <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="cart-item cart-item_certain cart-item_on-pin js-cart-on-pin" id="good-card-<?=$arItem['ID']?>">
            <div class="cart-item__image-wrap">
                <div class="cart-item__image-vertical-align">
                    <img class="cart-item__img " src="<?=$img['src']?>" alt="<?=$arItem['NAME']?>">
                </div>
            </div>
            <div class="cart-item__text-wrap">
                <p class="cart-item__name"><?=$arItem['NAME']?></p>
                <p class="cart-item__series"><?=$arItem['PROPERTY_ARTNUMBER_VALUE']?></p>
                <p class="cart-item__price">
                    <?=$arItem['PRICE'][$arParams['PRICE_CODE']]['PRINT_VALUE']?>
                    <span class="rub"><span>руб.</span></span>
                </p>
            </div>
            <form action="<?=$arItem['ADD_URL']?>" class="cart-item__form js-cart-item__basket-button" data-id="<?=$arItem['ID']?>" data-name="<?=$arItem['NAME']?>" data-list="true">
                <button class="cart-item__button cart-item__button_with-icon">
                    <i class="cart-item__button-icon"></i>
                    <span class="cart-item__button-span">В корзину</span>
                </button>
            </form>
        </a>
    <?endforeach;?>
</div>

<?if($arResult['PROPERTIES']['VIDEO']['VALUE']):?>
    <div class="video-block container">
        <div class="video-block__container">
            <div class="video-block__video-block js-lazy" data-src="<?=CFile::GetPath($arResult['PROPERTIES']['VIDEO_IMG']['VALUE'])?>">
                <? if($arResult['PROPERTIES']['VIDEO']['VALUE']['path']){ ?>
                    <a href="https://www.youtube.com/embed/<?=getYoutubeVideoID($arResult['PROPERTIES']['VIDEO']['VALUE']['path'])?>" class="video-block__video-link js-video-block-link fancybox.iframe"></a>
                <? } ?>
            </div>
            <div class="video-block__text-block">
                <div class="video-block__title-block">
                    <h2 class="video-block__title-line"><?=$arResult['PROPERTIES']['VIDEO_TITLE']['VALUE']?></h2>
                    <h2 class="video-block__title-line"><?=$arResult['PROPERTIES']['VIDEO_SUBTITLE']['VALUE']?></h2>
                </div>
                <p class="video-block__desc">
                    <?=$arResult['PROPERTIES']['VIDEO_DESCRIPTION']['~VALUE']['TEXT']?>
                </p>

                <? if ($arResult['PROPERTIES']['VIDEO_BTN_TEXT']['VALUE']):?>
                    <div class="video-block__button-wrap">
                    <a href="<?= $arResult['PROPERTIES']['VIDEO_BTN_LINK']['VALUE'] ?>"
                    class="btn btn_green"><?= $arResult['PROPERTIES']['VIDEO_BTN_TEXT']['VALUE'] ?></a>
                    </div>
                <?endif; ?>
            </div>
        </div>
    </div>
<?endif;?>



<?if(!empty($arResult['BENEFITS'])):?>
    <div class="news-inner-detail container">
        <div class="news-inner-detail__wrap">
            <?foreach($arResult['BENEFITS'] as $arBenefit):?>
                <div class="news-inner-detail__item">
                    <div class="news-inner-detail__image-wrap">
                        <?if($arBenefit['PREVIEW_PICTURE']):?>
                            <img src="<?=$arBenefit['PREVIEW_PICTURE']?>" alt="<?=$arBenefit['NAME']?> <?=$arBenefit['PROPERTY_SUBTITLE_VALUE']?>" class="news-inner-detail__img">
                        <?endif;?>
                        <?$arItem = $arResult['ITEMS'][$arBenefit['PROPERTY_ITEM_VALUE']]?>
                        <?if(!empty($arItem)):?>
                            <div class="point point_center js-pin-centered" data-item="<?=$arBenefit['ID']?>"></div>

                            <a href="<?=$arItem['DETAIL_PAGE_URL']?>" class="cart-item cart-item_certain cart-item_on-pin js-detail-news-cart" id="news-detail-<?=$arBenefit['ID']?>">
                                <div class="cart-item__image-wrap">
                                    <div class="cart-item__image-vertical-align">
                                        <img class="cart-item__img " src="<?=CFile::GetPath($arItem['PREVIEW_PICTURE'])?>" alt="<?=$arItem['NAME']?>">
                                    </div>
                                </div>
                                <div class="cart-item__text-wrap">
                                    <p class="cart-item__name"><?=$arItem['NAME']?></p>
                                    <p class="cart-item__series"><?=$arItem['PROPERTY_ARTNUMBER_VALUE']?></p>
                                    <div class="cart-item__price-block">
                                        <div class="cart-item__price-wrap">
                                            <p class="cart-item__price">
                                                <?=$arItem['PRICE'][$arParams['PRICE_CODE']]['PRINT_VALUE']?>
                                                <span class="rub"><span>руб.</span></span>
                                            </p>
                                            <?if($arItem['DISCOUNT']):?>
                                                <p class="cart-item__discount"><?=$arItem['DISCOUNT']?>%</p>
                                            <?endif;?>
                                        </div>
                                        <?if($arItem['DISCOUNT']):?>
                                            <p class="cart-item__old-price">
                                                <?=$arItem['OLD_PRICE']?>
                                            </p>
                                        <?endif;?>
                                    </div>
                                </div>
                                <form action="<?=$arItem['ADD_URL']?>" class="cart-item__form js-cart-item__basket-button" data-id="<?=$arItem['ID']?>" data-name="<?=$arItem['NAME']?>" data-list="true">
                                    <button class="cart-item__button cart-item__button_with-icon">
                                        <i class="cart-item__button-icon"></i>
                                        <span class="cart-item__button-span">В корзину</span>
                                    </button>
                                </form>
                            </a>
                        <?endif;?>
                    </div>
                    <div class="news-inner-detail__text-wrap">
                        <div class="news-inner-detail__text-title">
                            <p class="news-inner-detail__text-title-line"><?=$arBenefit['NAME']?></p>
                            <?if($arBenefit['PROPERTY_SUBTITLE_VALUE']):?>
                                <p class="news-inner-detail__text-title-line"><?=$arBenefit['PROPERTY_SUBTITLE_VALUE']?></p>
                            <?endif;?>
                        </div>
                        <ul class="news-inner-detail__text">
                            <?foreach($arBenefit['PROPERTY_BENEFITS_VALUE'] as $benefit):?>
                                <li class="news-inner-detail__text-item">
                                    <?=$benefit['TEXT']?>
                                </li>
                            <?endforeach;?>
                        </ul>
                    </div>
                </div>
            <?endforeach;?>
        </div>
    </div>
<?endif;?>


<? if ($arResult['PROPERTIES']['ADDITIONAL_TEXT']['VALUE']) { ?>
    <div class="news-inner-top news-inner-top__text-block container">
        <p class="news-inner-top__text">
            <?= $arResult['PROPERTIES']['ADDITIONAL_TEXT']['VALUE']["TEXT"] ?>
        </p>
    </div>
<? } ?>






<?if(!empty($arResult['SHOP']) && $arResult['SHOP']['ID']):?>
    <?
    global $arStoreFilter;
    $arStoreFilter['ID'] = array($arResult['SHOP']['ID']);
    $APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "stores_map_news",
        Array(
            "IBLOCK_TYPE" => "content",
            "IBLOCK_ID" => "11",
            "NEWS_COUNT" => "1",

            "SORT_BY1" => "SORT",
            "SORT_ORDER1" => "ASC",
            "SORT_BY2" => "NAME",
            "SORT_ORDER2" => "ASC",

            "FILTER_NAME" => "arStoreFilter",
            "FIELD_CODE" => array(
                0 => "",
                1 => "",
            ),
            "PROPERTY_CODE" => array(
                0 => "ADDRESS",
                1 => "ASSORTMENT",
                2 => "TIMETABLE",
                3 => "TIMETABLE_TEXT",
                4 => "SERVICE_TIMETABLE",
                5 => "SERVICE_PHONE",
                6 => "PHONE",
                7 => "POINT",
                8 => "",
            ),
            "CHECK_DATES" => "Y",
            "IBLOCK_URL" => "/stores/",
            "SECTION_URL" => "/stores/",
            "DETAIL_URL" => "/stores/#ELEMENT_CODE#/",
            "SEARCH_PAGE" => "/stores/",

            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "CACHE_FILTER" => "N",
            "CACHE_GROUPS" => "Y",

            "PREVIEW_TRUNCATE_LEN" => "",
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "SET_TITLE" => "N",
            "SET_BROWSER_TITLE" => "Y",
            "SET_META_KEYWORDS" => "Y",
            "SET_META_DESCRIPTION" => "Y",
            "SHOW_404" => "N",
            "MESSAGE_404" => "",
            "SET_STATUS_404" => "N",
            "SET_LAST_MODIFIED" => "Y",
            "INCLUDE_IBLOCK_INTO_CHAIN" => "N",
            "ADD_SECTIONS_CHAIN" => "N",
            "HIDE_LINK_WHEN_NO_DETAIL" => "N",

            "PARENT_SECTION" => "",
            "PARENT_SECTION_CODE" => "",
            "INCLUDE_SUBSECTIONS" => "Y",

            "DISPLAY_DATE" => "Y",
            "DISPLAY_NAME" => "Y",
            "DISPLAY_PICTURE" => "Y",
            "DISPLAY_PREVIEW_TEXT" => "Y",
            "MEDIA_PROPERTY" => "",
            "SLIDER_PROPERTY" => "",

            "DISPLAY_TOP_PAGER" => "N",
            "DISPLAY_BOTTOM_PAGER" => "Y",
            "PAGER_TITLE" => "Новости",
            "PAGER_SHOW_ALWAYS" => "N",
            "PAGER_TEMPLATE" => "",
            "PAGER_DESC_NUMBERING" => "N",
            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
            "PAGER_SHOW_ALL" => "N",
            "PAGER_BASE_LINK_ENABLE" => "N",

            "TEMPLATE_THEME" => "site",
        ),
        $component
    );?>
<?endif;?>