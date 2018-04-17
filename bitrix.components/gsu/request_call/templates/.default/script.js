$(function() {

    $(document).on("click","#btn_request_call",function(e){
        e.preventDefault();

        obj = $(this);

        var ajaxDiv = obj.closest('.b-popup').find(".ajax_path");
        var addressAjax=ajaxDiv.text();
        ajaxDiv.empty();

        var data = {
            'name':obj.closest('.b-popup').find(".input_name").val(),
            'phone':obj.closest('.b-popup').find(".input_phone").val(),
            'manager_id':obj.attr("data-manager"),
            'there':1
        };
		if(obj.closest('.b-popup').find('input[name="request_call_e-mail"]').length)
		{
			data['email']=obj.closest('.b-popup').find('input[name="request_call_e-mail"]').val();
		}
		if(obj.closest('.b-popup').find('input[name="request_call_userId"]').length)
		{
			data['userId']=obj.closest('.b-popup').find('input[name="request_call_userId"]').val();
		}

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