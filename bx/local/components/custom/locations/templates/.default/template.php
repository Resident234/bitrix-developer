<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?
use Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
$this->setFrameMode(true);
?>
<?=$arResult["CURRENT_LOCATION_FORMATTED"];?>
<hr>
<br>
<?=$arResult['UsersFromCurrentCityList'];?>