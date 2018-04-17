<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$arAllRegisteredUsersLogins = array();
$arAllRegisteredUsersLogins = HelperUsers::getAllRegisteredUsers(); // получаем логины всех зарегистрированных пользователей

foreach($arResult["ITEMS"] as &$arItem){
    $arLikedUsersLogins = array();
    sort($arItem["PROPERTIES"]["LIKED_USERS"]["VALUE"]);
    foreach($arItem["PROPERTIES"]["LIKED_USERS"]["VALUE"] as $intLikedUserID){
        $arLikedUsersLogins[] = $arAllRegisteredUsersLogins[$intLikedUserID];
    }
    $arItem["PROPERTIES"]["LIKED_USERS"]["VALUE_LOGINS_STR"] = implode(", ", $arLikedUsersLogins);
}