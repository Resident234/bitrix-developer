<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
	$this->setFrameMode(true);
	//dump($_SERVER);
?>

<div class="b-popup__title">Выберите адрес</div>
<div class="b-popup__text b-popup__text_large">

		<?foreach($arResult["ADDRESS_DELIVERY"] as $address):?>
			<p>
				<a href="#" data-company_id="<?=$arParams["COMPANY_ID"]?>" data-id="<?=$address["ID"]?>" class="b-link__popup select_address fancy_close "><?=$address["NAME"]?><a><br />
			</p>
		<?endforeach;?>

</div>

<div class="ajax_path" style="display: none">
	<?=$arResult["AJAX_ADDRESS"]=$componentPath."/ajax.php";?>
</div>
