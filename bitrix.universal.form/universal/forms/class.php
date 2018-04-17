<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Loader;

if (!Loader::includeModule("iblock"))
{
	ShowError(GetMessage("IBLOCK_MODULE_NOT_INSTALL"));
	return;
}

require_once "UniversalForm.php";

class FormsComponent extends UniversalForm
{
    var $arFields = Array ();
    var $formID = false;
    
    public function onPrepareComponentParams($arParams)
    {
        
        if (!$this->formID)
        {
            $this->formID = Cutil::translit($arParams['NAME'], "ru", Array (
                "change_case" => "U"
            ));
            
        }
           
        
        if (!empty($arParams['FIELDS']))
        {
            
            $arParams['FIELDS'] = @\Bitrix\Main\Web\Json::decode(base64_decode($arParams['FIELDS']), true);

            if (is_array($arParams['FIELDS']))
            {
                
                foreach ($arParams['FIELDS'] as $arField)
                {
                    if (in_array($arField['type'], UniversalForm::$arTypes) && !empty($arField['label']))
                    {
                        $fieldName = (!empty($arField['name'])) ? $arField['name'] : Cutil::translit($arField['label'], "ru", Array (
                            "change_case" => "U"
                        ));
                        
                        if ($arField['type'] == 'select')
                        {
                            $arField['values'] = explode("\n", $arField['values']);
                            foreach ($arField['values'] as &$val)
                                $val = trim($val);
                        }
                        
                        if ($arField['type'] == 'file' && (!isset($arField['maxSize']) || empty($arField['maxSize'])))
                            $arField['maxSize'] = self::$maxFileSize;
                        
                        if ($arField['type'] == 'file' && (!isset($arField['allowTypes']) || empty($arField['allowTypes'])))
                            $arField['allowTypes'] = self::$arDefaultExtensions;    
                        else
                        {
                            $arField['allowTypes'] = explode(";", $arField['allowTypes']);
                            foreach ($arField['allowTypes'] as &$val)
                                $val = trim($val);
                                
                        }

                        $this->arFields[$fieldName] = Array (
                            'name'          => $fieldName,
                            'label'         => $arField['label'],
                            'type'          => $arField['type'],
                            'mask'          => $arField['mask'],
                            'required'      => ($arField['required'] == "Y") ? true : false,
                            'default'       => $arField['default'],
                            'values'        => $arField['values'],
                            'class-overlay' => $arField['class-overlay'],
                            'class'         => $arField['class'],
                            'placeholder'   => $arField['placeholder']
                        );
                        
                        if ($arField['type'] == 'file')
                        {
                            $this->arFields[$fieldName]['allowTypes'] = $arField['allowTypes'];
                            $this->arFields[$fieldName]['maxSize'] = intval($arField['maxSize']);
                        }
                    }
                }
            }
        }
        
        
        
        if (isset($_REQUEST[$this->formID]['is_ajax']))
            $arParams['IS_AJAX'] = 'Y';
        
        return $arParams;
    }
    
    public function executeComponent()
    {
        global $APPLICATION;
        
        if ($this->arParams['IS_AJAX'] == "Y")
            $APPLICATION->RestartBuffer();
        
        try
        {
            
            if (isset($_REQUEST[$this->formID]))
            {
                // submit
                $this->Send($_REQUEST[$this->formID]);
            }
            

            foreach ($this->arFields as $key => $arField)
                $this->arFields[$key] = $this->GetHtml($arField);

            
        }
        catch (Exception $e)
        {
            ShowError($e->getMessage());
        }
        
        $this->arResult['FORM_ID'] = $this->formID;
        $this->arResult['FIELDS'] = $this->arFields;
        $this->arResult['AJAX_FIELD'] = $this->GetAjaxField();
        
        $this->includeComponentTemplate();
        
        if ($this->arParams['IS_AJAX'] == "Y")
            exit();
    }
}