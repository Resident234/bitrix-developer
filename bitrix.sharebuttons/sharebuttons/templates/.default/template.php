<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
//$this->addExternalJS(($this->GetPath())."/js/script.js");


$APPLICATION->AddHeadScript($componentPath . "/js/script.js");
$APPLICATION->SetAdditionalCSS($componentPath . "/css/style.css");



?>



<?



foreach ($arResult["OFFERS"] as $k => $v) {


	$previewPictureSocial="";

	if(!empty($v["PREVIEW_PICTURE"])) {
		$PREVIEW_PICTURE=CFile::GetFileArray($v["PREVIEW_PICTURE"]["ID"]);
		$previewPictureSocial = $PREVIEW_PICTURE['SRC'];

	}else if(!empty($v["DETAIL_PICTURE"])) {
		$PREVIEW_PICTURE=CFile::GetFileArray($v["DETAIL_PICTURE"]["ID"]);
		$previewPictureSocial = $PREVIEW_PICTURE['SRC'];

	}else if(!empty($v["PROPERTIES"]["MORE_PHOTO"]["VALUE"])) {
		$PREVIEW_PICTURE=CFile::GetFileArray($v["PROPERTIES"]["MORE_PHOTO"]["VALUE"][0]);
		$previewPictureSocial = $PREVIEW_PICTURE['SRC'];

	}
	$protocol = (CMain::IsHTTPS()) ? "https://" : "http://";
	if(!empty($previewPictureSocial)){

		$previewPictureSocial=$protocol.$_SERVER["HTTP_HOST"].$previewPictureSocial;

	}



	?>


	<input type="hidden" class="js-social-offer" id="social-offer_<?= $v["ID"]; ?>"
	data-title="<?=$v["NAME"];?>"
	data-url="https://<?=$_SERVER['SERVER_NAME'].$v["DETAIL_PAGE_URL"];?>"
	data-image="<?=$previewPictureSocial;?>"/>

	<div class="hidden js-footer__socials-item-container js-footer__socials-item-container-offer_<?= $v["ID"]; ?>"
		 style="display:inline-block;">

		<? if ($arResult["FB_USE"]): ?>
			<div style="display: none;">
				<img src="<?=$previewPictureSocial;?>" alt="">
			</div>
			<a href="#"
			   onclick="event.preventDefault(); Share.facebook(
				   'https://<?=$_SERVER['SERVER_NAME'].$v["DETAIL_PAGE_URL"];?>',
				   '<?=$v["NAME"];?>',
				   '<?= $previewPictureSocial ?>',
				   '<?=strip_tags($v["PREVIEW_TEXT_SOCIAL"]); ?>')"
			   class="b-footer__socials-item _fb _medium-size "
			></a>
			<input type="hidden" data-current-lang="<?=Helper::getSiteVersionByIp();?>"/>

 
		<? endif; ?>
		<? if ($arResult["VK_USE"]): ?>
			<a href="#"
			   onclick="event.preventDefault(); event.preventDefault(); Share.vkontakte(
				   '<?/* $arResult["URL_TO_LIKE"]*/ ?>https://<?=$_SERVER['SERVER_NAME'].str_replace("&clear_cache=Y","",$v["DETAIL_PAGE_URL"]);?>',
				   '<?=$arResult["TITLE"]/*$v["NAME"];*/?>',
				   '<?/*=$arResult["IMAGE"]*//*."===".$previewPictureSocial*/ ?>',
				   '<?=$arResult["DESCRIPTION"] //strip_tags($v["PREVIEW_TEXT_SOCIAL"]); ?>')"
			   class="b-footer__socials-item _vk _medium-size "
			></a>
		<? endif; ?>


		<? if ($arResult["OK_USE"]): ?>
			<a href="#"
			   onclick="event.preventDefault(); Share.odnoklassniki(
				   'https://<?=$_SERVER['SERVER_NAME'].$v["DETAIL_PAGE_URL"];?>',
				   '<?=$v["NAME"];?>',
				   '<?= $previewPictureSocial ?>',
				   '<?= strip_tags($v["PREVIEW_TEXT_SOCIAL"]); ?>')"
			   class="b-footer__socials-item _fb _medium-size "
			></a>
		<? endif; ?>
		<? if ($arResult["MAILRU_USE"]): ?>
			<a href="#"
			   onclick="event.preventDefault(); Share.mailru(
				   'https://<?=$_SERVER['SERVER_NAME'].$v["DETAIL_PAGE_URL"];?>',
				   '<?=$v["NAME"];?>',
				   '<?= $previewPictureSocial ?>',
				   '<?= strip_tags($v["PREVIEW_TEXT_SOCIAL"]); ?>')"
			   class="b-footer__socials-item _fb _medium-size "
			></a>
		<? endif; ?>
		<? if ($arResult["LJ_USE"]): ?>
			<a href="#"
			   onclick="event.preventDefault(); Share.livejournal(
				   'https://<?=$_SERVER['SERVER_NAME'].$v["DETAIL_PAGE_URL"];?>',
				   '<?=$v["NAME"];?>',
				   '<?= $previewPictureSocial ?>',
				   '<?= strip_tags($v["PREVIEW_TEXT_SOCIAL"]); ?>')"
			   class="b-footer__socials-item _fb _medium-size "
			></a>
		<? endif; ?>
		<? if ($arResult["GP_USE"]): ?>
			<a href="#"
			   onclick="event.preventDefault(); Share.googleplus(
				   'https://<?=$_SERVER['SERVER_NAME'].$v["DETAIL_PAGE_URL"];?>',
				   '<?=$v["NAME"];?>',
				   '<?= $previewPictureSocial ?>',
				   '<?= strip_tags($v["PREVIEW_TEXT_SOCIAL"]); ?>')"
			   class="b-footer__socials-item _fb _medium-size "
			></a>
		<? endif; ?>
		<? if ($arResult["PI_USE"]): ?>
			<!--<a href="#"
		   onclick="event.preventDefault(); Share.pinterest(
			   '<?= $arResult["URL_TO_LIKE"] ?>',
			   '<?= $arResult["TITLE"] ?>',
			   '<?= $arResult["IMAGE"] ?>',
			   '<?= $arResult["DESCRIPTION"] ?>')"
		   class="b-footer__socials-item _pi _medium-size "
		></a>-->
			<div style="display: none;">
				<img src="<?=$previewPictureSocial;?>" alt="">
			</div>
			<script type="text/javascript">(function() {
					if (window.pluso)if (typeof window.pluso.start == "function") return;
					if (window.ifpluso==undefined) { window.ifpluso = 1;
						var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
						s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
						s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
						var h=d[g]('body')[0];
						h.appendChild(s);
					}})();</script>
			<div class="pluso b-footer__socials-item _pi _medium-size" data-background="transparent" data-options="big,square,line,horizontal,nocounter,theme=02"
				 data-services="pinterest"></div>


		<? endif; ?>

		<? if ($arResult["TW_USE"]): ?>
			<a href="#"
			   onclick="event.preventDefault(); Share.twitter(
				   'https://<?=$_SERVER['SERVER_NAME'].$v["DETAIL_PAGE_URL"];?>',
				   '<?=$v["NAME"];?>',
				   '<?= $previewPictureSocial ?>',
				   '<?= strip_tags($v["PREVIEW_TEXT_SOCIAL"]); ?>')"
			   class="b-footer__socials-item _tw _medium-size "
			></a>
		<? endif; ?>
	</div>



	<?
}
?>






