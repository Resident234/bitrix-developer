<?php

if (!CModule::IncludeModule('iblock')) {
    ShowError(GetMessage('IBLOCK_MODULE_NOT_INSTALLED'));
    return;
}
use Bitrix\Main\Localization\Loc;
Loc::loadMessages($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/seo/admin/seo_sitemap.php');
/**
 * Class SitemapHelper
 */
class SitemapHelper
{


    /**
     * ....
     *
     * @param $PID, $arSitemap, $arCurrentDir, $sitemapFile
     */
    public static function seoSitemapGetFilesData($PID, $arSitemap, $arCurrentDir, $sitemapFile)
    {
        global $NS;

        $arDirList = array();

        if ($arCurrentDir['ACTIVE'] == Bitrix\Seo\SitemapRuntimeTable::ACTIVE) {
            $list = \CSeoUtils::getDirStructure(
                $arSitemap['SETTINGS']['logical'] == 'Y',
                $arSitemap['SITE_ID'],
                $arCurrentDir['ITEM_PATH']
            );

            foreach ($list as $dir) {
                $dirKey = "/" . ltrim($dir['DATA']['ABS_PATH'], "/");

                if ($dir['TYPE'] == 'F') {
                    if (!isset($arSitemap['SETTINGS']['FILE'][$dirKey])
                        || $arSitemap['SETTINGS']['FILE'][$dirKey] == 'Y'
                    ) {
                        if (preg_match($arSitemap['SETTINGS']['FILE_MASK_REGEXP'], $dir['FILE'])) {
                            $f = new Bitrix\Main\IO\File($dir['DATA']['PATH'], $arSitemap['SITE_ID']);
                            $sitemapFile->addFileEntry($f);
                            $NS['files_count']++;
                        }
                    }
                } else {
                    if (!isset($arSitemap['SETTINGS']['DIR'][$dirKey])
                        || $arSitemap['SETTINGS']['DIR'][$dirKey] == 'Y'
                    ) {
                        $arDirList[] = $dirKey;
                    }
                }
            }
        } else {
            $len = strlen($arCurrentDir['ITEM_PATH']);
            if (!empty($arSitemap['SETTINGS']['DIR'])) {
                foreach ($arSitemap['SETTINGS']['DIR'] as $dirKey => $checked) {
                    if ($checked == 'Y') {
                        if (strncmp($arCurrentDir['ITEM_PATH'], $dirKey, $len) === 0) {
                            $arDirList[] = $dirKey;
                        }
                    }
                }
            }

            if (!empty($arSitemap['SETTINGS']['FILE'])) {
                foreach ($arSitemap['SETTINGS']['FILE'] as $dirKey => $checked) {
                    if ($checked == 'Y') {
                        if (strncmp($arCurrentDir['ITEM_PATH'], $dirKey, $len) === 0) {
                            $fileName = Bitrix\Main\IO\Path::combine(
                                SiteTable::getDocumentRoot($arSitemap['SITE_ID']),
                                $dirKey
                            );

                            if (!is_dir($fileName)) {
                                $f = new Bitrix\Main\IO\File($fileName, $arSitemap['SITE_ID']);
                                if ($f->isExists()
                                    && !$f->isSystem()
                                    && preg_match($arSitemap['SETTINGS']['FILE_MASK_REGEXP'], $f->getName())
                                ) {
                                    $sitemapFile->addFileEntry($f);
                                    $NS['files_count']++;
                                }
                            }
                        }
                    }
                }
            }
        }

        if (count($arDirList) > 0) {
            foreach ($arDirList as $dirKey) {
                $arRuntimeData = array(
                    'PID' => $PID,
                    'ITEM_PATH' => $dirKey,
                    'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::UNPROCESSED,
                    'ACTIVE' => Bitrix\Seo\SitemapRuntimeTable::ACTIVE,
                    'ITEM_TYPE' => Bitrix\Seo\SitemapRuntimeTable::ITEM_TYPE_DIR,
                );
                Bitrix\Seo\SitemapRuntimeTable::add($arRuntimeData);
            }
        }

        Bitrix\Seo\SitemapRuntimeTable::update($arCurrentDir['ID'], array(
            'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::PROCESSED
        ));
    }

    /**
     * Генерирует sitemap.xml
     *
     * @param $lang, $action, $ID, $value, $pid, $NS, $sessid
     */
    public static function run($lang, $action, $ID, $value, $pid, $NS, $sessid) {


        $bIBlock = Bitrix\Main\Loader::includeModule('iblock');

        //$ID = intval($_REQUEST['ID']);
        $NS = isset($NS) && is_array($NS) ? $NS : array();

        $arSitemap = null;
        if ($ID > 0) {
            $dbSitemap = Bitrix\Seo\SitemapTable::getById($ID);
            $arSitemap = $dbSitemap->fetch();

            $dbSite = Bitrix\Main\SiteTable::getByPrimary($arSitemap['SITE_ID']);
            $arSitemap['SITE'] = $dbSite->fetch();
        }

        if (!is_array($arSitemap)) {
            require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
            ShowError(Loc::getMessage("SEO_ERROR_SITEMAP_NOT_FOUND"));
            require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
        } else {
            $arSitemap['SETTINGS'] = unserialize($arSitemap['SETTINGS']);

            $arSitemapSettings = array(
                'SITE_ID' => $arSitemap['SITE_ID'],
                'PROTOCOL' => $arSitemap['SETTINGS']['PROTO'] == 1 ? 'https' : 'http',
                'DOMAIN' => $arSitemap['SETTINGS']['DOMAIN'],
            );
        }

        if ($action == 'sitemap_run') {
            $arValueSteps = array(
                'init' => 0,
                'files' => 40,
                'iblock_index' => 50,
                'iblock' => 60,
                'forum_index' => 70,
                'forum' => 80,
                'index' => 100,
            );

            $v = $value;

            $PID = $ID;

            if ($v == $arValueSteps['init']) {
                Bitrix\Seo\SitemapRuntimeTable::clearByPid($PID);

                $NS['time_start'] = microtime(true);
                $NS['files_count'] = 0;
                $NS['steps_count'] = 0;

                $bRootChecked = isset($arSitemap['SETTINGS']['DIR']['/'])
                    && $arSitemap['SETTINGS']['DIR']['/'] == 'Y';

                $arRuntimeData = array(
                    'PID' => $PID,
                    'ITEM_TYPE' => Bitrix\Seo\SitemapRuntimeTable::ITEM_TYPE_DIR,
                    'ITEM_PATH' => '/',
                    'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::UNPROCESSED,
                    'ACTIVE' => $bRootChecked ? Bitrix\Seo\SitemapRuntimeTable::ACTIVE : Bitrix\Seo\SitemapRuntimeTable::INACTIVE,
                );

                Bitrix\Seo\SitemapRuntimeTable::add($arRuntimeData);

                echo Loc::getMessage('SITEMAP_RUN_FILES', array('#PATH#' => '/'))."<br>";

                $sitemapFile = new Bitrix\Seo\SitemapRuntime($PID, $arSitemap['SETTINGS']['FILENAME_FILES'], $arSitemapSettings);

                $v++;
            } elseif ($v < $arValueSteps['files']) {
                $NS['steps_count']++;

                $sitemapFile = new Bitrix\Seo\SitemapRuntime($PID, $arSitemap['SETTINGS']['FILENAME_FILES'], $arSitemapSettings);

                $stepDuration = 15;
                $ts_finish = microtime(true) + $stepDuration * 0.95;

                $bFinished = false;
                $bCheckFinished = false;

                $dbRes = null;

                while (!$bFinished && microtime(true) <= $ts_finish) {
                    if (!$dbRes) {
                        $dbRes = Bitrix\Seo\SitemapRuntimeTable::getList(array(
                            'order' => array('ITEM_PATH' => 'ASC'),
                            'filter' => array(
                                'PID' => $PID,
                                'ITEM_TYPE' => Bitrix\Seo\SitemapRuntimeTable::ITEM_TYPE_DIR,
                                'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::UNPROCESSED,
                            ),
                            'limit' => 1000
                        ));
                    }

                    if ($arRes = $dbRes->Fetch()) {
                        self::seoSitemapGetFilesData($PID, $arSitemap, $arRes, $sitemapFile);
                        $bCheckFinished = false;
                    } elseif (!$bCheckFinished) {
                        $dbRes = null;
                        $bCheckFinished = true;
                    } else {
                        $bFinished = true;
                    }
                }

                if (!$bFinished) {
                    if ($v < $arValueSteps['files'] - 1) {
                        $v++;
                    }

                    echo Loc::getMessage('SITEMAP_RUN_FILES', array('#PATH#' => $arRes['ITEM_PATH']))."<br>";
                } else {
                    if (!is_array($NS['XML_FILES'])) {
                        $NS['XML_FILES'] = array();
                    }

                    if ($sitemapFile->isNotEmpty()) {
                        if ($sitemapFile->isCurrentPartNotEmpty()) {
                            $sitemapFile->finish();
                        } else {
                            $sitemapFile->delete();
                        }

                        $NS['XML_FILES'] = array_merge($NS['XML_FILES'], $sitemapFile->getNameList());
                    } else {
                        $sitemapFile->delete();
                    }

                    $v = $arValueSteps['files'];
                    echo Loc::getMessage('SITEMAP_RUN_FILE_COMPLETE',
                       array('#FILE#' => $arSitemap['SETTINGS']['FILENAME_FILES']))."<br>";
                }

            } elseif ($v < $arValueSteps['iblock_index']) {
                $NS['time_start'] = microtime(true);

                $arIBlockList = array();
                if (Bitrix\Main\Loader::includeModule('iblock')) {
                    $arIBlockList = $arSitemap['SETTINGS']['IBLOCK_ACTIVE'];
                    if (count($arIBlockList) > 0) {
                        $arIBlocks = array();
                        $dbIBlock = CIBlock::GetList(array(), array('ID' => array_keys($arIBlockList)));
                        while ($arIBlock = $dbIBlock->Fetch()) {
                            $arIBlocks[$arIBlock['ID']] = $arIBlock;
                        }

                        foreach ($arIBlockList as $iblockId => $iblockActive) {
                            if ($iblockActive !== 'Y' || !array_key_exists($iblockId, $arIBlocks)) {
                                unset($arIBlockList[$iblockId]);
                            } else {
                                Bitrix\Seo\SitemapRuntimeTable::add(array(
                                    'PID' => $PID,
                                    'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::UNPROCESSED,
                                    'ITEM_ID' => $iblockId,
                                    'ITEM_TYPE' => Bitrix\Seo\SitemapRuntimeTable::ITEM_TYPE_IBLOCK,
                                ));

                                $fileName = str_replace(
                                    array('#IBLOCK_ID#', '#IBLOCK_CODE#', '#IBLOCK_XML_ID#'),
                                    array($iblockId, $arIBlocks[$iblockId]['CODE'], $arIBlocks[$iblockId]['XML_ID']),
                                    $arSitemap['SETTINGS']['FILENAME_IBLOCK']
                                );

                                $sitemapFile = new Bitrix\Seo\SitemapRuntime($PID, $fileName, $arSitemapSettings);
                                if ($sitemapFile->isExists()) {
                                    //$sitemapFile->delete();
                                }
                            }
                        }
                    }
                }

                $NS['LEFT_MARGIN'] = 0;
                $NS['IBLOCK_LASTMOD'] = 0;

                $NS['IBLOCK'] = array();

                if (count($arIBlockList) <= 0) {
                    $v = $arValueSteps['iblock'];
                    echo Loc::getMessage('SITEMAP_RUN_IBLOCK_EMPTY')."<br>";
                } else {
                    $v = $arValueSteps['iblock_index'];
                    echo Loc::getMessage('SITEMAP_RUN_IBLOCK')."<br>";
                }
            } else {

                if ($v < $arValueSteps['iblock']) {
                    $stepDuration = 10;
                    $ts_finish = microtime(true) + $stepDuration * 0.95;

                    $bFinished = false;
                    $bCheckFinished = false;

                    $currentIblock = false;
                    $iblockId = 0;

                    $dbOldIblockResult = null;
                    $dbIblockResult = null;

                    while (!$bFinished && microtime(true) <= $ts_finish) {
                        if (!$currentIblock) {
                            $arCurrentIBlock = false;
                            $dbRes = Bitrix\Seo\SitemapRuntimeTable::getList(array(
                                'order' => array('ID' => 'ASC'),
                                'filter' => array(
                                    'PID' => $PID,
                                    'ITEM_TYPE' => Bitrix\Seo\SitemapRuntimeTable::ITEM_TYPE_IBLOCK,
                                    'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::UNPROCESSED,
                                ),
                                'limit' => 1
                            ));

                            $currentIblock = $dbRes->fetch();

                            if ($currentIblock) {
                                $iblockId = intval($currentIblock['ITEM_ID']);

                                $dbIBlock = CIBlock::GetByID($iblockId);
                                $arCurrentIBlock = $dbIBlock->Fetch();

                                if (!$arCurrentIBlock) {
                                    Bitrix\Seo\SitemapRuntimeTable::update($currentIblock['ID'], array(
                                        'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::PROCESSED
                                    ));

                                    $NS['LEFT_MARGIN'] = 0;
                                    $NS['IBLOCK_LASTMOD'] = 0;
                                    $NS['LAST_ELEMENT_ID'] = 0;
                                    unset($NS['CURRENT_SECTION']);
                                } else {
                                    if (strlen($arCurrentIBlock['LIST_PAGE_URL']) <= 0) {
                                        $arSitemap['SETTINGS']['IBLOCK_LIST'][$iblockId] = 'N';
                                    }
                                    if (strlen($arCurrentIBlock['SECTION_PAGE_URL']) <= 0) {
                                        $arSitemap['SETTINGS']['IBLOCK_SECTION'][$iblockId] = 'N';
                                    }
                                    if (strlen($arCurrentIBlock['DETAIL_PAGE_URL']) <= 0) {
                                        $arSitemap['SETTINGS']['IBLOCK_ELEMENT'][$iblockId] = 'N';
                                    }

                                    $NS['IBLOCK_LASTMOD'] = max($NS['IBLOCK_LASTMOD'],
                                        MakeTimeStamp($arCurrentIBlock['TIMESTAMP_X']));

                                    if ($NS['LEFT_MARGIN'] <= 0 && $arSitemap['SETTINGS']['IBLOCK_ELEMENT'][$iblockId] != 'N') {
                                        $NS['CURRENT_SECTION'] = 0;
                                    }

                                    $fileName = str_replace(
                                        array('#IBLOCK_ID#', '#IBLOCK_CODE#', '#IBLOCK_XML_ID#'),
                                        array($iblockId, $arCurrentIBlock['CODE'], $arCurrentIBlock['XML_ID']),
                                        $arSitemap['SETTINGS']['FILENAME_IBLOCK']
                                    );
                                    $sitemapFile = new Bitrix\Seo\SitemapRuntime($PID, $fileName, $arSitemapSettings);
                                }
                            }
                        }

                        if (!$currentIblock) {
                            $bFinished = true;
                        } elseif (is_array($arCurrentIBlock)) {
                            if ($dbIblockResult == null) {
                                if (isset($NS['CURRENT_SECTION'])) {
                                    $dbIblockResult = CIBlockElement::GetList(
                                        array('ID' => 'ASC'),
                                        array(
                                            'IBLOCK_ID' => $iblockId,
                                            'ACTIVE' => 'Y',
                                            'SECTION_ID' => intval($NS['CURRENT_SECTION']),
                                            '>ID' => intval($NS['LAST_ELEMENT_ID']),
                                            'SITE_ID' => $arSitemap['SITE_ID'],
                                        ),
                                        false,
                                        array('nTopCount' => 1000),
                                        array('ID', 'TIMESTAMP_X', 'DETAIL_PAGE_URL')
                                    );
                                } else {
                                    $NS['LAST_ELEMENT_ID'] = 0;
                                    $dbIblockResult = CIBlockSection::GetList(
                                        array('LEFT_MARGIN' => 'ASC'),
                                        array(
                                            'IBLOCK_ID' => $iblockId,
                                            'GLOBAL_ACTIVE' => 'Y',
                                            '>LEFT_BORDER' => intval($NS['LEFT_MARGIN']),
                                        ),
                                        false,
                                        array(
                                            'ID',
                                            'TIMESTAMP_X',
                                            'SECTION_PAGE_URL',
                                            'LEFT_MARGIN',
                                            'IBLOCK_SECTION_ID'
                                        ),
                                        array('nTopCount' => 100)
                                    );
                                }
                            }

                            if (isset($NS['CURRENT_SECTION'])) {
                                $arElement = $dbIblockResult->fetch();

                                if ($arElement) {
                                    $arElement['LANG_DIR'] = $arSitemap['SITE']['DIR'];

                                    $bCheckFinished = false;
                                    $elementLastmod = MakeTimeStamp($arElement['TIMESTAMP_X']);
                                    $NS['IBLOCK_LASTMOD'] = max($NS['IBLOCK_LASTMOD'], $elementLastmod);
                                    $NS['LAST_ELEMENT_ID'] = $arElement['ID'];

                                    $NS['IBLOCK'][$iblockId]['E']++;

                                    $url = \CIBlock::ReplaceDetailUrl($arElement['DETAIL_PAGE_URL'], $arElement, false, "E");

                                    $sitemapFile->addIBlockEntry($url, $elementLastmod);
                                } elseif (!$bCheckFinished) {
                                    $bCheckFinished = true;
                                    $dbIblockResult = null;
                                } else {
                                    $bCheckFinished = false;
                                    unset($NS['CURRENT_SECTION']);
                                    $NS['LAST_ELEMENT_ID'] = 0;

                                    $dbIblockResult = null;
                                    if ($dbOldIblockResult) {
                                        $dbIblockResult = $dbOldIblockResult;
                                        $dbOldIblockResult = null;
                                    }
                                }
                            } else {
                                $arSection = $dbIblockResult->fetch();

                                if ($arSection) {
                                    $bCheckFinished = false;
                                    $sectionLastmod = MakeTimeStamp($arSection['TIMESTAMP_X']);
                                    $NS['LEFT_MARGIN'] = $arSection['LEFT_MARGIN'];
                                    $NS['IBLOCK_LASTMOD'] = max($NS['IBLOCK_LASTMOD'], $sectionLastmod);

                                    $bActive = false;
                                    $bActiveElement = false;

                                    if (isset($arSitemap['SETTINGS']['IBLOCK_SECTION_SECTION'][$iblockId][$arSection['ID']])) {
                                        $bActive = $arSitemap['SETTINGS']['IBLOCK_SECTION_SECTION'][$iblockId][$arSection['ID']] == 'Y';
                                        $bActiveElement = $arSitemap['SETTINGS']['IBLOCK_SECTION_ELEMENT'][$iblockId][$arSection['ID']] == 'Y';
                                    } elseif ($arSection['IBLOCK_SECTION_ID'] > 0) {
                                        $dbRes = Bitrix\Seo\SitemapRuntimeTable::getList(array(
                                            'filter' => array(
                                                'PID' => $PID,
                                                'ITEM_TYPE' => Bitrix\Seo\SitemapRuntimeTable::ITEM_TYPE_SECTION,
                                                'ITEM_ID' => $arSection['IBLOCK_SECTION_ID'],
                                                'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::PROCESSED,
                                            ),
                                            'select' => array('ACTIVE', 'ACTIVE_ELEMENT'),
                                            'limit' => 1
                                        ));

                                        $parentSection = $dbRes->fetch();
                                        if ($parentSection) {
                                            $bActive = $parentSection['ACTIVE'] == Bitrix\Seo\SitemapRuntimeTable::ACTIVE;
                                            $bActiveElement = $parentSection['ACTIVE_ELEMENT'] == Bitrix\Seo\SitemapRuntimeTable::ACTIVE;
                                        }
                                    } else {
                                        $bActive = $arSitemap['SETTINGS']['IBLOCK_SECTION'][$iblockId] == 'Y';
                                        $bActiveElement = $arSitemap['SETTINGS']['IBLOCK_ELEMENT'][$iblockId] == 'Y';
                                    }

                                    $arRuntimeData = array(
                                        'PID' => $PID,
                                        'ITEM_ID' => $arSection['ID'],
                                        'ITEM_TYPE' => Bitrix\Seo\SitemapRuntimeTable::ITEM_TYPE_SECTION,
                                        'ACTIVE' => $bActive ? Bitrix\Seo\SitemapRuntimeTable::ACTIVE : Bitrix\Seo\SitemapRuntimeTable::INACTIVE,
                                        'ACTIVE_ELEMENT' => $bActiveElement ? Bitrix\Seo\SitemapRuntimeTable::ACTIVE : Bitrix\Seo\SitemapRuntimeTable::INACTIVE,
                                        'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::PROCESSED,
                                    );

                                    if ($bActive) {
                                        $NS['IBLOCK'][$iblockId]['S']++;

                                        $arSection['LANG_DIR'] = $arSitemap['SITE']['DIR'];

                                        $url = \CIBlock::ReplaceDetailUrl($arSection['SECTION_PAGE_URL'], $arSection, false,
                                            "S");

                                        $sitemapFile->addIBlockEntry($url, $sectionLastmod);
                                    }

                                    Bitrix\Seo\SitemapRuntimeTable::add($arRuntimeData);

                                    if ($bActiveElement) {
                                        $NS['CURRENT_SECTION'] = $arSection['ID'];
                                        $NS['LAST_ELEMENT_ID'] = 0;

                                        $dbOldIblockResult = $dbIblockResult;
                                        $dbIblockResult = null;
                                    }

                                } elseif (!$bCheckFinished) {
                                    unset($NS['CURRENT_SECTION']);
                                    $bCheckFinished = true;
                                    $dbIblockResult = null;
                                } else {
                                    $bCheckFinished = false;
                                    // we have finished current iblock

                                    Bitrix\Seo\SitemapRuntimeTable::update($currentIblock['ID'], array(
                                        'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::PROCESSED,
                                    ));

                                    if ($arSitemap['SETTINGS']['IBLOCK_LIST'][$iblockId] == 'Y' && strlen($arCurrentIBlock['LIST_PAGE_URL']) > 0) {
                                        $NS['IBLOCK'][$iblockId]['I']++;

                                        $arCurrentIBlock['IBLOCK_ID'] = $arCurrentIBlock['ID'];
                                        $arCurrentIBlock['LANG_DIR'] = $arSitemap['SITE']['DIR'];

                                        $url = \CIBlock::ReplaceDetailUrl($arCurrentIBlock['LIST_PAGE_URL'], $arCurrentIBlock,
                                            false, "");
                                        $sitemapFile->addIBlockEntry($url, $NS['IBLOCK_LASTMOD']);
                                    }

                                    if ($sitemapFile->isNotEmpty()) {
                                        if ($sitemapFile->isCurrentPartNotEmpty()) {
                                            $sitemapFile->finish();
                                        } else {
                                            $sitemapFile->delete();
                                        }

                                        if (!is_array($NS['XML_FILES'])) {
                                            $NS['XML_FILES'] = array();
                                        }

                                        $NS['XML_FILES'] = array_merge($NS['XML_FILES'], $sitemapFile->getNameList());
                                    } else {
                                        $sitemapFile->delete();
                                    }

                                    $currentIblock = false;
                                    $NS['LEFT_MARGIN'] = 0;
                                    $NS['IBLOCK_LASTMOD'] = 0;
                                    unset($NS['CURRENT_SECTION']);
                                    $NS['LAST_ELEMENT_ID'] = 0;
                                }
                            }
                        }
                    }
                    if ($v < $arValueSteps['iblock'] - 1) {

                        //echo Loc::getMessage('SITEMAP_RUN_IBLOCK_NAME', array('#IBLOCK_NAME#' => $arCurrentIBlock['NAME']))."<br>";
                        $v++;
                    }

                    if ($bFinished) {
                        $v = $arValueSteps['iblock'];
                        echo Loc::getMessage('SITEMAP_RUN_FINALIZE')."<br>";
                    }
                } elseif ($v < $arValueSteps['forum_index']) {
                    $NS['time_start'] = microtime(true);

                    $arForumList = array();
                    if (!empty($arSitemap['SETTINGS']['FORUM_ACTIVE'])) {
                        foreach ($arSitemap['SETTINGS']['FORUM_ACTIVE'] as $forumId => $active) {
                            if ($active == "Y") {
                                $arForumList[$forumId] = "Y";
                            }
                        }
                    }
                    if (count($arForumList) > 0 && Main\Loader::includeModule('forum')) {
                        $arForums = array();
                        $db_res = CForumNew::GetListEx(
                            array(),
                            array(
                                '@ID' => array_keys($arForumList),
                                "ACTIVE" => "Y",
                                "SITE_ID" => $arSitemap['SITE_ID'],
                                "!TOPICS" => 0
                            )
                        );
                        while ($res = $db_res->Fetch()) {
                            $arForums[$res['ID']] = $res;
                        }
                        $arForumList = array_intersect_key($arForums, $arForumList);

                        foreach ($arForumList as $id => $forum) {
                            Bitrix\Seo\SitemapRuntimeTable::add(array(
                                    'PID' => $PID,
                                    'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::UNPROCESSED,
                                    'ITEM_ID' => $id,
                                    'ITEM_TYPE' => Bitrix\Seo\SitemapRuntimeTable::ITEM_TYPE_FORUM
                                )
                            );

                            $fileName = str_replace('#FORUM_ID#', $forumId, $arSitemap['SETTINGS']['FILENAME_FORUM']);
                            $sitemapFile = new Bitrix\Seo\SitemapRuntime($PID, $fileName, $arSitemapSettings);
                        }
                    }

                    $NS['FORUM_CURRENT_TOPIC'] = 0;

                    if (count($arForumList) <= 0) {
                        $v = $arValueSteps['forum'];
                        echo Loc::getMessage('SITEMAP_RUN_FORUM_EMPTY')."<br>";
                    } else {
                        $v = $arValueSteps['forum_index'];
                        echo Loc::getMessage('SITEMAP_RUN_FORUM')."<br>";
                    }
                } else {
                    if ($v < $arValueSteps['forum']) {
                        $stepDuration = 10;
                        $ts_finish = microtime(true) + $stepDuration * 0.95;

                        $bFinished = false;
                        $bCheckFinished = false;

                        $currentForum = false;
                        $forumId = 0;

                        $dbTopicResult = null;
                        $arTopic = null;

                        while (!$bFinished && microtime(true) <= $ts_finish && CModule::IncludeModule("forum")) {
                            if (!$currentForum) {
                                $arCurrentForum = false;
                                $dbRes = Bitrix\Seo\SitemapRuntimeTable::getList(array(
                                    'order' => array('ID' => 'ASC'),
                                    'filter' => array(
                                        'PID' => $PID,
                                        'ITEM_TYPE' => Bitrix\Seo\SitemapRuntimeTable::ITEM_TYPE_FORUM,
                                        'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::UNPROCESSED,
                                    ),
                                    'limit' => 1
                                ));

                                $currentForum = $dbRes->fetch();

                                if ($currentForum) {
                                    $forumId = intval($currentForum['ITEM_ID']);

                                    $db_res = CForumNew::GetListEx(
                                        array(),
                                        array(
                                            'ID' => $forumId,
                                            "ACTIVE" => "Y",
                                            "SITE_ID" => $arSitemap['SITE_ID'],
                                            "!TOPICS" => 0
                                        )
                                    );
                                    $arCurrentForum = $db_res->Fetch();
                                    if (!$arCurrentForum) {
                                        Bitrix\Seo\SitemapRuntimeTable::update($currentForum['ID'], array(
                                            'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::PROCESSED
                                        ));
                                    } else {
                                        $fileName = str_replace('#FORUM_ID#', $forumId,
                                            $arSitemap['SETTINGS']['FILENAME_FORUM']);
                                        $sitemapFile = new Bitrix\Seo\SitemapRuntime($PID, $fileName, $arSitemapSettings);
                                    }
                                }
                            }

                            if (!$currentForum) {
                                $bFinished = true;
                            } elseif (is_array($arCurrentForum)) {
                                $bActive = (array_key_exists($forumId,
                                        $arSitemap['SETTINGS']['FORUM_TOPIC']) && $arSitemap['SETTINGS']['FORUM_TOPIC'][$forumId] == "Y");
                                if ($bActive) {
                                    if ($dbTopicResult == null) {
                                        $dbTopicResult = CForumTopic::GetList(
                                            array("LAST_POST_DATE" => "DESC"),
                                            array_merge(
                                                array(
                                                    "FORUM_ID" => $forumId,
                                                    "APPROVED" => "Y"
                                                ),
                                                ($NS['FORUM_CURRENT_TOPIC'] > 0 ? array(
                                                    ">ID" => $NS["FORUM_CURRENT_TOPIC"]
                                                ) : array()
                                                )
                                            ),
                                            false,
                                            0,
                                            array('nTopCount' => 100)
                                        );
                                    }
                                    if (($arTopic = $dbTopicResult->fetch()) && $arTopic) {
                                        $NS["FORUM_CURRENT_TOPIC"] = $arTopic["ID"];
                                        $url = CForumNew::PreparePath2Message(
                                            $arCurrentForum["PATH2FORUM_MESSAGE"],
                                            array(
                                                "FORUM_ID" => $arCurrentForum["ID"],
                                                "TOPIC_ID" => $arTopic["ID"],
                                                "TITLE_SEO" => $arTopic["TITLE_SEO"],
                                                "MESSAGE_ID" => "s",
                                                "SOCNET_GROUP_ID" => $arTopic["SOCNET_GROUP_ID"],
                                                "OWNER_ID" => $arTopic["OWNER_ID"],
                                                "PARAM1" => $arTopic["PARAM1"],
                                                "PARAM2" => $arTopic["PARAM2"]
                                            )
                                        );
                                        $sitemapFile->addIBlockEntry($url, MakeTimeStamp($arTopic['LAST_POST_DATE']));
                                    }
                                } else {
                                    $url = CForumNew::PreparePath2Message(
                                        $arCurrentForum["PATH2FORUM_MESSAGE"],
                                        array(
                                            "FORUM_ID" => $arCurrentForum["ID"],
                                            "TOPIC_ID" => $arCurrentForum["TID"],
                                            "TITLE_SEO" => $arCurrentForum["TITLE_SEO"],
                                            "MESSAGE_ID" => "s",
                                            "SOCNET_GROUP_ID" => $arCurrentForum["SOCNET_GROUP_ID"],
                                            "OWNER_ID" => $arCurrentForum["OWNER_ID"],
                                            "PARAM1" => $arCurrentForum["PARAM1"],
                                            "PARAM2" => $arCurrentForum["PARAM2"]
                                        )
                                    );
                                    $sitemapFile->addIBlockEntry($url, MakeTimeStamp($arCurrentForum['LAST_POST_DATE']));
                                }
                                if (empty($arTopic)) {
                                    $bCheckFinished = false;
                                    // we have finished current forum

                                    Bitrix\Seo\SitemapRuntimeTable::update($currentForum['ID'], array(
                                        'PROCESSED' => Bitrix\Seo\SitemapRuntimeTable::PROCESSED,
                                    ));

                                    if ($sitemapFile->isNotEmpty()) {
                                        if ($sitemapFile->isCurrentPartNotEmpty()) {
                                            $sitemapFile->finish();
                                        } else {
                                            $sitemapFile->delete();
                                        }

                                        if (!is_array($NS['XML_FILES'])) {
                                            $NS['XML_FILES'] = array();
                                        }

                                        $NS['XML_FILES'] = array_merge($NS['XML_FILES'], $sitemapFile->getNameList());
                                    } else {
                                        $sitemapFile->delete();
                                    }

                                    $currentForum = false;
                                    $dbTopicResult = null;
                                    $NS['FORUM_CURRENT_TOPIC'] = 0;
                                }
                            }
                        }
                        if ($v < $arValueSteps['forum'] - 1) {
                            echo Loc::getMessage('SITEMAP_RUN_FORUM_NAME', array('#FORUM_NAME#' => $arCurrentForum['NAME']))."<br>";
                            $v++;
                        }

                        if ($bFinished) {
                            $v = $arValueSteps['forum'];
                            echo Loc::getMessage('SITEMAP_RUN_FINALIZE')."<br>";
                        }
                    } else {
                        Bitrix\Seo\SitemapRuntimeTable::clearByPid($PID);

                        $arFiles = array();

                        $sitemapFile = new Bitrix\Seo\SitemapIndex($arSitemap['SETTINGS']['FILENAME_INDEX'], $arSitemapSettings);

                        if (count($NS['XML_FILES']) > 0) {
                            foreach ($NS['XML_FILES'] as $xmlFile) {
                                $arFiles[] = new Bitrix\Main\IO\File(Bitrix\Main\IO\Path::combine(
                                    $sitemapFile->getSiteRoot(),
                                    $xmlFile
                                ), $arSitemap['SITE_ID']);
                            }
                        }

                        $sitemapFile->createIndex($arFiles);

                        $arExistedSitemaps = array();

                        if ($arSitemap['SETTINGS']['ROBOTS'] == 'Y') {
                            $sitemapUrl = $sitemapFile->getUrl();

                            $robotsFile = new Bitrix\Seo\RobotsFile($arSitemap['SITE_ID']);
                            $robotsFile->addRule(
                                array(Bitrix\Seo\RobotsFile::SITEMAP_RULE, $sitemapUrl)
                            );

                            $arSitemapLinks = $robotsFile->getRules(Bitrix\Seo\RobotsFile::SITEMAP_RULE);
                            if (count($arSitemapLinks) > 1) // 1 - just added rule
                            {
                                foreach ($arSitemapLinks as $rule) {
                                    if ($rule[1] != $sitemapUrl) {
                                        $arExistedSitemaps[] = $rule[1];
                                    }
                                }
                            }
                        }

                        $v = $arValueSteps['index'];
                    }
                }
            }

            if ($v == $arValueSteps['index']) {
                echo Loc::getMessage('SITEMAP_RUN_FINISH')."<br>";
                Bitrix\Seo\SitemapTable::update($ID, array('DATE_RUN' => new Bitrix\Main\Type\DateTime()));
            }

            //echo Bitrix\Seo\SitemapRuntime::showProgress("", Loc::getMessage('SEO_SITEMAP_RUN_TITLE'), $v)."<br>";
            echo $v." %<br><br>";

            if ($v < $arValueSteps['index']) {

                self::run($lang, $action, $ID, $v, $PID, $NS, $sessid);

            }/* else {
                echo Loc::getMessage('SEO_SITEMAP_RUN_ROBOTS_WARNING')."<br>";
            }*/
        }
    }



}
