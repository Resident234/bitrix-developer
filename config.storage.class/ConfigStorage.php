<?php

/**
 * Class ConfigStorage
 */
class ConfigStorage
{
	/**
	 * key-value хранилище различных конфигурационных констант
	 *
	 * @var array
	 */
	private static $config = array();

	/**
	 * массив с маппингом полей для вывода свойств в зависимости от языка
	 *
	 * @var array
	 */
	private static $translateConfig = array();

	/**
	 * Инициализирует конфигурацию и перекрывающие значения для конкретного сайта
	 *
	 * @param $path2config
	 * @param $siteID
     */
	public static function initConfigForSite($path2config, $siteID)
	{
		if(is_dir($path2config)) {

			$dictionary = array(
				's1' => 'pravpunkt'
			);
			
			if(!empty($dictionary[$siteID])){
				$arConfigFiles = array(
					$path2config . DIRECTORY_SEPARATOR . $dictionary[$siteID] . '.php',
					$path2config . DIRECTORY_SEPARATOR . $dictionary[$siteID] . '_override.php',
				);

				foreach ($arConfigFiles as $configFile) {
					if (file_exists($configFile)) {
						include_once $configFile;
					}
				}
			}
			
		}
	}

	/**
	 * Устанавливает список конфигурационных параметров
	 *
	 * @param array $config
	 */
	public static function setConfiguration(array $config)
	{
		self::$config = $config;
	}

	/**
	 * Возвращает параметр конфигурации
	 *
	 * @param string $key
	 * @return mixed
	 */
	public static function getParam($key, $key2 = false)
	{
		if (!$key2) {
			return @self::$config[$key];
		} else {
			if ($newKey = self::$config[$key2]) {
				return @self::$config[$key][$newKey];
			}
			return @self::$config[$key][$key2];
		}
	}

	/**
	 * Устанавливает параметр конфигурации
	 *
	 * @param string $key
	 * @param $value
	 */
	public static function setParam($key, $value)
	{
		self::$config[$key] = $value;
	}

	public static function getTranslateConfig()
	{
		if(empty(self::$translateConfig) && is_file(self::getParam('translateConfigFile'))){
			self::$translateConfig = include(self::getParam('translateConfigFile'));
		}
		return self::$translateConfig;
	}
}