<div class="js-footer__socials-item-main-container" style="display:inline-block;">


<? if ($arResult["FB_USE"]): ?>
	<div style="display: none;">
		<img src="<?=$arResult["IMAGE"];?>" alt="">
	</div>

		<a href="#"
		   onclick="event.preventDefault(); Share.facebook(
			   '<?= $arResult["URL_TO_LIKE"] ?>',
			   '<?= $arResult["TITLE"] ?>',
			   '<?= $arResult["IMAGE"] ?>',
			   '<?= $arResult["DESCRIPTION"] ?>')"
		   class="b-footer__socials-item _fb _medium-size "
			></a>
		<input type="hidden" data-current-lang="<?=Helper::getSiteVersionByIp();?>"/>


	<? endif; ?>
	<? if ($arResult["VK_USE"]): ?>
		<a href="#"
		   onclick="event.preventDefault(); event.preventDefault(); Share.vkontakte(
			   '<?= str_replace("&clear_cache=Y","",$arResult["URL_TO_LIKE"]) ?>',
			   '<?= $arResult["TITLE"] ?>',
			   '<?//= $arResult["IMAGE"] ?>',
			   '<?= $arResult["DESCRIPTION"] ?>')"
		   class="b-footer__socials-item _vk _medium-size "
			></a>
	<? endif; ?>

	<? if ($arResult["OK_USE"]): ?>
		<a href="#"
		   onclick="event.preventDefault(); Share.odnoklassniki(
			   '<?= $arResult["URL_TO_LIKE"] ?>',
			   '<?= $arResult["TITLE"] ?>',
			   '<?= $arResult["IMAGE"] ?>',
			   '<?= $arResult["DESCRIPTION"] ?>')"
		   class="b-footer__socials-item _fb _medium-size "
			></a>
	<? endif; ?>
	<? if ($arResult["MAILRU_USE"]): ?>
		<a href="#"
		   onclick="event.preventDefault(); Share.mailru(
			   '<?= $arResult["URL_TO_LIKE"] ?>',
			   '<?= $arResult["TITLE"] ?>',
			   '<?= $arResult["IMAGE"] ?>',
			   '<?= $arResult["DESCRIPTION"] ?>')"
		   class="b-footer__socials-item _fb _medium-size "
			></a>
	<? endif; ?>
	<? if ($arResult["LJ_USE"]): ?>
		<a href="#"
		   onclick="event.preventDefault(); Share.livejournal(
			   '<?= $arResult["URL_TO_LIKE"] ?>',
			   '<?= $arResult["TITLE"] ?>',
			   '<?= $arResult["IMAGE"] ?>',
			   '<?= $arResult["DESCRIPTION"] ?>')"
		   class="b-footer__socials-item _fb _medium-size "
			></a>
	<? endif; ?>
	<? if ($arResult["GP_USE"]): ?>
		<a href="#"
		   onclick="event.preventDefault(); Share.googleplus(
			   '<?= $arResult["URL_TO_LIKE"] ?>',
			   '<?= $arResult["TITLE"] ?>',
			   '<?= $arResult["IMAGE"] ?>',
			   '<?= $arResult["DESCRIPTION"] ?>')"
		   class="b-footer__socials-item _fb _medium-size "
			></a>
	<? endif; ?>
	<? if ($arResult["PI_USE"]): ?>
		<!--<a href="#"
		   onclick="event.preventDefault(); Share.pinterest(
			   '<?= $arResult["URL_TO_LIKE"] ?>',
			   '<?= $arResult["TITLE"] ?>',
			   '<?= $arResult["IMAGE"] ?>',
			   '<?= $arResult["DESCRIPTION"] ?>')"
		   class="b-footer__socials-item _pi _medium-size "
		></a>-->
		<div style="display: none;">
			<img src="<?=$arResult["IMAGE"];?>" alt="">
		</div>
		<script type="text/javascript">(function() {
				if (window.pluso)if (typeof window.pluso.start == "function") return;
				if (window.ifpluso==undefined) { window.ifpluso = 1;
					var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
					s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
					s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
					var h=d[g]('body')[0];
					h.appendChild(s);
				}})();</script>
		<div class="pluso b-footer__socials-item _pi _medium-size" data-background="transparent" data-options="big,square,line,horizontal,nocounter,theme=02"
			 data-services="pinterest"></div>
			

	<? endif; ?>

	<? if ($arResult["TW_USE"]): ?>
		<a href="#"
		   onclick="event.preventDefault(); Share.twitter(
			   '<?= $arResult["URL_TO_LIKE"] ?>',
			   '<?= $arResult["TITLE"] ?>',
			   '<?= $arResult["IMAGE"] ?>',
			   '<?= $arResult["DESCRIPTION"] ?>')"
		   class="b-footer__socials-item _tw _medium-size "
		></a>
	<? endif; ?>

</div>