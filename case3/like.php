<?require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('iblock');

$newId = intVal(htmlspecialchars($_POST['newId']));
$iblockId = intVal(htmlspecialchars($_POST['iblockId']));

if(empty($newId)) die();
if(empty($iblockId)) die();

global $USER;
if ($USER->IsAuthorized()) {

    $arAllRegisteredUsersLogins = array();
    $arAllRegisteredUsersLogins = HelperUsers::getAllRegisteredUsers();


    $arFilter = array(
        "IBLOCK_ID" => $iblockId,
        "ID" => $newId
    );
    $rsElement = CIBlockElement::GetList(
        array(),
        $arFilter,
        false,
        false,
        array("ID", "IBLOCK_ID", "PROPERTY_LIKED_USERS")
    );

    $PROPERTY_LIKED_USERS = array();
    while ($arElement = $rsElement->GetNext()) {
        /** PROPERTY_LIKED_USERS - это множественное свойство, поэтому вместо if
         * используется while */

        $PROPERTY_LIKED_USERS[] = $arElement["PROPERTY_LIKED_USERS_VALUE"];

    }

    sort($PROPERTY_LIKED_USERS);

    $strActionType = "like";
    if (in_array($USER->GetID(), $PROPERTY_LIKED_USERS)) {
        $PROPERTY_LIKED_USERS = array_diff($PROPERTY_LIKED_USERS, array($USER->GetID()));
        $strActionType = "dislike";
    } else {
        $PROPERTY_LIKED_USERS = array_merge($PROPERTY_LIKED_USERS, array($USER->GetID()));
    }

    sort($PROPERTY_LIKED_USERS);

    CIBlockElement::SetPropertyValueCode($newId, "LIKED_USERS", $PROPERTY_LIKED_USERS);

    if (defined('BX_COMP_MANAGED_CACHE')) {
        $GLOBALS['CACHE_MANAGER']->ClearByTag('iblock_id_' . $iblockId);
    }
    $arLikedUsersLogins = array();
    foreach ($PROPERTY_LIKED_USERS as $intLikedUserID) {
        $arLikedUsersLogins[] = $arAllRegisteredUsersLogins[$intLikedUserID];
    }
    $VALUE_LOGINS_STR = implode(", ", $arLikedUsersLogins);

    $result = array(
        'action' => $strActionType,
        'content' => $VALUE_LOGINS_STR
    );



    echo json_encode($result);

}else{
    die();
}
