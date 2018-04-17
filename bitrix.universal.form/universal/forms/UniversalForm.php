<?php

class UniversalForm extends CBitrixComponent
{

    static $arTypes = Array (
        'text',
        'textarea',
        'hidden',
        'select',
        'email',
        'file'
    );

    static $maxFileSize = 5242880;
    static $arDefaultExtensions = Array ('doc', 'docx', 'jpg', 'jpeg', 'png', 'pdf', 'tiff', 'xls', 'xlsx');
    static $idFAQElement = '';
    static $idFAQIBlockId= "";

    static function ValidateEmail($value)
    {
        if(filter_var($value, FILTER_VALIDATE_EMAIL))
            return true;

        return false;
    }

    static function ValidateMask($mask, $value)
    {
        if (strlen($value) <= 0) return false;

        if (strlen($value) <> strlen($mask))
            return false;

        $maskSplit = str_split($mask);
        $valueSplit = str_split($value);

        foreach ($maskSplit as $key => $letter)
        {
            if ($letter == "#" && !is_numeric($valueSplit[$key]))
            {
                return false;
            }
            elseif ($letter != "#" && $valueSplit[$key] != $letter)
            {
                return false;
            }
        }

        return true;
    }

    public function GetAjaxField()
    {
        return '<input type="hidden" name="'.$this->formID.'[is_ajax]" value="Y" />';
    }


    public function GetHtml($arField)
    {
        $fieldName = $this->GetFieldName($arField['name']);
        $fieldValue = $this->GetFieldValue($arField['name']);

        $arField['field_name'] = $fieldName;

        if ($arField['required'])
            $requiredHtml = 'required="required"';

        if (is_array($arField['error']))
            $errorHtml = 'data-error="'.$arField['error']['TEXT'].'" class="error"';

        if (!empty($arField['mask']))
            $maskHtml = 'data-mask="'.$arField['mask'].'"';

        if (!empty($arField['placeholder']))
            $placeholderHtml = 'placeholder="'.$arField['placeholder'].'"';

        if (!empty($arField['class']))
            $classHtml = 'class="'.$arField['class'].'"';


        switch ($arField['type'])
        {
            case 'text':
                $arField['html'] = '<input '.$maskHtml.' '.$classHtml.' '.$placeholderHtml.' '.$errorHtml.' type="text" ' . $requiredHtml . ' value="'.$fieldValue.'" id="'.$arField['name'].'" name="'.$fieldName.'" />';
                break;

            case 'email':
                $arField['html'] = '<input '.$maskHtml.' '.$classHtml.' '.$placeholderHtml.' '.$errorHtml.' type="email" ' . $requiredHtml . ' value="'.$fieldValue.'" id="'.$arField['name'].'" name="'.$fieldName.'" />';
                break;

            case 'textarea':
                $arField['html'] = '<textarea '.$maskHtml.' '.$classHtml.' '.$placeholderHtml.' '.$errorHtml.' ' . $requiredHtml . ' id="'.$arField['name'].'" name="'.$fieldName.'">'.$fieldValue.'</textarea>';
                break;

            case 'select':
                $arField['html'] = '<select '.$errorHtml.' ' . $requiredHtml . ' id="'.$arField['name'].'" name="'.$fieldName.'">';
                foreach ($arField['values'] as $value)
                {
                    $selected = ($fieldValue == $value) ? 'selected="selected"' : '';
                    $arField['html'] .= '<option '.$selected.' value="'.$value.'">'.$value.'</option>';
                }
                $arField['html'] .= '</select>';
                break;

            case 'file':
                $arField['html'] = '<input '.$errorHtml.' type="file" ' . $requiredHtml . ' id="'.$arField['name'].'" name="'.$fieldName.'" />';
                break;

            case 'hidden':
                $arField['html'] = '<input '.$maskHtml.' '.$errorHtml.' type="hidden" ' . $requiredHtml . ' value="'.$fieldValue.'" id="'.$arField['name'].'" name="'.$fieldName.'" />';
                break;
        }

        return $arField;
    }


    public function GetFieldName($name, $withFormID = true)
    {
        if ($this->arParams['DECODE_FIELDNAME'] == "Y")
            $name = md5($name);

        if ($withFormID)
            $name = $this->formID . "[" . $name . "]";

        return $name;

    }

    public function GetFieldValue($name)
    {
        if (!empty($this->arFields[$name]['default']))
            $value = $this->arFields[$name]['default'];

        $requestValue = $_REQUEST[$this->formID][$this->GetFieldName($name, false)];

        if ($requestValue && !empty($requestValue))
            $value = $requestValue;

        return $value;
    }

