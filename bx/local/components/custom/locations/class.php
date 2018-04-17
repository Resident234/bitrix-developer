<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

require_once(__DIR__ . '/geo.php');

class StandardElementListComponent extends CBitrixComponent
{
	/**
	 * кешируемые ключи arResult
	 * @var array()
	 */
	protected $cacheKeys = array();
	
	/**
	 * дополнительные параметры, от которых должен зависеть кеш
	 * @var array
	 */
	protected $cacheAddon = array();

	/**
	 * парамтеры постраничной навигации
	 * @var array
	 */
	protected $navParams = array();

    /**
     * вохвращаемые значения
     * @var mixed
     */
	protected $returned;

    /**
     * тегированный кеш
     * @var mixed
     */
    protected $tagCache;

    /**
     * местоположение по умолчанию
     * @var array
     */
    protected $arLocationDefault = array();



    /**
     *
     * @var mixed
     */
    protected $strEntityDataClass;
	
	/**
	 * подключает языковые файлы
	 */
	public function onIncludeComponentLang()
	{
		$this->includeComponentLang(basename(__FILE__));
		Loc::loadMessages(__FILE__);
	}
	
    /**
     * подготавливает входные параметры
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $result = array(
            'HL_ID' => intval($params['HL_ID']),
            'COOKIE_TIME' => !empty(intval($params['COOKIE_TIME'])) ? $params['COOKIE_TIME'] : intval(time() + 60 * 60 * 24 * 365),
            'COUNT' => intval($params['COUNT']) > 0 ? intval($params['COUNT']) : 1000,
            'SORT_FIELD' => strlen($params['SORT_FIELD']) ? $params['SORT_FIELD'] : 'ID',
            'SORT_DIRECTION' => $params['SORT_DIRECTION'] == 'ASC' ? 'ASC' : 'DESC',
            'CACHE_TIME' => intval($params['CACHE_TIME']) > 0 ? intval($params['CACHE_TIME']) : 3600,
        );
        return $result;
    }
	
	/**
	 * определяет читать данные из кеша или нет
	 * @return bool
	 */
	protected function readDataFromCache()
	{
		global $USER;
		if ($this->arParams['CACHE_TYPE'] == 'N')
			return false;

		if (is_array($this->cacheAddon))
			$this->cacheAddon[] = $USER->GetUserGroupArray();
		else
			$this->cacheAddon = array($USER->GetUserGroupArray());

        $this->cacheAddon[] = $this->arResult['CURRENT_CITY_HASH'];

		return !($this->startResultCache(false, $this->cacheAddon, md5(serialize($this->arParams))));
	}

	/**
	 * кеширует ключи массива arResult
	 */
	protected function putDataToCache()
	{
		if (is_array($this->cacheKeys) && sizeof($this->cacheKeys) > 0)
		{
			$this->SetResultCacheKeys($this->cacheKeys);
		}
	}

	/**
	 * прерывает кеширование
	 */
	protected function abortDataCache()
	{
		$this->AbortResultCache();
	}

    /**
     * завершает кеширование
     * @return bool
     */
    protected function endCache()
    {
        if ($this->arParams['CACHE_TYPE'] == 'N')
            return false;

        $this->endResultCache();
    }
	
	/**
	 * проверяет подключение необходиимых модулей
	 * @throws LoaderException
	 */
	protected function checkModules()
	{
		if (!Main\Loader::includeModule('iblock'))
			throw new Main\LoaderException(Loc::getMessage('LOCATION_CLASS_IBLOCK_MODULE_NOT_INSTALLED'));

        if (!Main\Loader::includeModule('main'))
            throw new Main\LoaderException(Loc::getMessage('LOCATION_CLASS_MAIN_MODULE_NOT_INSTALLED'));

        if (!Main\Loader::includeModule('highloadblock'))
            throw new Main\LoaderException(Loc::getMessage('LOCATION_CLASS_HL_MODULE_NOT_INSTALLED'));

        if (!Main\Loader::includeModule('sale'))
            throw new Main\LoaderException(Loc::getMessage('LOCATION_CLASS_SALE_MODULE_NOT_INSTALLED'));
	}
	
