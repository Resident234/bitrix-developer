$(function() {

    $.fancybox({href : '#create_password'});
    $(".fancybox-skin").css("padding","30px");

    $(document).on("click","#btn_edit_password",function(e){
        e.preventDefault();

        obj = $(this);

        var ajaxDiv = obj.closest('.b-popup').find(".ajax_path");
        var addressAjax=ajaxDiv.text();
        ajaxDiv.empty();

        var data = {
            'password':obj.closest('.b-popup').find(".input_password").val(),
            'repeat_password':obj.closest('.b-popup').find(".input_password_repeat").val(),
            'ID':obj.closest('.b-popup').find(".id").text(),
            'CONFIRM_CODE':obj.closest('.b-popup').find(".confirm_code").text(),
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
            }
        );
    });

});