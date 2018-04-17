<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"]."&amp;" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?".$arResult["NavQueryString"] : "");

if($arResult["NavPageCount"] > 1)
{
?>
    <div class="news-wrap__pagination-container js-pagination-container" data-count-pages="<?=$arResult["NavPageCount"];?>">
        <div class="news-wrap__button-wrap news-wrap__button-show-more">
            <?
            $bFirst = true;

            if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
                ?>
                <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] + 1) ?>"
                   class="btn ajax-link btn_green-border-center"
                   data-title-normal="Показать ещё" data-title-loading="Загружаем..." data-page="<?= ($arResult["NavPageNomer"] + 1) ?>">Показать ещё</a>
                <?
            endif;
            ?>

        </div>

        <div class="news-wrap__pagination-wrap js-pagination-wrap">

            <?

            if ($arResult["NavPageNomer"] > 1):
                if ($arResult["nStartPage"] > 1):
                    $bFirst = false;
                    if ($arResult["bSavePage"]):
                        ?>
                        <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=1"
                           class="btn" data-page="1">1</a>
                        <?
                    else:
                        ?>
                        <a href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>"
                           class="btn btn_green-border" data-page="1">1</a>
                        <?
                    endif;
                    if ($arResult["nStartPage"] > 2):
                        ?>
                        <a class="btn btn_green">...</a>
                        <?
                    endif;
                endif;
            endif;

            do {
                if ($arResult["nStartPage"] == $arResult["NavPageNomer"]):
                    ?>
                    <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["nStartPage"] ?>"
                       class="btn btn_green-border" data-page="<?= $arResult["nStartPage"] ?>"><?= $arResult["nStartPage"] ?></a>
                    <?
                elseif ($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false):
                    ?>
                    <a href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>"
                       class="btn btn_green" data-page="<?= $arResult["nStartPage"] ?>"><?= $arResult["nStartPage"] ?></a>
                    <?
                else:
                    ?>
                    <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["nStartPage"] ?>"
                       class="btn btn_green" data-page="<?= $arResult["nStartPage"] ?>"><?= $arResult["nStartPage"] ?></a>
                    <?
                endif;

                $arResult["nStartPage"]++;
                $bFirst = false;
            } while ($arResult["nStartPage"] <= $arResult["nEndPage"]);

            if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]):
                if ($arResult["nEndPage"] < $arResult["NavPageCount"]):
                    if ($arResult["nEndPage"] < ($arResult["NavPageCount"] - 1)):
                        ?>
                        <a class="btn btn_green">...</a>
                        <?
                    endif;
                    ?>
                    <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageCount"] ?>"
                       class="btn btn_green" data-page="<?= $arResult["NavPageCount"] ?>"><?= $arResult["NavPageCount"] ?></a>
                    <?
                endif;
            endif;
            ?>


        </div>

    </div>

<?
}
?>