	/**
	 * проверяет заполнение обязательных параметров
	 * @throws SystemException
	 */
	protected function checkParams()
	{
		if ($this->arParams['HL_ID'] <= 0)
			throw new Main\ArgumentNullException('HL_ID');
	}
	
	/**
	 * выполяет действия перед кешированием 
	 */
	protected function executeProlog()
	{

	}


	/**
	 * получение текущего местоположения пользоввателя
	 */
	protected function getCurrentLocation()
	{

        $arLocationDefault = array('CURRENT_CITY_HASH' => md5(
            implode("_", array(
            "Центральный федеральный округ", "Москва", "Москва"
        ))),
            'CURRENT_LOCATION_FORMATTED' => "RU, Центральный федеральный округ, Москва, Москва, 55.755787 - 37.617634",
        );

        global $APPLICATION;

        if (!$_SESSION['GEO']['CURRENT_CITY_HASH'] || !$_SESSION['GEO']['CURRENT_LOCATION_FORMATTED']) {
            if ($APPLICATION->get_cookie('CURRENT_CITY_HASH') &&
                $APPLICATION->get_cookie('CURRENT_LOCATION_FORMATTED')) {

                $_SESSION['GEO']['CURRENT_CITY_HASH'] = $APPLICATION->get_cookie('CURRENT_CITY_HASH');
                $_SESSION['GEO']['CURRENT_LOCATION_FORMATTED'] = $APPLICATION->get_cookie('CURRENT_LOCATION_FORMATTED');

                $this->arResult['CURRENT_CITY_HASH'] = $APPLICATION->get_cookie('CURRENT_CITY_HASH');
                $this->arResult['CURRENT_LOCATION_FORMATTED'] = $APPLICATION->get_cookie('CURRENT_LOCATION_FORMATTED');

            } else {
                $geo = new Geo();
                $geo_data = $geo->get_geobase_data();

                $arCurrentLocationFormatted = array();
                $arCurrentCoordinates = array();

                if (!empty($geo_data['country'])) $arCurrentLocationFormatted[] = $geo_data['country'];
                if (!empty($geo_data['district'])) $arCurrentLocationFormatted[] = $geo_data['district'];
                if (!empty($geo_data['region'])) $arCurrentLocationFormatted[] = $geo_data['region'];
                if (!empty($geo_data['city'])) $arCurrentLocationFormatted[] = $geo_data['city'];

                if (!empty($geo_data['lat'])) $arCurrentCoordinates[] = $geo_data['lat'];
                if (!empty($geo_data['lng'])) $arCurrentCoordinates[] = $geo_data['lng'];

                if (!empty($arCurrentCoordinates)) $arCurrentLocationFormatted[] = implode(" - ", $arCurrentCoordinates);

                $this->arResult['CURRENT_CITY_HASH'] = md5(implode("_", array($geo_data['district'], $geo_data['region'], $geo_data['city'])));
                $this->arResult['CURRENT_LOCATION_FORMATTED'] = implode(", ", $arCurrentLocationFormatted);

                if(empty($this->arResult['CURRENT_CITY_HASH'])) $this->arResult['CURRENT_CITY_HASH'] = $arLocationDefault['CURRENT_CITY_HASH'];
                if(empty($this->arResult['CURRENT_LOCATION_FORMATTED'])) $this->arResult['CURRENT_LOCATION_FORMATTED'] = $arLocationDefault['CURRENT_LOCATION_FORMATTED'];

                $_SESSION['GEO']['CURRENT_CITY_HASH'] = $this->arResult['CURRENT_CITY_HASH'];
                $_SESSION['GEO']['CURRENT_LOCATION_FORMATTED'] = $this->arResult['CURRENT_LOCATION_FORMATTED'];

                $APPLICATION->set_cookie("CURRENT_CITY_HASH", $this->arResult['CURRENT_CITY_HASH'], $this->arParams["COOKIE_TIME"]);
                $APPLICATION->set_cookie("CURRENT_LOCATION_FORMATTED", $this->arResult['CURRENT_LOCATION_FORMATTED'], $this->arParams["COOKIE_TIME"]);

            }
        }else{
            $this->arResult['CURRENT_CITY_HASH'] = $_SESSION['GEO']['CURRENT_CITY_HASH'];
            $this->arResult['CURRENT_LOCATION_FORMATTED'] = $_SESSION['GEO']['CURRENT_LOCATION_FORMATTED'];
        }




	}

