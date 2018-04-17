<?php

class HelperUsers
{

    /**
     * @return mixed
     */
    public static function getAllRegisteredUsers()
    {
        $arAllRegisteredUsersLogins = array();

        $cache = new CPHPCache();
        $cache_time = 86400;
        $cache_id = 'arAllRegisteredUsersLogins_' . SITE_ID;
        $cache_path = '/arAllRegisteredUsersLogins/';

        if ($cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path)) {
            $res = $cache->GetVars();
            if (is_array($res["arAllRegisteredUsersLogins"]) &&
                (count($res["arAllRegisteredUsersLogins"]) > 0))
                $arAllRegisteredUsersLogins = $res["arAllRegisteredUsersLogins"];
        }

        if (empty($arAllRegisteredUsersLogins)) {

            $order = array('sort' => 'asc');
            $tmp = 'sort';
            $rsUsers = CUser::GetList($order, $tmp);

            global $CACHE_MANAGER;
            $CACHE_MANAGER->StartTagCache($cache_path);

            while ($arrUser = $rsUsers->GetNext()) {
                $arAllRegisteredUsersLogins[$arrUser["ID"]] = $arrUser["LOGIN"];
                $CACHE_MANAGER->RegisterTag("RegisteredUser_" . $arrUser["ID"] . "_" . $arrUser["LOGIN"]);
            }

            $CACHE_MANAGER->EndTagCache();

            if ($cache_time > 0) {
                $cache->StartDataCache($cache_time, $cache_id, $cache_path);
                $cache->EndDataCache(array("arAllRegisteredUsersLogins" => $arAllRegisteredUsersLogins));
            }

        }

        return $arAllRegisteredUsersLogins;
    }


}