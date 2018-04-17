/*
$(window).on("load",function(){

    grecaptcha.render('captcha_message', {
        'sitekey': '6Lek-gUTAAAAAKnurb3JEp8KXS0L9R29T60OCgqe',
        'theme': 'light'
    });
});
$(function() {

    $(document).on("click","#btn_send_mes",function(e){
        e.preventDefault();

        obj = $(this);

        var ajaxDiv = obj.closest('.b-popup').find(".ajax_path");
        var addressAjax=ajaxDiv.text();
        ajaxDiv.empty();
        console.log(addressAjax);

        var data = {
            'name':obj.closest('.b-popup').find(".input_name").val(),
            'email':obj.closest('.b-popup').find(".input_email").val(),
            'message':obj.closest('.b-popup').find(".input_message").val(),

            'product-id':obj.closest('.b-popup').find(".product-id").val(),
            'product-name':obj.closest('.b-popup').find(".product-name").val(),
            'user-name':obj.closest('.b-popup').find(".user-name").val(),
            'user-email':obj.closest('.b-popup').find(".user-email").val(),

            'captcha':obj.closest('.b-popup').find("#form_captcha_mess").serialize(),
            'there':1
        };
        console.log(data);
        obj.closest('.b-popup').find(".preloader").css({
            "display":"block",
            "height":$('.fancybox-skin').outerHeight(),
            "width":$('.fancybox-skin').outerWidth()
        });

        obj.closest('.b-popup').load(
            addressAjax,
            data,
            function(){

                captcha_mess = grecaptcha.render('captcha_message', {
                    'sitekey': '6Lek-gUTAAAAAKnurb3JEp8KXS0L9R29T60OCgqe',
                    'theme': 'light'
                });
            }
        );
    });

});*/