    /**
     * получение id текущего пользователя
     */
    protected function getCurrentUserData()
    {
        global $USER;
        $arUserInfo = array();

        if ($USER->IsAuthorized()){
            $currentUserID = $USER->GetID();
            $arUserInfo["ID"] = $currentUserID;
            $arUserInfo["LOGIN"] = $USER->GetLogin();
            $arUserInfo["NAME"] = $USER->GetFirstName();
            $arUserInfo["LAST_NAME"] = $USER->GetLastName();
        }else{
            $currentFUserID = CSaleBasket::GetBasketUserID();
            //FUSER_ID - это не одно и тоже, что USER_ID

            $res = Bitrix\Main\UserTable::getList(Array(
                "select"=>Array("ID", "UF_*", "NAME", "LAST_NAME", "LOGIN"),
                "filter"=>Array("=UF_FUSER_ID" => $currentFUserID),
            ));
            if ($arUser = $res->fetch()) {
                /** юзер уже здесь был, достаём его данные */

                $currentUserID = $arUser['ID'];
                $arUserInfo["ID"] = $currentUserID;
                $arUserInfo["LOGIN"] = $arUser['LOGIN'];
                $arUserInfo["NAME"] = $arUser['NAME'];
                $arUserInfo["LAST_NAME"] = $arUser['LAST_NAME'];

            }else{
                /** первый заход, регитрируем */

                $user = new CUser;
                $intRandUserNumber = rand(0, PHP_INT_MAX);
                $intRandUserPassword = rand(0, PHP_INT_MAX);

                $arFields = Array(
                    "NAME" => "User" . $intRandUserNumber,
                    "LAST_NAME" => "Anonymous" . $intRandUserNumber,
                    "EMAIL" => "Anonymous" . $intRandUserNumber . "@mail.ru",
                    "LOGIN" => "login" . $intRandUserNumber,
                    "LID" => "ru",
                    "ACTIVE" => "Y",
                    "GROUP_ID" => array(3),
                    "PASSWORD" => $intRandUserPassword,
                    "CONFIRM_PASSWORD" => $intRandUserPassword,
                    "UF_FUSER_ID" => $currentFUserID

                );

                $arUserInfo["LOGIN"] = $arFields['LOGIN'];
                $arUserInfo["NAME"] = $arFields['NAME'];
                $arUserInfo["LAST_NAME"] = $arFields['LAST_NAME'];

                $currentUserID = $user->Add($arFields);
                $arUserInfo["ID"] = $currentUserID;
            }
        }

        return $arUserInfo;

    }

    /**
     * получение сущности HL блока
     */
    protected function getHLEntityDataClass($HL_ID){
        $arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById($HL_ID)->fetch();
        $obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();
        $this->strEntityDataClass = $strEntityDataClass;
    }

