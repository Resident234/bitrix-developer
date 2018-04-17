$(function() {

    //Сразу открываем вспываху
    $.fancybox({href : '#create_email'});
    $(".fancybox-skin").css("padding","30px");

    //ajax запрос
    $(document).on("click","#btn_create_email",function(e){
        e.preventDefault();

        obj = $(this);

        var ajaxDiv = obj.closest('.b-popup').find(".ajax_path");
        var addressAjax=ajaxDiv.text();
        ajaxDiv.empty();

        var data = {
            'email':obj.closest('.b-popup').find(".input_email").val(),
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
            data
        );
    });

    //Закрытие текущего окна, обновление для авторизации родительского
    $(document).on("click","#return_to_site",function(e) {

        var obj = $(this);
        var personalPageAddress = obj.closest('.b-popup').find(".personal").text();

        window.opener.location.href = personalPageAddress;
        window.close();
    });

});