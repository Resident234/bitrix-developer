<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

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
            'GROUP_VK' => trim($params['GROUP_VK']),
			'WIDTH_VK' => intval($params['WIDTH_VK']),
			'HEIGHT_VK' => intval($params['HEIGHT_VK']),
            'HREF_VK' => trim($params['HREF_VK']),
			'GROUP_OK' => trim($params['GROUP_OK']),
			'WIDTH_OK' => intval($params['WIDTH_OK']),
			'HEIGHT_OK' => intval($params['HEIGHT_OK']),
            'HREF_OK' => trim($params['HREF_OK']),
			'LOGIN_INSTAGRAM' => trim($params['LOGIN_INSTAGRAM']),
			'TOKEN_INSTAGRAM' => trim($params['TOKEN_INSTAGRAM']),
			'WIDTH_INSTAGRAM' => intval($params['WIDTH_INSTAGRAM']),
			'HEIGHT_INSTAGRAM' => intval($params['HEIGHT_INSTAGRAM']),
            'HREF_INSTAGRAM' => trim($params['HREF_INSTAGRAM']),
			'GROUP_FACEBOOK' => trim($params['GROUP_FACEBOOK']),
			'WIDTH_FACEBOOK' => intval($params['WIDTH_FACEBOOK']),
			'HEIGHT_FACEBOOK' => intval($params['HEIGHT_FACEBOOK']),
            'HREF_FACEBOOK' => trim($params['HREF_FACEBOOK']),
			'CHANNEL_YOUTUBE' => trim($params['CHANNEL_YOUTUBE']),
			'WIDTH_YOUTUBE' => intval($params['WIDTH_YOUTUBE']),
			'HEIGHT_YOUTUBE' => intval($params['HEIGHT_YOUTUBE']),
            'HREF_YOUTUBE' => trim($params['HREF_YOUTUBE'])
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

	}
	
	/**
	 * проверяет заполнение обязательных параметров
	 * @throws SystemException
	 */
	protected function checkParams()
	{
		if ($this->arParams['GROUP_VK'] == "")
			throw new Main\ArgumentNullException('GROUP_VK');

		if ($this->arParams['GROUP_OK'] == "")
			throw new Main\ArgumentNullException('GROUP_OK');

		if ($this->arParams['LOGIN_INSTAGRAM'] == "")
			throw new Main\ArgumentNullException('LOGIN_INSTAGRAM');

		if ($this->arParams['TOKEN_INSTAGRAM'] == "")
			throw new Main\ArgumentNullException('TOKEN_INSTAGRAM');

		if ($this->arParams['GROUP_FACEBOOK'] == "")
			throw new Main\ArgumentNullException('GROUP_FACEBOOK');

		if ($this->arParams['CHANNEL_YOUTUBE'] == "")
			throw new Main\ArgumentNullException('CHANNEL_YOUTUBE');

	}
	
	/**
	 * выполяет действия перед кешированием 
	 */
	protected function executeProlog()
	{

	}


	/**
	 * получение результатов
	 */
	protected function getResult()
	{
		$this->arResult['GROUP_VK']=$this->arParams['GROUP_VK'];
		$this->arResult['WIDTH_VK']=$this->arParams['WIDTH_VK'];
		$this->arResult['HEIGHT_VK']=$this->arParams['HEIGHT_VK'];
        $this->arResult['HREF_VK']=$this->arParams['HREF_VK'];
		$this->arResult['GROUP_OK']=$this->arParams['GROUP_OK'];
		$this->arResult['WIDTH_OK']=$this->arParams['WIDTH_OK'];
		$this->arResult['HEIGHT_OK']=$this->arParams['HEIGHT_OK'];
        $this->arResult['HREF_OK']=$this->arParams['HREF_OK'];
		$this->arResult['LOGIN_INSTAGRAM']=$this->arParams['LOGIN_INSTAGRAM'];
		$this->arResult['TOKEN_INSTAGRAM']=$this->arParams['TOKEN_INSTAGRAM'];
		$this->arResult['WIDTH_INSTAGRAM']=$this->arParams['WIDTH_INSTAGRAM'];
		$this->arResult['HEIGHT_INSTAGRAM']=$this->arParams['HEIGHT_INSTAGRAM'];
        $this->arResult['HREF_INSTAGRAM']=$this->arParams['HREF_INSTAGRAM'];
		$this->arResult['GROUP_FACEBOOK']=$this->arParams['GROUP_FACEBOOK'];
		$this->arResult['WIDTH_FACEBOOK']=$this->arParams['WIDTH_FACEBOOK'];
		$this->arResult['HEIGHT_FACEBOOK']=$this->arParams['HEIGHT_FACEBOOK'];
        $this->arResult['HREF_FACEBOOK']=$this->arParams['HREF_FACEBOOK'];
		$this->arResult['CHANNEL_YOUTUBE']=$this->arParams['CHANNEL_YOUTUBE'];
		$this->arResult['WIDTH_YOUTUBE']=$this->arParams['WIDTH_YOUTUBE'];
		$this->arResult['HEIGHT_YOUTUBE']=$this->arParams['HEIGHT_YOUTUBE'];
        $this->arResult['HREF_YOUTUBE']=$this->arParams['HREF_YOUTUBE'];
	}
	
	/**
	 * выполняет действия после выполения компонента, например установка заголовков из кеша
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
			if ($this->arParams['AJAX'] == 'Y')
				$APPLICATION->RestartBuffer();
			if (!$this->readDataFromCache())
			{
			    $this->getResult();
				$this->putDataToCache();
				$this->includeComponentTemplate();
			}
			$this->executeEpilog();
			if ($this->arParams['AJAX'] == 'Y')
				die();

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