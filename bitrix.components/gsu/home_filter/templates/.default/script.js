$(function(){

    $(document).on("change","select.producers",function(e){
        e.preventDefault();

        var obj = $(this);
        var address = $(".getProducersPath").val();

        var data = {
            'id':obj.val()
        };

        $.ajax({
            type: "POST",
            url: address,
            dataType: "json",
            data:data,
            success: function (res) {

                var select = $("select.models");
                select.empty();

                $.each(res, function(index, value) {
                    select.prepend($('<option value="' + value.CODE + '">' + value.NAME + '</option>'));
                });

                select.trigger('refresh');

                var propertyId = $(".propertyId").val();
                //var xmlId = $("select.models option:selected").val();
                //$(".b-main-calc__btn").attr("href","/catalog/index.php?USE_FILTER=Y&arrFilter_"+propertyId+"_"+xmlId+"=Y&set_filter=Найти");
                var code = $("select.models option:selected").val();

                $(".b-main-calc__btn").attr("href","/accessories/"+code+"/");
                $(".b-lock").css("display","none");

            }
        });
    });

    $(document).on("change","select.models",function(e){
        e.preventDefault();

        var code = $(this).val();
        var propertyId = $(".propertyId").val();
        $(".b-main-calc__btn").attr("href","/accessories/"+code+"/");

    });
});


