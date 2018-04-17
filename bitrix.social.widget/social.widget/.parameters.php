<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main;
use Bitrix\Main\Localization\Loc as Loc;

Loc::loadMessages(__FILE__); 

try
{

	$arComponentParameters = array(
		"GROUPS" => array(
			"VK" => array(
				"NAME" => Loc::getMessage('NAME_GROUP_VK'),
			),
			"OK" => array(
				"NAME" => Loc::getMessage('NAME_GROUP_OK'),
			),
			"instagram" => array(
				"NAME" => Loc::getMessage('NAME_GROUP_INSTAGRAM'),
			),
			"facebook" => array(
				"NAME" => Loc::getMessage('NAME_GROUP_FACEBOOK'),
			),
			"youtube" => array(
				"NAME" => Loc::getMessage('NAME_GROUP_YOUTUBE'),
			),
		),
		'PARAMETERS' => array(
			'GROUP_VK' => Array(
				'PARENT' => 'VK',
				'NAME' => Loc::getMessage('GROUP_ID'),
				'TYPE' => 'STRING',
				"MULTIPLE" => "N",
				"DEFAULT" => "",
				"COLS" => 25
			),
			'WIDTH_VK' => Array(
				'PARENT' => 'VK',
				'NAME' => Loc::getMessage('WIDTH_WIDGET'),
				'TYPE' => 'NUMBER',
				"MULTIPLE" => "N",
				"DEFAULT" => ""
			),
			'HEIGHT_VK' => Array(
				'PARENT' => 'VK',
				'NAME' => Loc::getMessage('HEIGHT_WIDGET'),
				'TYPE' => 'NUMBER',
				"MULTIPLE" => "N",
				"DEFAULT" => ""
			),
            'HREF_VK' => Array(
                'PARENT' => 'VK',
                'NAME' => Loc::getMessage('HREF_GROUP'),
                'TYPE' => 'STRING',
                "MULTIPLE" => "N",
                "DEFAULT" => ""
            ),

			'GROUP_OK' => Array(
				'PARENT' => 'OK',
				'NAME' => Loc::getMessage('GROUP_ID'),
				'TYPE' => 'STRING',
				"MULTIPLE" => "N",
				"DEFAULT" => "",
				"COLS" => 25
			),
			'WIDTH_OK' => Array(
				'PARENT' => 'OK',
				'NAME' => Loc::getMessage('WIDTH_WIDGET'),
				'TYPE' => 'NUMBER',
				"MULTIPLE" => "N",
				"DEFAULT" => ""
			),
			'HEIGHT_OK' => Array(
				'PARENT' => 'OK',
				'NAME' => Loc::getMessage('HEIGHT_WIDGET'),
				'TYPE' => 'NUMBER',
				"MULTIPLE" => "N",
				"DEFAULT" => ""
			),
            'HREF_OK' => Array(
                'PARENT' => 'OK',
                'NAME' => Loc::getMessage('HREF_GROUP'),
                'TYPE' => 'STRING',
                "MULTIPLE" => "N",
                "DEFAULT" => ""
            ),

			'LOGIN_INSTAGRAM' => Array(
				'PARENT' => 'instagram',
				'NAME' => Loc::getMessage('LOGIN_INSTAGRAM'),
				'TYPE' => 'STRING',
				"MULTIPLE" => "N",
				"DEFAULT" => "",
				"COLS" => 25
			),
			'TOKEN_INSTAGRAM' => Array(
				'PARENT' => 'instagram',
				'NAME' => Loc::getMessage('TOKEN_INSTAGRAM'),
				'TYPE' => 'STRING',
				"MULTIPLE" => "N",
				"DEFAULT" => "",
				"COLS" => 25
			),
			'WIDTH_INSTAGRAM' => Array(
				'PARENT' => 'instagram',
				'NAME' => Loc::getMessage('WIDTH_WIDGET'),
				'TYPE' => 'NUMBER',
				"MULTIPLE" => "N",
				"DEFAULT" => ""
			),
			'HEIGHT_INSTAGRAM' => Array(
				'PARENT' => 'instagram',
				'NAME' => Loc::getMessage('HEIGHT_WIDGET'),
				'TYPE' => 'NUMBER',
				"MULTIPLE" => "N",
				"DEFAULT" => ""
			),
            'HREF_INSTAGRAM' => Array(
                'PARENT' => 'instagram',
                'NAME' => Loc::getMessage('HREF_GROUP'),
                'TYPE' => 'STRING',
                "MULTIPLE" => "N",
                "DEFAULT" => ""
            ),

			'GROUP_FACEBOOK' => Array(
				'PARENT' => 'facebook',
				'NAME' => Loc::getMessage('LINK_GROUP_FACEBOOK'),
				'TYPE' => 'STRING',
				"MULTIPLE" => "N",
				"DEFAULT" => "",
				"COLS" => 25
			),
			'WIDTH_FACEBOOK' => Array(
				'PARENT' => 'facebook',
				'NAME' => Loc::getMessage('WIDTH_WIDGET'),
				'TYPE' => 'NUMBER',
				"MULTIPLE" => "N",
				"DEFAULT" => ""
			),
			'HEIGHT_FACEBOOK' => Array(
				'PARENT' => 'facebook',
				'NAME' => Loc::getMessage('HEIGHT_WIDGET'),
				'TYPE' => 'NUMBER',
				"MULTIPLE" => "N",
				"DEFAULT" => ""
			),
            'HREF_FACEBOOK' => Array(
                'PARENT' => 'facebook',
                'NAME' => Loc::getMessage('HREF_GROUP'),
                'TYPE' => 'STRING',
                "MULTIPLE" => "N",
                "DEFAULT" => ""
            ),

			'CHANNEL_YOUTUBE' => Array(
				'PARENT' => 'youtube',
				'NAME' => Loc::getMessage('ID_CHANNEL_YOUTUBE'),
				'TYPE' => 'STRING',
				"MULTIPLE" => "N",
				"DEFAULT" => "",
				"COLS" => 25
			),
			'WIDTH_YOUTUBE' => Array(
				'PARENT' => 'youtube',
				'NAME' => Loc::getMessage('WIDTH_WIDGET'),
				'TYPE' => 'NUMBER',
				"MULTIPLE" => "N",
				"DEFAULT" => ""
			),
			'HEIGHT_YOUTUBE' => Array(
				'PARENT' => 'youtube',
				'NAME' => Loc::getMessage('HEIGHT_WIDGET'),
				'TYPE' => 'NUMBER',
				"MULTIPLE" => "N",
				"DEFAULT" => ""
			),
            'HREF_YOUTUBE' => Array(
                'PARENT' => 'youtube',
                'NAME' => Loc::getMessage('HREF_GROUP'),
                'TYPE' => 'STRING',
                "MULTIPLE" => "N",
                "DEFAULT" => ""
            ),

			'CACHE_TIME' => array(
				'DEFAULT' => 3600
			)
		)
	);
}
catch (Main\LoaderException $e)
{
	ShowError($e->getMessage());
}
?>