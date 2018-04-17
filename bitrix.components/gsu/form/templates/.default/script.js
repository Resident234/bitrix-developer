$(function() {

    $(document).on("click",".btn.save",function(e){
        e.preventDefault();

        var obj = $(this);
        var wrapperClassName = obj.attr('data-class');
        var wrapper =  obj.closest("." + wrapperClassName);



        var ajaxDiv = wrapper.find(".ajax_submit");
        var addressAjax=ajaxDiv.text();
        ajaxDiv.empty();


        var form_description_address = "/local/templates/.default/ajax_forms_descriptions/company_form.php";

        $.ajax({
            type: "POST",
            data:{},
            url: form_description_address,
            dataType: "json",
            success: function (res) {

                var data ={
                    'ID': obj.attr("data-id"),
                    'WRAP_CLASS': obj.attr('data-class'),
                    'IS_SUBMIT':1
                };

                $.each(res, function(index, value ) {
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
                    function() {
                        if(parseInt($('.b-form').find(".success").text())==1) {
                            $.fancybox({href : '#success_save'});
                            $(".fancybox-skin").css("padding","30px");
                        }
                        wrapper.find("select").styler();
                        $(".input_phone").mask("+7 (999) 999-9999");
                    }
                );

            }
        });

    });
    /*$(document).on("click",".delete_address",function(e){
        e.preventDefault();
        var obj = $(this);
        var data = {'id' : obj.attr("data-id")};
        var url = obj.closest(".b-form").find(".delete_path").text();

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: "json",
            success: function (res) {

                if(res) obj.closest(".b-vacancies__item").slideUp(function(){});
            }
        });

    });*/

});