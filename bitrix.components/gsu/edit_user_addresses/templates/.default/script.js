$(function() {

    $(document).on("click",".save_address",function(e){
        e.preventDefault();

        var obj = $(this);
        var wrapper = obj.closest('.b-vacancies__item');

        var ajaxDiv = obj.closest('.b-form').find(".ajax_path");
        var addressAjax=ajaxDiv.text();


        var arFields = ["INDEX","REGION","CITY","STREET","HOME","APARTMENT","FLOOR","ELEVATOR","PASS"];

        var data ={
            'ID': obj.attr("data-id"),
            'NAME':wrapper.find(".b-vacancies__item__name").text(),
            'there':1
        };
        $.each(arFields, function(index, value) {
            if((value.indexOf("PASS")!=-1)||(value.indexOf("ELEVATOR")!=-1)){
                data[value] = obj.closest('.b-form').find("select.input_"+value).val();
            }
            else data[value] = obj.closest('.b-form').find(".input_"+value).val();
        });




        obj.closest('.b-form').find(".preloader.address").css({
            "display":"block",
            "height":$('.fancybox-skin').outerHeight(),
            "width":$('.fancybox-skin').outerWidth()});

        wrapper.load(
            addressAjax,
            data,
            function() {
                $("select").styler();
                if(parseInt(wrapper.find(".success").text())) {
                    $.fancybox({href : '#success_save'});
                }
            }
        );
    });
    $(document).on("click",".delete_address",function(e){
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

    });

});