$(document).ready(function () {
    $('body').on('click', '[data-form-open]', function (event) {
        event.preventDefault();
        var formId = $(this).data('form-open');

        if (typeof formId != 'undefined') {
            $('#form_' + formId).arcticmodal({
                beforeOpen: function (data, el) {
                    var form = $(el).find('form');
                    form.find('[data-mask]').each(function () {
                        var mask = $(this).data('mask');
                        mask = mask.replace(/#/g, "9");
                        $(this).mask(mask);
                    });
                }
            });
        }
        return false;
    });
    
    $('body').on('submit', '[data-form]', function (event) {
        event.preventDefault();
        var formId = $(this).data('form');
        $.ajax({
            type: 'post',
            data: $(this).serialize(),
            success: function (data) {
                $("#form_" + formId).html(data)
                .find('[data-mask]').each(function () {
                        var mask = $(this).data('mask');
                        mask = mask.replace(/#/g, "9");
                        $(this).mask(mask);
                });
            }
            
        });
        return false;
    });
});


