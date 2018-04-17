$(function () {

    $(document).on("click", ".btn_restore", function (e) {
        e.preventDefault();

        obj = $(this);

        var ajaxDiv = obj.closest('.b-wrap').find(".ajax_path");
        var addressAjax = ajaxDiv.val();
        ajaxDiv.empty();

        console.log(addressAjax);
        var data = {
            'email': obj.closest('.b-wrap').find(".input_email").val(),
            'there': 1
        };

        obj.closest('.b-wrap').find(".preloader").css({
            "display": "block"
        });

        obj.closest('.b-wrap').load(
            addressAjax,
            data,
            function () {

            }
        );
    });

});