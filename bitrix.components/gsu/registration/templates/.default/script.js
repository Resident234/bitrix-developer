$(function() {

    $(document).on("click",".btn_registration",function(e){
        e.preventDefault();

        obj = $(this);

        var ajaxDiv = obj.closest('.b-popup').find(".ajax_path");
        var addressAjax=ajaxDiv.text();
        ajaxDiv.empty();

        var status = 0;
        if(obj.closest('.b-popup').find("input.registration_is-opt").prop("checked")) status = 1;

        var data = {
            'name':obj.closest('.b-popup').find(".input_name").val(),
            'surname':obj.closest('.b-popup').find(".input_surname").val(),
            'email':obj.closest('.b-popup').find(".input_email").val(),
            'phone':obj.closest('.b-popup').find(".input_phone").val(),
            'is_opt':status,
            'there':1
        };


        obj.closest('.b-popup').find(".preloader").css({
            "display":"block",
            "height":$('.fancybox-skin').outerHeight(),
            "width":$('.fancybox-skin').outerWidth()});

        obj.closest('.b-popup').load(
            addressAjax,
            data,
            function(){
                $(".input_phone").mask("+7 (999) 999-9999");
                $.fancybox.update();

                $("select,input[type=checkbox],input[type=radio]").styler(
                    {
                        onSelectClosed:function(){
                            var type=$(this).find("li.selected").data("type");
                            $(this).closest(".b-cat-line__sort").removeClass("asc").removeClass("desc").addClass(type);
                        }
                    });
            }
        );
    });

});