    /**
     * добавление данных о текущем пользователе в HL влок
     */
    protected function addUserDataToHLblock(){
        $arCurrentUserData = $this->getCurrentUserData();

        $strEntityDataClass = $this->strEntityDataClass;

        $rsData = $strEntityDataClass::getList(array(
            'select' => array("ID", 'UF_ID'),
            'order' => array($this->arParams['SORT_FIELD'] => $this->arParams['SORT_DIRECTION']),
            'limit' => '1',
            'filter' => array('UF_ID' => $arCurrentUserData["ID"]),
        ));
        if ($arData = $rsData->Fetch()) {
            //обновить LAST_LOCATION_F
            $data = array(
                "UF_LAST_LOCATION_F" => $this->arResult['CURRENT_LOCATION_FORMATTED'],
                "UF_CITY_HASH" => $this->arResult['CURRENT_CITY_HASH']
            );
            $strEntityDataClass::update($arData["ID"], $data);
        }else{
            //добавляем пользователя
            $data = array(
                "UF_ID"=> $arCurrentUserData["ID"],
                "UF_LOGIN"=> $arCurrentUserData["LOGIN"],
                "UF_NAME"=> $arCurrentUserData["NAME"],
                "UF_LAST_NAME"=> $arCurrentUserData["LAST_NAME"],
                "UF_LAST_LOCATION_F" => $this->arResult['CURRENT_LOCATION_FORMATTED'],
                "UF_CITY_HASH" => $this->arResult['CURRENT_CITY_HASH']
            );
            $strEntityDataClass::add($data);
        }
    }


    /**
     * получение пользователей из текущего города
     */
    protected function getUsersFromCurrentCity()
    {
        $strEntityDataClass = $this->strEntityDataClass;

        $rsData = $strEntityDataClass::getList(array(
            'select' => array("ID", "UF_ID", "UF_LOGIN", "UF_NAME", "UF_LAST_NAME", "UF_LAST_LOCATION_F"),
            'order' => array('ID' => 'ASC'),
            'limit' => $this->arParams["COUNT"],
            'filter' => array('UF_CITY_HASH' => $this->arResult['CURRENT_CITY_HASH']),
        ));

        $currentUserData = $this->getCurrentUserData();


        $arUsersFromCurrentCityList = array();
        while ($arData = $rsData->Fetch()) {
            if($currentUserData["ID"] == $arData["UF_ID"]) continue;
            $arUser = array();
            if(!empty($arData["UF_LOGIN"])) $arUser[] = "<b>Логин:</b> " . $arData["UF_LOGIN"] . "<br>";
            if(!empty($arData["UF_NAME"])) $arUser[] = "<b>Имя:</b> " . $arData["UF_NAME"] . "<br>";
            if(!empty($arData["UF_LAST_NAME"])) $arUser[] = "<b>Фамилия:</b> " . $arData["UF_LAST_NAME"] . "<br>";
            if(!empty($arData["UF_LAST_LOCATION_F"])) $arUser[] = "<b>Последнее местоположение:</b> " . $arData["UF_LAST_LOCATION_F"] . "<br><hr>";
            $strUserInfo = implode("", $arUser);
            $arUsersFromCurrentCityList[] = $strUserInfo;
        }

        $strUsersFromCurrentCityList = implode("", $arUsersFromCurrentCityList);
        $this->arResult['UsersFromCurrentCityList'] = $strUsersFromCurrentCityList;


    }

	/**
	 * выполняет действия после выполения компонента
	 */
	protected function executeEpilog()
	{

	}
	
	/**
	 * выполняет логику работы компонента
	 */
	public function executeComponent()
	{
		global $APPLICATION;
		try
		{
			$this->checkModules();
			$this->checkParams();
			$this->executeProlog();

            $this->getHLEntityDataClass($this->arParams['HL_ID']);
            $this->getCurrentLocation();
            $this->addUserDataToHLblock();

            if (!$this->readDataFromCache())
			{
                $this->getUsersFromCurrentCity();
                $this->putDataToCache();
				$this->includeComponentTemplate();
			}
			$this->executeEpilog();


			return $this->returned;
		}
		catch (Exception $e)
		{
			$this->abortDataCache();
			ShowError($e->getMessage());
		}
	}
}
?>