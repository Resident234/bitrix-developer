$(window).on("load",function(){

    grecaptcha.render('captcha_letter', {
        'sitekey': '6Lek-gUTAAAAAKnurb3JEp8KXS0L9R29T60OCgqe',
        'theme': 'light'
    });
});

$(function() {

    $(document).on("click","#btn_send_letter",function(e){
        e.preventDefault();

        obj = $(this);

        var ajaxDiv = obj.closest('.b-popup').find(".ajax_path");
        var addressAjax=ajaxDiv.text();
        ajaxDiv.empty();

        var data = {
            'name':obj.closest('.b-popup').find(".input_name").val(),
            'email':obj.closest('.b-popup').find(".input_email").val(),
            'message':obj.closest('.b-popup').find(".input_message").val(),
            'captcha':obj.closest('.b-popup').find("#form_captcha_letter").serialize(),
            'there':1
        };

        obj.closest('.b-popup').find(".preloader").css({
            "display":"block",
            "height":$('.fancybox-skin').outerHeight(),
            "width":$('.fancybox-skin').outerWidth()
        });
        obj.closest('.b-popup').load(
            addressAjax,
            data,
            function(){

                grecaptcha.render('captcha_letter', {
                    'sitekey': '6Lek-gUTAAAAAKnurb3JEp8KXS0L9R29T60OCgqe',
                    'theme': 'light'
                });
            }
        );
    });

});