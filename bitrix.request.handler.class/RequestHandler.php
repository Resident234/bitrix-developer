<?php

namespace GSU\Handler;


/**
 * Class RequestHandler
 */
class RequestHandler
{
	/**
	 * Адрес 404 страницы
	 */
	const PAGE_404 = '/404.php';

	/**
	 * Показывает 404 страницу при необходимости
	 * @global $APPLICATION CMain
	 */
	public static function Show404IfNeeded()
	{

		global $APPLICATION;

		$isRedirectNeeded = !defined('ADMIN_SECTION') && ($APPLICATION->GetCurPage() != self::PAGE_404);
		$isError404 = (defined('ERROR_404')) && (ERROR_404 == 'Y')
			|| (function_exists('http_response_code') && http_response_code() == 404);

		if ($isRedirectNeeded && $isError404 && file_exists($_SERVER['DOCUMENT_ROOT'] . self::PAGE_404)) {
			if (!defined('ERROR_404')) {
				define('ERROR_404', 'Y');
			}

			$APPLICATION->RestartBuffer();

			include($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/header.php');
			include($_SERVER['DOCUMENT_ROOT'] . self::PAGE_404);
			include($_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/footer.php');
		}
	}
}
