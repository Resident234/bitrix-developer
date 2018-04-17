<?

function getCurCity()
{
    global $APPLICATION;
    if(!$APPLICATION->get_cookie("CITY_ID"))
    {
        set_city_id($_SERVER["REMOTE_ADDR"]);
    }
    if (CModule::IncludeModule('sale')) {

        if($_SESSION["PLACE"][$APPLICATION->get_cookie("CITY_ID")])
            return $_SESSION["PLACE"][$APPLICATION->get_cookie("CITY_ID")];
        else {
            $ID = 0;
            if($APPLICATION->get_cookie("CITY_ID"))
                $ID = $APPLICATION->get_cookie("CITY_ID");
            else if($_SESSION["CITY_ID"])
                $ID = $_SESSION["CITY_ID"];
            else return "Москва";

            $db_vars = CSaleLocation::GetList(array("CITY_NAME_LANG"=>"ASC"), array("LID" => LANGUAGE_ID, "CITY_ID"=>$ID), false, false, array());
            if ($vars = $db_vars->Fetch()) {
                $_SESSION["PLACE"][$APPLICATION->get_cookie("CITY_ID")] = $vars["CITY_NAME"];
                return $vars["CITY_NAME"];
            }
        }
    }

    return "Москва";
}

function set_city_id($ip)
{
    global $APPLICATION;
    $data = file_get_contents_timeout("http://ipgeobase.ru:7020/geo?ip=".$ip);
    if($data){
        $xml = simplexml_load_string($data);
        $city = (string) $xml->ip->city;
        if (CModule::IncludeModule('sale')) {
            $db_vars = CSaleLocation::GetList(array("CITY_NAME_LANG"=>"ASC"), array("LID" => LANGUAGE_ID, "CITY_NAME"=>$city), false, false, array());
            if ($vars = $db_vars->Fetch()) {
                $APPLICATION->set_cookie("CITY_ID", $vars["CITY_ID"], time()+60*60*24*30, "/");
                $_SESSION["CITY_ID"] = $vars["CITY_ID"];
            }
        }
    } else {
        if (CModule::IncludeModule('sale')) {
            $db_vars = CSaleLocation::GetList(array("CITY_NAME_LANG"=>"ASC"), array("LID" => LANGUAGE_ID, "CITY_NAME"=>"Москва"), false, false, array());
            if ($vars = $db_vars->Fetch()) {
                $APPLICATION->set_cookie("CITY_ID", $vars["CITY_ID"], time()+60*60*24*30, "/");
                $_SESSION["CITY_ID"] = $vars["CITY_ID"];
            }
        }
    }
}

function file_get_contents_timeout($filename, $timeout=3)
{
    if(strpos($filename,"://")===false) return file_get_contents($filename);
    if(!function_exists("curl_init")) return false;
    $session=curl_init($filename);
    curl_setopt($session,CURLOPT_MUTE,true);
    curl_setopt($session,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($session,CURLOPT_CONNECTTIMEOUT,$timeout);
    curl_setopt($session,CURLOPT_TIMEOUT,$timeout);
    curl_setopt($session,CURLOPT_USERAGENT,"Mozilla/5.0 (compatible)");
    $result=curl_exec($session);
    curl_close($session);
    return $result;
}

?>