    public function Send($data = null)
    {
        $this->arResult = Array ();

        if (!$data)
            $data = $_POST;

        foreach ($data as $key => $val)
            $data[$key] = mysql_escape_string($val);

        foreach ($this->arFields as $key => $field)
        {
            $val = $data[$this->GetFieldName($field['name'], false)];

            $this->arFields[$field['name']]['value'] = $val;

            if ($field['required'] == true && in_array($field['type'], Array("text", "hidden", "select", "email")) && empty($val))
            {
                $arError = Array (
                    'FIELD_ID'  => $this->GetFieldName($field['name']),
                    'TEXT'      => GetMessage('NT_FORMS_EMPTY_FIELD', Array ('#FIELD#' => $field['label']))
                );

                $this->arResult['ERROR'][] = $arError;
                $this->arFields[$key]['error'] = $arError;
                continue;
            }

            if ($field['required'] == true && $field['type'] == "email" && !self::ValidateEmail($val))
            {
                $arError = Array (
                    'FIELD_ID'  => $this->GetFieldName($field['name']),
                    'TEXT'      => GetMessage('NT_FORMS_ERROR_EMAIL', Array ('#FIELD#' => $field['label']))
                );

                $this->arResult['ERROR'][] = $arError;
                $this->arFields[$key]['error'] = $arError;
                continue;
            }


            if ($field['mask'] && strlen($field['mask']) > 0 && ! self::ValidateMask($field['mask'], $val) )
            {
                $arError = Array (
                    'FIELD_ID'  => $this->GetFieldName($field['name']),
                    'TEXT'      => GetMessage('NT_FORMS_ERROR_MASK_FIELD', Array ('#FIELD#' => $field['label'], '#MASK#' => $field['mask']))
                );
                $this->arResult['ERROR'][] = $arError;
                $this->arFields[$key]['error'] = $arError;
                continue;
            }

            if ($field['type'] == 'file')
            {

                if (isset($_FILES[$this->formID]['name'][$this->GetFieldName($field['name'], false)]) && !empty($_FILES[$this->formID]['name'][$this->GetFieldName($field['name'], false)]))
                {
                    $arFile = Array (
                        'name'      => $_FILES[$this->formID]['name'][$this->GetFieldName($field['name'], false)],
                        'type'      => $_FILES[$this->formID]['type'][$this->GetFieldName($field['name'], false)],
                        'tmp_name'  => $_FILES[$this->formID]['tmp_name'][$this->GetFieldName($field['name'], false)],
                        'error'     => $_FILES[$this->formID]['error'][$this->GetFieldName($field['name'], false)],
                        'size'      => $_FILES[$this->formID]['size'][$this->GetFieldName($field['name'], false)]
                    );
                }

                if ($field['required'] == true && !isset($arFile))
                {
                    $arError = Array (
                        'FIELD_ID'  => $this->GetFieldName($field['name']),
                        'TEXT'      => GetMessage('NT_FORMS_EMPTY_FIELD', Array ('#FIELD#' => $field['label']))
                    );
                    $this->arResult['ERROR'][] = $arError;
                    $this->arFields[$key]['error'] = $arError;

                    continue;
                }

                if (isset($arFile))
                {
                    $fileExt = end(explode(".", $arFile['name']));
                    if ($field['maxSize'] && $field['maxSize'] < $arFile['size'])
                    {

                        $arError = Array (
                            'FIELD_ID'  => $this->GetFieldName($field['name']),
                            'TEXT'      => GetMessage('NT_FORMS_ERROR_MAXSIZE')
                        );
                        $this->arResult['ERROR'][] = $arError;
                        $this->arFields[$key]['error'] = $arError;


                        continue;
                    }

                    if ($field['allowTypes'] && !in_array($fileExt, $field['allowTypes']))
                    {

                        $arError = Array (
                            'FIELD_ID'  => $this->GetFieldName($field['name']),
                            'TEXT'      => GetMessage('NT_FORMS_ERROR_FILETYPE')
                        );
                        $this->arResult['ERROR'][] = $arError;
                        $this->arFields[$key]['error'] = $arError;

                        continue;
                    }

                    $fileId = CFile::SaveFile(Array (
                        'name' => $arFile['name'],
                        'size' => $arFile['size'],
                        'tmp_name' => $arFile['tmp_name'],
                        'type' => $arFile['type'],
                    ), 'form_files');

                    if (!$fileId)
                    {
                        $arError = Array (
                            'FIELD_ID'  => $this->GetFieldName($field['name']),
                            'TEXT'      => GetMessage('NT_FORMS_ERROR_UPLOAD_FILE')
                        );
                        $this->arResult['ERROR'][] = $arError;
                        $this->arFields[$key]['error'] = $arError;
                        continue;
                    }

                    $this->arFields[$field['name']]['value'] = "http://" . $_SERVER['HTTP_HOST'] . CFile::GetPath($fileId);
                    //$this->arFields[$field['name'] . "_path"]['value'] = "<a href='". $this->arFields[$field['name']]['value']."'>".$arFile['name']."</a>";

                }

            }
        }

        if (!$this->arResult['ERROR'])
        {

            if ($this->arParams['SEND_IBLOCK_ENABLED'] == "Y")
            {
                $this->Save2IBlock();
            }

            if ($this->arParams['SEND_EMAIL_ENABLED'] == "Y")
            {
                $this->SendEmail();
            }



            //$this->SendEmail();
            //$this->arResult['fields'] = Array ();

            unset($_REQUEST[$this->formID]);

            $this->arResult['SUCCESS'] = $this->arParams['MESSAGE_SUCCESS'];


        }

    }

