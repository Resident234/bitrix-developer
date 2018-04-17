
$(function() {

    $(document).on("click",".select_address",function(e){
        e.preventDefault();

        var obj = $(this);

        var full_address=$(this).text();

        var ajaxDiv = obj.closest('.b-popup').find(".ajax_path");
        var addressAjax=ajaxDiv.text();

        var companyId = obj.attr("data-company_id");

        var data = {
            'ID':obj.attr("data-id")
        };



        $.ajax({
            type: "POST",
            data: data,
            url: addressAjax,
            dataType: "json",
            success: function (res) {
                var wrapper;
                if(companyId){
                    wrapper = $(document).find(".btn.save[data-id="+companyId+"]").closest(".b-form");
                }
                else{
                    wrapper = $(document).find(".new_company").closest(".b-form");
                }


                wrapper.find("input[type=checkbox]")[0].checked = false;
                wrapper.find(".jq-checkbox").removeClass("checked");

                full_address=full_address.replace(res.DELIVERY_INDEX+" ", "");

                wrapper.find(".input_DELIVERY_BIG_ADDRESS").val(full_address);
                wrapper.find(".input_DELIVERY_REGION").val(res.DELIVERY_REGION);
                wrapper.find(".input_DELIVERY_CITY").val(res.DELIVERY_CITY);
                wrapper.find(".input_DELIVERY_STREET").val(res.DELIVERY_STREET);
                wrapper.find(".input_DELIVERY_HOME").val(res.DELIVERY_HOME);
                wrapper.find(".input_DELIVERY_APARTMENT").val(res.DELIVERY_APARTMENT);

                wrapper.find(".input_DELIVERY_INDEX").val(res.DELIVERY_INDEX);
                wrapper.find(".input_DELIVERY_FLOOR").val(res.DELIVERY_FLOOR);
                wrapper.find(".input_DELIVERY_PASS").val(res.DELIVERY_PASS);
                wrapper.find(".input_DELIVERY_ELEVATOR").val(res.DELIVERY_ELEVATOR);

                wrapper.find(".address_deliver").val(res.ID);


            }
        });
    });

});