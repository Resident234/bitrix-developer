<form method="post" enctype="multipart/form-data" class="nextype-form">

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