    private function SendEmail()
    {
        $arData = Array ();
        $arData["ID"] = $this->idFAQElement;
        $arData["LINK_TO_EDIT"] = "http://".$_SERVER["HTTP_HOST"] ."/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=".$this->idFAQIBlockId."&type=faq&ID=".$this->idFAQElement;
        foreach ($this->arFields as $fieldName => $arField)
        {
            if (!empty($arField['value']))
                $arData[$fieldName] = $arField['value'];

            $arData['HTML_FIELDS'] .= $arField['label'] . ": " . $arField['value'] . "<br/>";
        }

        $arData['EMAIL_TO'] = (!empty($this->arParams['SEND_EMAIL_ADDRESS'])) ? $this->arParams['SEND_EMAIL_ADDRESS'] : COption::GetOptionString("main", "email_from", "");
        $arData['FORM_NAME'] = $this->arParams['NAME'];

        if (!empty($this->arParams['SEND_EMAIL_EVENT_NAME']))
        {
            $event = new CEvent;
            $event->SendImmediate($this->arParams['SEND_EMAIL_EVENT_NAME'], SITE_ID, $arData);
        }
        else
        {
            $defaultEvent = "NEXTYPE_FORM_" . $this->formID;
            $rsEventType = CEventType::GetList(Array ('TYPE_ID' => $defaultEvent));
            if (!$arEventType = $rsEventType->Fetch())
            {
                $obEventType = new CEventType;
                $eventTypeID = $obEventType->Add(Array(
                    "EVENT_NAME"    => $defaultEvent,
                    "NAME"          => $defaultEvent,
                    "LID"           => array ("ru", "en"),
                ));

                if ($eventTypeID)
                {
                    $eventMessage = new CEventMessage;
                    $eventID = $eventMessage->Add(Array (
                        "ACTIVE" => "Y",
                        "LID" => SITE_ID,
                        "EVENT_NAME" => $defaultEvent,
                        "EMAIL_FROM" => "#DEFAULT_EMAIL_FROM#",
                        "EMAIL_TO" => "#EMAIL_TO#",
                        "BCC" => "#BCC#",
                        "SUBJECT" => GetMessage('NT_FORM_DEFAULT_SUBJECT'),
                        "BODY_TYPE" => "html",
                        "MESSAGE" => GetMessage('NT_FORM_DEFAULT_MESSAGE')
                    ));


                }

            }
            $event = new CEvent;
            $event->SendImmediate($defaultEvent, SITE_ID, $arData);
        }
    }

    function Save2IBlock()
    {
        global $APPLICATION, $USER;
        if (!empty($this->arParams['SEND_IBLOCK_TYPE']) && !empty($this->arParams['SEND_IBLOCK_ID']))
        {
            $rsIblockProps = CIBlock::GetProperties($this->arParams['SEND_IBLOCK_ID']);
            $arIblockProps = Array ();
            while ($arIblockProp = $rsIblockProps->Fetch())
            {
                $arIblockProps[strtoupper($arIblockProp['CODE'])] = Array (
                    'ID'    => $arIblockProp['ID'],
                    'NAME'  => $arIblockProp['NAME'],
                    'CODE' => $arIblockProp['CODE'],
                );
            }

            $propSortCounter = 1;
            foreach ($this->arFields as $fieldName => $arField)
            {
                if (!isset($arIblockProps[$fieldName]))
                {
                    $arPropData = Array (
                        "NAME" => $arField['label'],
                        "ACTIVE" => "Y",
                        "SORT" => $propSortCounter,
                        "CODE" => $fieldName,
                        "PROPERTY_TYPE" => "S",
                        "IBLOCK_ID" => intval($this->arParams['SEND_IBLOCK_ID'])
                    );
                    $ibProperty = new CIBlockProperty;
                    $ibPropertyID = $ibProperty->Add($arPropData);
                    $propSortCounter++;

                    if ($ibPropertyID)
                    {
                        $arIblockProps[$fieldName] = Array (
                            'ID' => $ibPropertyID,
                            'NAME' => $arPropData['NAME'],
                            'CODE' => $arPropData['CODE'],
                        );
                    }

                }


            }

            $propertyValues = Array ();
            foreach ($this->arFields as $fieldName => $arField)
            {
                $propertyValues[$arIblockProps[$fieldName]['ID']] = stripslashes($arField['value']);
            }


            $ibElement = new CIBlockElement;
            $idElement = $ibElement->Add(Array (
                'NAME'              => GetMessage('NT_FORM_DEFAULT_ELEMENT_NAME', Array (
                    '#FORM_NAME#' => $this->arParams['NAME'],
                    '#DATE_TIME#' => date("d.m.Y H:i"),
                )),
                "MODIFIED_BY"       => $USER->GetID(),
                "IBLOCK_SECTION_ID" => false,
                "IBLOCK_ID"         => intval($this->arParams['SEND_IBLOCK_ID']),
                "PROPERTY_VALUES"   => $propertyValues,
                "ACTIVE"            => "N"
            ));

            $this->idFAQElement = $idElement;
            $this->idFAQIBlockId = intval($this->arParams['SEND_IBLOCK_ID']);
        }
    }
}