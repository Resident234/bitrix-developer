<?
    CJSCore::Init(array("jquery"));
    $APPLICATION->AddHeadScript($this->GetFolder() ."/jquery.arcticmodal.js");
    $APPLICATION->AddHeadScript($this->GetFolder() ."/jquery.maskedinput.js");
?>

<a href="#" class="nextype-button" data-form-open="<?=$arResult['FORM_ID']?>">Открыть форму</a>


<? if ($_REQUEST['bitrix_include_areas'] != "Y"): ?>
<div style="display: none;">
<? endif; ?>
    
    
<div class="box-modal" id="form_<?=$arResult['FORM_ID']?>">
    <? if ($arParams['IS_AJAX'] == "Y")
            $APPLICATION->RestartBuffer();
    ?>
	<div class="arcticmodal-close">&#215;</div>
        <h4><?=$arParams['NAME']?></h4>
	<form method="post" data-form="<?=$arResult['FORM_ID']?>" enctype="multipart/form-data" class="nextype-form">
            
            <?=$arResult['AJAX_FIELD']?>
            
        <? if ($arResult['SUCCESS']): ?>
            <div class="success">
            <?=$arResult['SUCCESS']?>
            </div>
        <? endif; ?>
        <? foreach ($arResult['FIELDS'] as $arField): ?>
            <div class="row">
                <label><?=$arField['label']?> <? if ($arField['required']): ?><span class="required">*</span><?endif;?></label>
                <?=$arField['html']?>
                <? if ($arField['error']): ?>
                <div class="error-tip"><?=$arField['error']['TEXT']?></div>
                <? endif; ?>
            </div>
        <? endforeach; ?>
            <div class="row submit">
            <button type="submit"><?=GetMessage('NT_FORMS_SEND')?></button>
            </div>
        </form>
        
      <? if ($arParams['IS_AJAX'] == "Y")
            exit();
    ?>  
</div>
    
<? if ($_REQUEST['bitrix_include_areas'] != "Y"): ?>
</div>
<? endif; ?>