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

        var data = {
            'name':obj.closest('.b-popup').find(".input_name").val(),
            'email':obj.closest('.b-popup').find(".input_email").val(),
            'message':obj.closest('.b-popup').find(".input_message").val(),
            'manager_id':obj.closest('.b-popup').find(".managerId").text(),
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

});