<?

function get_company_form_description(){
    $fields=array(
        "OWNERSHIP_FORM" => array(
            "NAME" => "Форма собственности",
            "CSS_CLASS" => "b-form__row b-form__row_xm-width",
            "FIELD_ADD_CLASS" => "",
            "TYPE_FIELD" => "SELECT",
            "REQUIRED" => true,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Не заполнено",
            "CLASS_ERROR" => "",
            "CURRENT_VALUE" => $_REQUEST["OWNERSHIP_FORM"],
            "VALUE_LIST" => array("ООО", "ОАО", "ЗАО", "АО", "ИП"),
        ),
        "SMALL_NAME" => array(
            "NAME" => "Наименование организации",
            "CSS_CLASS" => "b-form__row b-form__row_b-width no_right",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => true,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Не заполнено",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["SMALL_NAME"]
        ),
        "FULL_NAME" => array(
            "NAME" => "Полное название для отображения в документах",
            "CSS_CLASS" => "b-form__row b-form__row_b-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => true,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Не заполнено",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["FULL_NAME"]
        ),
        "INN" => array(
            "NAME" => "ИНН",
            "CSS_CLASS" => "b-form__row b-form__row_sm-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => false,
            "VALIDATOR" =>  "/([0-9]){10,12}$/",
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Неверный ИНН",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["INN"]
        ),
        "KPP" => array(
            "NAME" => "КПП",
            "CSS_CLASS" => "b-form__row b-form__row_sm-width no_right",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => false,
            "VALIDATOR" => "/([0-9]){9,9}$/",
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Неверный КПП",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["KPP"]
        ),
        "HEADER_POST" => array(
            "NAME" => "Должность руководителя",
            "CSS_CLASS" => "b-form__row b-form__row_m-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => false,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["HEADER_POST"]
        ),
        "HEADER_SURNAME" => array(
            "NAME" => "Фамилия руководителя",
            "CSS_CLASS" => "b-form__row b-form__row_m-width no_right",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => false,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["HEADER_SURNAME"]
        ),
        "HEADER_BASIS" => array(
            "NAME" => " Руководитель действует на основани",
            "CSS_CLASS" => "b-form__row b-form__row_b-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => false,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["HEADER_BASIS"]
        ),
        "PHONE_GENERAL" => array(
            "NAME" => "Телефон",
            "CSS_CLASS" => "b-form__row b-form__row_sm-width b-company__phone",
            "FIELD_ADD_CLASS" => "input_phone",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => false,
            "VALIDATOR" => "/((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{5,10}$/",
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["PHONE_GENERAL"]
        ),
        "PHONE_ADD" => array(
            "NAME" => "Добавочный телефон",
            "CSS_CLASS" => "b-form__row b-form__row_sm-width b-company__added_phone",
            "FIELD_ADD_CLASS" => "add_phone",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => false,
            "VALIDATOR" =>"/\d{4}$/",
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["PHONE_ADD"]
        ),

        //Юридический адрес
        "LEGAL_REGION" => array(
            "NAME" => "Республика, область, край",
            "CSS_CLASS" => "b-form__row b-form__row_b-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => true,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Не ввели регион",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["LEGAL_REGION"]
        ),
        "LEGAL_CITY" => array(
            "NAME" => "Город",
            "CSS_CLASS" => "b-form__row b-form__row_sm-width no_right",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => true,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Не ввели город",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["LEGAL_CITY"]
        ),
        "LEGAL_STREET" => array(
            "NAME" => "Улица, переулок",
            "CSS_CLASS" => "b-form__row b-form__row_xm-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => true,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Не ввели улицу",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["LEGAL_STREET"]
        ),
        "LEGAL_HOME" => array(
            "NAME" => "Дом, стр., корп.",
            "CSS_CLASS" => "b-form__row b-form__row_sm-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => true,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Не ввели дом",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["LEGAL_HOME"]
        ),
        "LEGAL_APARTMENT" => array(
            "NAME" => "Квартира",
            "CSS_CLASS" => "b-form__row b-form__row_sm-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => false,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["LEGAL_APARTMENT"]
        ),

        //Адрес доставки
        "DELIVERY_INDEX" => array(
            "NAME" => "Индекс",
            "CSS_CLASS" => "b-form__row b-form__row_sm-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => false,
            "VALIDATOR" => "/([0-9]){6}$/",
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Некорректно заполнен индекс",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["DELIVERY_INDEX"]
        ),
        "DELIVERY_REGION" => array(
            "NAME" => "Республика, область, край",
            "CSS_CLASS" => "b-form__row b-form__row_b-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => true,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Не ввели регион",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["DELIVERY_REGION"]
        ),
        "DELIVERY_CITY" => array(
            "NAME" => "Город",
            "CSS_CLASS" => "b-form__row b-form__row_sm-width no_right",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => true,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Не ввели город",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["DELIVERY_CITY"]
        ),
        "DELIVERY_STREET" => array(
            "NAME" => "Улица, переулок",
            "CSS_CLASS" => "b-form__row b-form__row_xm-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => true,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Не ввели улицу",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["DELIVERY_STREET"]
        ),
        "DELIVERY_HOME" => array(
            "NAME" => "Дом, стр., корп.",
            "CSS_CLASS" => "b-form__row b-form__row_sm-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => true,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "Не ввели дом",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["DELIVERY_HOME"]
        ),
        "DELIVERY_APARTMENT" => array(
            "NAME" => "Квартира",
            "CSS_CLASS" => "b-form__row b-form__row_sm-width",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => false,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["DELIVERY_APARTMENT"]
        ),
        "DELIVERY_FLOOR" => array(
            "NAME" => "Этаж",
            "CSS_CLASS" => "b-form__row b-form__row_xsm-width no_right",
            "TYPE_FIELD" => "INPUT",
            "REQUIRED" => false,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "",
            "CLASS_ERROR" => "",
            "VALUE" => $_REQUEST["DELIVERY_FLOOR"]
        ),
        "DELIVERY_ELEVATOR" => array(
            "NAME" => "Лифт",
            "CSS_CLASS" => "b-form__row",
            "TYPE_FIELD" => "SELECT",
            "REQUIRED" => false,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "",
            "CLASS_ERROR" => "",
            "CURRENT_VALUE" => $_REQUEST["DELIVERY_ELEVATOR"],
            "VALUE_LIST" => array("Нет", "Пассажирский", "Грузовой"),
        ),
        "DELIVERY_PASS" => array(
            "NAME" => "Пропуск",
            "CSS_CLASS" => "b-form__row",
            "TYPE_FIELD" => "SELECT",
            "REQUIRED" => false,
            "VALIDATOR" => false,
            "INCORRECT" => false,
            "ERROR_MESSAGE" => "",
            "CLASS_ERROR" => "",
            "CURRENT_VALUE" => $_REQUEST["DELIVERY_PASS"],
            "VALUE_LIST" => array("Не требуется", "Выписывается на месте по паспорту", "Заказать предварительно"),
        ),
    );
    return $fields;
}
?>