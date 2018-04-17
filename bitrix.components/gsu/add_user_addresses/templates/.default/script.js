$(function() {

    var arFields = ["INDEX","REGION","CITY","STREET","HOME","APARTMENT","FLOOR","ELEVATOR","PASS","BLOCK","BUILDING"];

    $(document).on("click",".new_address",function(e){
        e.preventDefault();

        var obj = $(this);
        var wrapper = obj.closest('.b-form');
        var wrapperWrapper =  obj.closest('.b-vacancies__item');

        var data ={
            'there':1
        };
        $.each(arFields, function( index, value ) {
            if((value.indexOf("PASS")!=-1)||(value.indexOf("ELEVATOR")!=-1)){
                data[value] = obj.closest('.b-form').find("select.input_"+value).val();
            }
            else data[value] = obj.closest('.b-form').find(".input_"+value).val();
        });

        wrapper.find(".preloader.address").css({
            "display":"block",
            "height":$('.fancybox-skin').outerHeight(),
            "width":$('.fancybox-skin').outerWidth()});

        var ajaxDiv = wrapper.find(".ajax_path");
        var addressAjax=ajaxDiv.text();
        ajaxDiv.empty();

        wrapperWrapper.load(
            addressAjax,
            data,
            function() {
                $("select").styler();
                if(parseInt(wrapperWrapper.find(".success").text())) {
                    location.reload()
                }
                dadata_clear_address();
            }
        );
    });

    $(document).on("click",".undo_address",function(e){
        e.preventDefault();
        obj = $(this);

        obj.closest('.b-vacancies__item').slideUp();
        $(".add_address").css("display","inline-block");


    });

    function dadata_clear_address() {
        //проверка адреса
        $('.input_INDEX, .input_REGION, .input_CITY, .input_STREET, .input_HOME, .input_APARTMENT').focusout(function () {

            var index = $(this).parents(".b-vacancies__item__wrap").find(".input_INDEX").val();
            var region = $(this).parents(".b-vacancies__item__wrap").find(".input_REGION").val();
            var city = $(this).parents(".b-vacancies__item__wrap").find(".input_CITY").val();
            var street = $(this).parents(".b-vacancies__item__wrap").find(".input_STREET").val();
            var home = $(this).parents(".b-vacancies__item__wrap").find(".input_HOME").val();
            var apartment = $(this).parents(".b-vacancies__item__wrap").find(".input_APARTMENT").val();

            var index_obj = $(this).parents(".b-vacancies__item__wrap").find(".input_INDEX");
            var region_obj = $(this).parents(".b-vacancies__item__wrap").find(".input_REGION");
            var city_obj = $(this).parents(".b-vacancies__item__wrap").find(".input_CITY");
            var street_obj = $(this).parents(".b-vacancies__item__wrap").find(".input_STREET");
            var home_obj = $(this).parents(".b-vacancies__item__wrap").find(".input_HOME");
            var apartment_obj = $(this).parents(".b-vacancies__item__wrap").find(".input_APARTMENT");


            var text = "";
            if (index != "") {
                text = text + index;
            }
            if (region != "") {
                text = text + " " + region;
            }
            if (city != "") {
                text = text + " г." + city;
            }
            if (street != "") {
                text = text + " ул." + street;
            }
            if (home != "") {
                text = text + " д." + home;
            }
            if (apartment != "") {
                text = text + " кв." + apartment;
            }
            if (text == "") {
                return false;
            };

            var path_to_component=$("#path-to-component").val();
            
            $.post(path_to_component, {address: text})
                .done(function (data) {
                    if (data != "error") {

                        var res = jQuery.parseJSON(data);
                        if (res[0].postal_code != null) {
                            index_obj.val(res[0].postal_code);
                        }
                        if (res[0].region != null) {
                            region_obj.val(res[0].region);
                        }
                        if (res[0].city != null) {
                            city_obj.val(res[0].city);
                        }
                        if (res[0].street != null) {
                            street_obj.val(res[0].street);
                        }
                        if (res[0].house != null) {
                            home_obj.val(res[0].house);
                        }
                        if (res[0].flat != null) {
                            apartment_obj.val(res[0].flat);
                        }
                    }
                });


        });
    }
    dadata_clear_address();



});