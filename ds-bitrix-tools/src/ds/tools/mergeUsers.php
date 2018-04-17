<?
/** Предварительно должен выполняться каждый час
 * Ищет пользователей с одинаковыми email и объединяет их
 */
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$DUPLICATED_USERS_GROUP = 0;//id группы ппользователей, куда будут перемещаться неактивные дубли
//перед использованием выставить нужное значение

$str = "=============== Запуск " . date("Y-m-d H:i:s") . "==================\n";

if (!CModule::IncludeModule('sale')) {
    return;
}

//Получаем пользователей, группируя по Email
$arBy = "date_register";
$order = "ASC";
$arFilter = array(
    "ACTIVE" => "Y"
);
$arParams = array(
    "FIELDS" => array("ID", "EMAIL")
);
$rsUsers = CUser::GetList($arBy, $order, $arFilter, $arParams);
$arEmails = array();
$arUsersId = array();
while ($arFields = $rsUsers->GetNext()) {

    $arEmails[$arFields["EMAIL"]][] = $arFields["ID"];
    $arUsersId[] = $arFields["ID"];
}

//Получаем пользователей, группируя по ID юзера
$arFilter = array("USER_ID" => $arUsersId);
$arSelect = array("ID", "USER_ID", "USER_EMAIL");
$res = CSaleOrder::GetList(array(), $arFilter, false, false, $arSelect);
$arOrders = array();
while ($arFields = $res->GetNext()) {

    $arOrders[$arFields["USER_ID"]][] = $arFields["ID"];
}


//Идем по ящикам
foreach ($arEmails as $email => $arUsers) {

    //Если у ящика больше одного пользователя
    if (count($arUsers) > 1) {

        $removeList = array();
        $generalUserID = $arUsers[0];
        $str .= "Найдены дубли: \n";

        //Идем по пользователям
        foreach ($arUsers as $key => $userId) {

            $str .= "ИД " . $userId . " ";

            //Если не первый
            if ($key) {

                //Если есть заказы
                if (count($arOrders[$userId])) {

                    //Идем по заказам
                    foreach ($arOrders[$userId] as $orderId) {

                        $arFields = array(
                            "USER_ID" => $generalUserID,
                        );
                        CSaleOrder::Update($orderId, $arFields);
                    }
                }

                //Сделать неактивным, перебросить в группу таких же
                $user = new CUser;
                $fields = Array(
                    "ACTIVE" => "N",

                );
                $user->Update($userId, $fields);

                $arGroups = CUser::GetUserGroup($userId);
                $arGroups[] = $DUPLICATED_USERS_GROUP;
                CUser::SetUserGroup($userId, $arGroups);
            }
        }

        $str .= "\n (email: ".$email.") - объединены в ИД ".$generalUserID;
        $str .= "\n||||||\n";
    }

}
Bitrix\Main\Diag\Debug::writeToFile($str, "", "mergeUsers_" . date("Y-m-d") . ".log");
dump($str);
