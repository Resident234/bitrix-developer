<?php
/**
 * Created by PhpStorm.
 * User: GSU
 * Date: 21.01.2018
 * Time: 16:36
 */

class SearchHelper
{
    public static function reIndexCatalog(){
        CModule::IncludeModule("search");
        $cSearchIndexRowsBefore = self::countSearchIndexRows();
        AddMessage2Log("--- Before reindex - ".$cSearchIndexRowsBefore." rows --- ");

        CSearch::ReIndexAll(true);

        $cSearchIndexRowsAfter = self::countSearchIndexRows();
        AddMessage2Log("--- After reindex - ".$cSearchIndexRowsAfter." rows --- ");

        return 'reIndexCatalog();';
    }

    public static function countSearchIndexRows(){
        global $DB;
        $strSql = "SELECT * FROM b_search_content";
        $db_res = $DB->Query($strSql);

        return $db_res->SelectedRowsCount();
    }

}