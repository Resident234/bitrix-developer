<?php

class FacetIndexHelper
{


    /**
     * сброс фасетного индекса
     * @param $iblockId - id инфоблока, индекс которого нужно сбросить
     */
    public static function cleanFacetIndex($iblockId)
    {
        CModule::IncludeModule('iblock');
        Bitrix\Iblock\PropertyIndex\Manager::DeleteIndex($iblockId);
        Bitrix\Iblock\PropertyIndex\Manager::markAsInvalid($iblockId);
    }



}