<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

    <div class="news-wrap__list ajax-list">
        <? foreach ($arResult["ITEMS"] as $arItem): ?>
            <?
            $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'],
                CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
            $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'],
                CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
                array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

            $img = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width' => 375, 'height' => 370),
                BX_RESIZE_IMAGE_EXACT);
            ?>
            <div class="news-wrap__item js-news-wrap__item ajax-item"
                 id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                <? if(HelperSession::inSession($arItem['ID'], "viewed_news")){ ?>
                    <? include("viewed_new_template.php"); ?>
                <? }else{ ?>
                    <? include("viewed_new.php"); ?>
                <? } ?>
            </div>
        <? endforeach; ?>
    </div>
<?= $arResult['NAV_STRING'] ?>