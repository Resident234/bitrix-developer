var dadata_suggestions_company_and_addresses;

var dadata_token="3fd748f2a4de3138577435ef0223ac8036b8e334";
var dadata_serviceUrl_suggestions="https://suggestions.dadata.ru/suggestions/api/4_1/rs";


$(function() {

    $(document).on("click",".new_company",function(e){
        e.preventDefault();


        var obj = $(this);
        var wrapper =  obj.closest(".b-vacancies__item");

        var addressAjax = wrapper.find(".ajax_submit").val();
        var formDescriptionJson = wrapper.find(".description-form").val();
        var formDescription = JSON.parse(formDescriptionJson);


        var data ={

            'IS_SUBMIT':1,
            'DESCRIPTION_FIELDS':formDescriptionJson,
            'ADDRESS_DELIVER_ID': wrapper.find(".address_deliver").val(),
            'DELIVERY_EQ_LEGAL':wrapper.find("#ch4").prop("checked")
        };

        $.each(formDescription, function(index, value ) {
            data[index] = wrapper.find(value.TYPE_FIELD.toLowerCase() + ".input_" + index).val();
        });

        wrapper.find(".preloader.company").css({
            "display":"block",
            "height":$('.fancybox-skin').outerHeight(),
            "width":$('.fancybox-skin').outerWidth()
        });

        wrapper.load(
            addressAjax,
            data,
            function(d) {

                if(wrapper.find(".input_COMMENT1").val()=="undefined"){wrapper.find(".input_COMMENT1").val("");}
                if(wrapper.find(".input_COMMENT2").val()=="undefined"){wrapper.find(".input_COMMENT2").val("");}


                wrapper.find("select").styler();
                //wrapper.find("#ch3").styler();
                //wrapper.find("#ch4").styler();
                wrapper.find(input[type="checkbox"]).styler();

                $(".input_phone").mask("+7 (999) 999-9999");
                if(parseInt(wrapper.find(".success").text())) {
                    location.reload()
                }
                dadata_suggestions_company_and_addresses();


            }
        );



    });
    $(document).on("change","#ch4",function(e) {
        e.preventDefault();
        var obj = $(this);

        var wrapper =  obj.closest(".b-form");


        if(obj.prop("checked")){

            wrapper.find(".input_DELIVERY_BIG_ADDRESS").val(wrapper.find(".input_LEGAL_BIG_ADDRESS").val());
            wrapper.find(".input_DELIVERY_REGION").val(wrapper.find(".input_LEGAL_REGION").val());
            wrapper.find(".input_DELIVERY_CITY").val(wrapper.find(".input_LEGAL_CITY").val());
            wrapper.find(".input_DELIVERY_STREET").val(wrapper.find(".input_LEGAL_STREET").val());
            wrapper.find(".input_DELIVERY_HOME").val(wrapper.find(".input_LEGAL_HOME").val());
            wrapper.find(".input_DELIVERY_APARTMENT").val(wrapper.find(".input_LEGAL_APARTMENT").val());

            wrapper.find(".was_eq").text("1");
        }
        else{
            wrapper.find(".input_DELIVERY_BIG_ADDRESS").val("");
            wrapper.find(".input_DELIVERY_REGION").val("");
            wrapper.find(".input_DELIVERY_CITY").val("");
            wrapper.find(".input_DELIVERY_STREET").val("");
            wrapper.find(".input_DELIVERY_HOME").val("");
            wrapper.find(".input_DELIVERY_APARTMENT").val("");

            wrapper.find(".input_DELIVERY_INDEX").val("");
            wrapper.find(".input_DELIVERY_FLOOR").val("");
            wrapper.find(".input_DELIVERY_PASS").val("");
            wrapper.find(".input_DELIVERY_ELEVATOR").val("");
        }
    });

    $(document).on("click",".undo_address",function(e){
        e.preventDefault();
        obj = $(this);

        obj.closest('.b-vacancies__item').slideUp();
        $(".add_company").css("display","inline-block");


    });



    dadata_suggestions_company_and_addresses = function() {
        //dadata

        $(".input_SMALL_NAME").suggestions({
            serviceUrl: dadata_serviceUrl_suggestions,
            token: dadata_token,
            type: "PARTY",
            count: 5,
            /* Вызывается, когда пользователь выбирает одну из подсказок */
            onSelect: function (suggestion) {
                //alert("-");
                console.log("===suggestion===");
                console.log(suggestion);
                console.log("===suggestion===");

                var full_doc_name="";
                var inn="";
                var kpp="";
                var management_name="";
                var management_post = "";
                var address="";
                var region_with_type="";

                if(suggestion['data']['type']=="LEGAL") {

                    full_doc_name = suggestion['data']['name']['full_with_opf'];
                    inn = suggestion['data']['inn'];
                    kpp = suggestion['data']['kpp'];



                    if (suggestion['data']['management']['name'] == undefined) {
                        management_name = "";
                    } else {
                        management_name = suggestion['data']['management']['name'];
                    }

                    if (suggestion['data']['management']['post'] == undefined) {
                        management_post = "";
                    } else {
                        management_post = suggestion['data']['management']['post'];
                    }

                    address = suggestion['data']['address']['unrestricted_value'];
                    region_with_type = suggestion['data']['address']['data']['region_with_type'];

                }else if(suggestion['data']['type']=='INDIVIDUAL'){

                    full_doc_name = suggestion['data']['name']['full_with_opf'];
                    inn = suggestion['data']['inn'];
                    kpp = suggestion['data']['kpp'];
                    management_name = "";
                    management_post = "";

                    management_name = suggestion['data']['name']['full'];

                    address = suggestion['data']['address']['unrestricted_value'];
                    region_with_type = "";


                }


                //заполняем скрытые поля с адресом
                //юридический адрес
                var city="";
                var street="";
                var house="";
                var postal_code="";

                if(suggestion['data']['type']=="LEGAL") {
                    if (suggestion['data']['address']['data']['city'] != null) {
                        city = suggestion['data']['address']['data']['city'];
                    } else {
                        city = suggestion['data']['address']['data']['settlement_with_type'];
                    };
                    street = suggestion['data']['address']['data']['street'];
                    house = suggestion['data']['address']['data']['house'];
                    postal_code = suggestion['data']['address']['data']['postal_code'];


                }else if(suggestion['data']['type']=='INDIVIDUAL') {
                    var address_individual_url = $("#address_individual").val();




                    $.ajax({
                        url: address_individual_url,
                        type: "POST",
                        data: {address: address},
                        async: false,
                        success: function(data){


                            if (data != "error") {

                                var res = jQuery.parseJSON(data);

                                console.log("+++++++");
                                console.log(res);
                                console.log("+++++++");

                                if (res[0].postal_code != null) {
                                    postal_code = res[0].postal_code;
                                }

                                if (res[0].region != null) {
                                    region_with_type = res[0].region;
                                }
                                if (res[0].city != null) {
                                    city = res[0].city;
                                }
                                if (res[0].street != null) {
                                    street = res[0].street;
                                }
                                if (res[0].house != null) {
                                    house = res[0].house;
                                }
                                //if (res[0].flat != null) {
                                //    apartment_obj.val(res[0].flat);
                                //}
                            }


                        }
                    });
                    /*
                    $.post(address_individual_url, {address: address}, async: false)
                        .done(function (data) {
                       });
                    */


                }




                $(this).parents(".b-vacancies__item__wrap").find(".input_FULL_NAME").val(full_doc_name);
                $(this).parents(".b-vacancies__item__wrap").find(".input_INN").val(inn);
                $(this).parents(".b-vacancies__item__wrap").find(".input_KPP").val(kpp);
                $(this).parents(".b-vacancies__item__wrap").find(".input_HEADER_POST").val(management_post);
                $(this).parents(".b-vacancies__item__wrap").find(".input_HEADER_SURNAME").val(management_name);
                $(this).parents(".b-vacancies__item__wrap").find(".input_LEGAL_BIG_ADDRESS").val(address);
                $(this).parents(".b-vacancies__item__wrap").find(".input_LEGAL_REGION").val(region_with_type);

                if ($(this).parents(".b-vacancies__item__wrap").find("input[type='checkbox']").prop("checked")) {
                    $(this).parents(".b-vacancies__item__wrap").find(".input_DELIVERY_BIG_ADDRESS").val(address);
                    $(this).parents(".b-vacancies__item__wrap").find(".input_DELIVERY_REGION").val(region_with_type);
                }



                $(this).parents(".b-vacancies__item__wrap").find(".input_LEGAL_CITY").val(city);
                $(this).parents(".b-vacancies__item__wrap").find(".input_LEGAL_STREET").val(street);
                $(this).parents(".b-vacancies__item__wrap").find(".input_LEGAL_HOME").val(house);
                $(this).parents(".b-vacancies__item__wrap").find(".input_DELIVERY_INDEX").val(postal_code);
                $(this).parents(".b-vacancies__item__wrap").find(".input_LEGAL_INDEX").val(postal_code);

                //адрес доставки
                if ($(this).parents(".b-vacancies__item__wrap").find("input[type='checkbox']").prop("checked")) {
                    $(this).parents(".b-vacancies__item__wrap").find(".input_DELIVERY_CITY").val(city);
                    $(this).parents(".b-vacancies__item__wrap").find(".input_DELIVERY_STREET").val(street);
                    $(this).parents(".b-vacancies__item__wrap").find(".input_DELIVERY_HOME").val(house);
                }




            }
        });
        $(".input_LEGAL_BIG_ADDRESS").suggestions({
            serviceUrl: dadata_serviceUrl_suggestions,
            token: dadata_token,
            type: "ADDRESS",
            count: 15,
            /* Вызывается, когда пользователь выбирает одну из подсказок */
            onSelect: function (suggestion) {


                BIG_ADDRESS_suggestion(suggestion,$(this),"LEGAL");

            }
        });
        $(".input_DELIVERY_BIG_ADDRESS").suggestions({
            serviceUrl: dadata_serviceUrl_suggestions,
            token: dadata_token,
            type: "ADDRESS",
            count: 15,
            /* Вызывается, когда пользователь выбирает одну из подсказок */
            onSelect: function (suggestion) {
                BIG_ADDRESS_suggestion(suggestion,$(this),"DELIVERY");
            }
        });

        function BIG_ADDRESS_suggestion(suggestion,t,type){


            var city = suggestion["data"]["city"];
            var street = suggestion["data"]["street"];
            var house = "";
            if(suggestion["data"]["house"]!=null){
                house=suggestion["data"]["house"];
            }else{
                house="";
            };
            var postal_code = suggestion["data"]["postal_code"];
            var region_with_type = suggestion["data"]["region_with_type"];

            if(type=="LEGAL") {

                t.parents(".b-vacancies__item__wrap").find(".input_LEGAL_CITY").val(city);
                t.parents(".b-vacancies__item__wrap").find(".input_LEGAL_STREET").val(street);
                t.parents(".b-vacancies__item__wrap").find(".input_LEGAL_HOME").val(house);
                t.parents(".b-vacancies__item__wrap").find(".input_DELIVERY_INDEX").val(postal_code);
                t.parents(".b-vacancies__item__wrap").find(".input_LEGAL_INDEX").val(postal_code);
                t.parents(".b-vacancies__item__wrap").find(".input_LEGAL_REGION").val(region_with_type);

            }else{
                t.parents(".b-vacancies__item__wrap").find(".input_DELIVERY_INDEX").val(postal_code);
                t.parents(".b-vacancies__item__wrap").find(".input_DELIVERY_CITY").val(city);
                t.parents(".b-vacancies__item__wrap").find(".input_DELIVERY_STREET").val(street);
                t.parents(".b-vacancies__item__wrap").find(".input_DELIVERY_HOME").val(house);
                t.parents(".b-vacancies__item__wrap").find(".input_DELIVERY_REGION").val(region_with_type);
            }




        }





        //при изменениях в textarea заполняем дублирующее скрытое поле
        $("textarea.input_COMMENT1").keyup(function(eventObject){
            $(this).next().val($(this).val());
        });
        $("textarea.input_COMMENT2").keyup(function(eventObject){
            $(this).next().val($(this).val());
        });



        var serviceUrl = "https://suggestions.dadata.ru/suggestions/api/4_1/rs";
        var token = "3fd748f2a4de3138577435ef0223ac8036b8e334";

        $(".input_HEADER_SURNAME").suggestions({
            serviceUrl: serviceUrl,
            token: token,
            type: "NAME",
            count: 5,
            onSelect: function (suggestion) {
                
            }
        });






    }
    dadata_suggestions_company_and_addresses();







});