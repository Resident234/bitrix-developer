$(function() {

    var dadata_fio=$("#dadata_fio").val();
    var dadata_mail=$("#dadata_mail").val();
    var dadata_phone=$("#dadata_phone").val();




    if($(".b-form__row.hidden").length==0) $("#add_phone").css("display","none");

    $(document).on("click","#save_changes",function(e){
        e.preventDefault();

        obj = $(this);

        var ajaxDiv = obj.closest('.b-form').find(".ajax_path");
        var addressAjax=ajaxDiv.text();
        ajaxDiv.empty();

        var status = 0;
        if(obj.closest('.b-form').find("input.registration_is-opt").prop("checked")) status = 1;

        var data = {
            'name':obj.closest('.b-form').find(".input_name").val(),
            'surname':obj.closest('.b-form').find(".input_surname").val(),
            'secondname':obj.closest('.b-form').find(".input_secondname").val(),
            'email':obj.closest('.b-form').find(".input_email").val(),
            'phone':obj.closest('.b-form').find(".input_phone").val(),
            'there':1
        };
        data["other_phones"]={
            'phone0':obj.closest('.b-form').find(".input_phone.p0").val(),
            'phone1':obj.closest('.b-form').find(".input_phone.p1").val(),
            'phone2':obj.closest('.b-form').find(".input_phone.p2").val()
        };
        console.log(data);

        obj.closest('.b-form').find(".preloader.edit").css({
            "display":"block",
            "height":$('.fancybox-skin').outerHeight(),
            "width":$('.fancybox-skin').outerWidth()});

        obj.closest('.b-form').load(
            addressAjax,
            data,
            function(){
                $(".input_phone").mask("+7 (999) 999-9999");

                var success = parseInt($(".b-form").find(".success").text());
                if(success){
                    $.fancybox({href : '#success_save'});
                    if($(".b-form__row.hidden").length==0) $("#add_phone").css("display","none");
                }
            }
        );
    });

    var counter =0;
    $(document).on("click","#add_phone",function(e){
        e.preventDefault();

            obj = $(this);
            obj.closest('.b-form').find(".b-form__row.hidden:first").removeClass("hidden");
            if($(".b-form__row.hidden").length==0) $("#add_phone").css("display","none");

    });


    //проверка имени, фамилии и отчества на опечатки
    $('.b-cabinet .input_surname').focusout(function(){
        if($(this).val()==""){ return false; };
        var surname=$(this).val();
        var name=$('.b-cabinet .input_name').val();
        var secondname=$('.b-cabinet .input_secondname').val();
        var text=surname+" "+name+" "+secondname;
        var t=$(this);
        $.post( dadata_fio, {name: text})
            .done(function( data ) {
                if(data!="error") {
                    var res=jQuery.parseJSON(data);
                    if(res[0].surname!="") {
                        t.val(res[0].surname);
                    }
                }
            });

    });
    $('.b-cabinet .input_name').focusout(function(){
        if($(this).val()==""){ return false; };
        var surname=$('.b-cabinet .input_surname').val();
        var name=$(this).val();
        var secondname=$('.b-cabinet .input_secondname').val();
        var text=surname+" "+name+" "+secondname;
        var t=$(this);
        $.post( dadata_fio, {name: text})
            .done(function( data ) {
                if(data!="error") {
                    var res=jQuery.parseJSON(data);
                    if(res[0].name!="") {
                        t.val(res[0].name);
                    }
                }
            });

    });
    $('.b-cabinet .input_secondname').focusout(function(){
        if($(this).val()==""){ return false; };
        var surname=$('.b-cabinet .input_surname').val();
        var name=$('.b-cabinet .input_name').val();
        var secondname=$(this).val();
        var text=surname+" "+name+" "+secondname;
        var t=$(this);
        $.post( dadata_fio, {name: text})
            .done(function( data ) {
                if(data!="error") {
                    var res=jQuery.parseJSON(data);

                    if(res[0].patronymic!="") {
                        t.val(res[0].patronymic);
                    }
                }
            });

    });

    //проверка e-mail
    $('.b-cabinet .input_email').focusout(function(){
        if($(this).val()==""){ return false; };
        var mail=$(this).val();
        var t=$(this);
        $.post( dadata_mail, {mail: mail})
            .done(function( data ) {
                if(data!="error") {

                    var res=jQuery.parseJSON(data);
                    if(res[0].email!=null) {
                        t.val(res[0].email);
                    }
                }
            });

    });

var serviceUrl = "https://suggestions.dadata.ru/suggestions/api/4_1/rs";
var token = "3fd748f2a4de3138577435ef0223ac8036b8e334";

    $(".input_surname, .input_name, .input_secondname").suggestions({
        serviceUrl: serviceUrl,
        token: token,
        type: "NAME",
        count: 5,
        onSelect: function (suggestion) {
            console.log("---++---");
            console.log(suggestion);
            console.log("---++---");
            $(this).parents(".b-form").find(".input_surname").val(suggestion["data"]["surname"]);
            $(this).parents(".b-form").find(".input_name").val(suggestion["data"]["name"]);
            $(this).parents(".b-form").find(".input_secondname").val(suggestion["data"]["patronymic"]);



        }
    });







});