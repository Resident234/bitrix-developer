$(function(){
	var companyPropId = $(".companyPropId").val();
	var companyConsPropId = $(".companyConsPropId").val();
	var consigneePropId = $(".consigneePropId").val();
	var addressPropId = $(".addressPropId").val();
	var otherCompanyVal = $(".otherTransportCompany").val();
	var transportCompanyVal = $(".transportCompany").val();
	var mapContainer=$('#map_wrapper2');
	if(mapContainer.length)
	{
		ymaps.ready(init);
	}
	//валидация полей
	$(document).on("click", ".b-order__buttons .btn_red", function(e) {
		var result = true;
		var step = $(".b-order__step");
		if(step.hasClass("step2")) { //если шаг "Оформление заказа"
			$(".b-order__step input[type=text]").each(function(){
				var parent = $(this).closest(".b-form__row__input");
				var name = parent.attr("name");
				var self = $(this);
				//if((parent.attr("name").indexOf("ORDER_PROP_36")==-1)&&(parent.attr("name").indexOf("ORDER_PROP_38")==-1)) {
				if(self.hasClass('required'))
				{
					if (parent.hasClass("email_field")) {
						var re = /^[\w-\.]+@[\w-]+\.[a-z]{2,4}$/i;
						if ($(this).val() == "") {
							result = false;
							parent.addClass("error");
							$(".b-form__row__input.email_field .b-form__row__input__error").html('Нужно заполнить!');
						}
						else if (!re.test($(this).val())) {
							result = false;
							parent.addClass("error");
							$(".b-form__row__input.email_field .b-form__row__input__error").html('Поле заполнено некорректно!');
						}
						else {
							parent.removeClass("error");
						}
					}
					else if ($(this).val() == "") {
						result = false;
						parent.addClass("error");
					}
					else {
						parent.removeClass("error");
					}
				}
				//}
			});
			$(".b-order__step input[type=checkbox]:last").each(function(){
				var parent = $(this).closest(".b-form__row_check");
				if(!$(".b-order__step input[type=checkbox]:last").prop("checked"))
				{
					result = false;
					parent.addClass("error");
				}
				else {
					parent.removeClass("error");
				}
			});
			$('textarea.b-form__row__input').each(function(){
				var parent = $(this).closest(".b-form__row__textarea");
				if(
					$("select.company_select_"+companyPropId).find('option:selected').data('id')==$(this).data('val') ||
					$("select.company_select_"+companyConsPropId).find('option:selected').data('id')==$(this).data('val')
				)
				{
					if($(this).val()=="")
					{
						result = false;
						parent.addClass('error');
					}
					else
					{
						parent.removeClass("error");
						if($("select.company_select_"+companyPropId).find('option:selected').data('id')==$(this).data('val'))
						{
							$(".company_input_"+companyPropId).val($(this).val());
						}
						else if($("select.company_select_"+companyConsPropId).find('option:selected').data('id')==$(this).data('val'))
						{
							$(".company_input_"+companyConsPropId).val($(this).val());
						}
					}
				}
			});
		}
		else if(step.hasClass("step3")){ //если шаг "Способ доставки"
			if($("input:radio:checked").length)
			{
				$('.b-form__row__input__error.delivery__error').css({
					display:'none'
				});
				$(".b-delivery__item.dropped input[type=text].REQ").each(function(){
					if($(this).val() == "")
					{
						result = false;
						$(this).closest(".b-form__row__input").addClass("error");
					}
					else {
						$(this).closest(".b-form__row__input").removeClass("error");
					}
				});
				//этот код нужен для исправления косяка битрикса.
				//если свойства заказа заполняются не из шага 2, то добавляются скрытые поля с пустыми значениями, их мы и заполняем.
				$(".b-delivery__item.dropped input[type=text]").each(function(){
					if($(this).val() != "")
					{
						var val = $(this).val();
						var name = $(this).prop("name");
						$("input[name="+name+"]").val(val);
					}
				});
				$(".b-delivery__item.dropped input[type=hidden]").each(function(){
					if($(this).val() != "")
					{
						var val = $(this).val();
						var name = $(this).prop("name");
						$("input[name="+name+"]").val(val);
					}
				});
			}
			else {
				$('.b-form__row__input__error.delivery__error').css({
					display:'block'
				});
				result = false;
			}
		}
		else if(step.hasClass("step4")) //если шаг "Способ оплаты"
		{
			if(!$("input:radio:checked").length)
			{
				result = false;
			}
		}
		return result;
	});
	//исправляем косяк битрикса. если нажали назад, то подставляем правильное значение для скрытого инпута
	$(document).on("click", ".backButton", function(e) {
		$(".CurrentStep").val($(".CurrentStep").val()-2);
	});
	$(document).on("click", ".b-delivery__show_map", function(){
		$(".b-map #map_wrapper1").toggle();
		return false;
	});
	$(document).on("change", ".b-delivery__punkt__sel select", function(){
		$(".pickup-description").hide();
		$(".pickup"+$(this).val()).show();
		$(".pickup-description").each(function(){
			if($(this).is(':visible'))
			{
				$(".RESULT_ADDRESS").val($(this).find(".ADDRESS").val());
				$(".RESULT_PHONE").val($(this).find(".PHONE").val());
				$('.deliveryInfo').val($('#PICK_UP_POINT_DELIVERY').find('.pickup-description:visible .pickupDelivery').val());
			}
		});
	});
	// очищаем значение textarea (иначе не показывается плэйсхолдер)
	$('.b-order__step.step2 textarea.b-form__row__input').text("");
    //Устанавливаю компанию плательщика
    $(".company_input_"+companyPropId).val($("select.company_select_"+companyPropId).val());
    $(document).on("change", "select.company_select_"+companyPropId, function(){
		if($(this).find('option:selected').data('id')) {
			$('.b-form__row__input.email_field ').parent(".b-form__row").css({
				clear: 'both'
			});
			$('textarea[data-val="' + $(this).find('option:selected').data('id') + '"]').fadeIn();
		}
		else
		{
			$(".company_input_"+companyPropId).val($("select.company_select_"+companyPropId).val());
			$(this).parents(".b-form__row").find('textarea.b-form__row__input').fadeOut();
			$(this).parents(".b-form__row").find(".b-form__row__textarea").removeClass('error');
		}
    });
    //Устанавливаю компанию грузополучателя
    $(".company_input_"+companyConsPropId).val($("select.company_select_"+companyConsPropId).val());
    $(document).on("change", "select.company_select_"+companyConsPropId, function(){
		if($(this).find('option:selected').data('id'))
		{
			$('.b-form__row__input.email_field ').parent(".b-form__row").css({
				clear:'both'
			});
			$('textarea[data-val="'+$(this).find('option:selected').data('id')+'"]').fadeIn();
		}
		else
		{
			$(".company_input_"+companyConsPropId).val($("select.company_select_"+companyConsPropId).val());
			$(this).parents(".b-form__row").find('textarea.b-form__row__input').fadeOut();
			$(this).parents(".b-form__row").find(".b-form__row__textarea").removeClass('error');
		}
    });
    //Устанавливаю грузополучателя
    $(".consignee_input_"+consigneePropId).val($("select.consignee_select_"+consigneePropId).val());
    $(document).on("change", "select.consignee_select_"+consigneePropId, function(){

        var selectVal = $("select.consignee_select_"+consigneePropId).val();
        if(selectVal.indexOf("other")==-1)
            $(".consignee_input_"+consigneePropId).val(selectVal);
        else {
            var obj = $(this);
            var rowBlock = obj.closest(".b-form__row");
            var inputBlock = rowBlock.find(".b-form__row__input");
            var errorBlock = rowBlock.find(".b-form__row__input__error");
            errorBlock.remove();
            inputBlock.addClass("error");
            inputBlock.append("<div class=\"b-form__row__input__error\">Укажите в комментари ниже</div>");
            $(".textearea_order").css("display","block");
        }
    });
    //Устанавливаю аддресс
    $(".address_input_"+addressPropId).val($("select.address_select_"+addressPropId).val());
    $(document).on("change", "select.address_select_"+addressPropId, function(){
        var selectVal = $("select.address_select_"+addressPropId).val(),cityContainer,streetContainer,buildingContainer,
			homeContainer,blockContainer,apartmentContainer;
		if(addressPropId==74) // опт
		{
			cityContainer = $('input[name=ORDER_PROP_52]'),
			streetContainer = $('input[name=ORDER_PROP_53]'),
			buildingContainer = $('input[name=ORDER_PROP_55]'),
			homeContainer = $('input[name=ORDER_PROP_54]'),
			blockContainer = $('input[name=ORDER_PROP_56]'),
			apartmentContainer = $('input[name=ORDER_PROP_57]');
		}
		else if(addressPropId==75) // розница
		{
			cityContainer = $('input[name=ORDER_PROP_46]'),
			streetContainer = $('input[name=ORDER_PROP_47]'),
			buildingContainer = $('input[name=ORDER_PROP_49]'),
			homeContainer = $('input[name=ORDER_PROP_48]'),
			blockContainer = $('input[name=ORDER_PROP_50]'),
			apartmentContainer = $('input[name=ORDER_PROP_51]');
		}
		var self = $(this), rowBlock = self.closest(".b-form__row"),inputBlock = rowBlock.find(".b-form__row__input"),
			errorBlock = rowBlock.find(".b-form__row__input__error");
        if(selectVal.indexOf("other")==-1)
		{
			$(".address_input_"+addressPropId).val(selectVal);
			var selected = self.find('option:selected');
			// подставляем значения из адреса
			if(selected.length)
			{
				if(selected.data('city'))
				{
					if(cityContainer.length)
					{
						cityContainer.val(selected.data('city'));
					}
				}
				if(selected.data('street'))
				{
					if(streetContainer.length)
					{
						streetContainer.val(selected.data('street'));
					}
				}
				if(selected.data('home'))
				{
					if(homeContainer.length)
					{
						homeContainer.val(selected.data('home'));
					}
				}
				if(selected.data('building'))
				{
					if(buildingContainer.length)
					{
						buildingContainer.val(selected.data('building'));
					}
				}
				if(selected.data('block'))
				{
					if(blockContainer.length)
					{
						blockContainer.val(selected.data('block'));
					}
				}
				if(selected.data('apartment'))
				{
					if(apartmentContainer.length)
					{
						apartmentContainer.val(selected.data('apartment'));
					}
				}
			}
			inputBlock.removeClass('error');
		}
		else
		{
			inputBlock.addClass("error");
			errorBlock.remove();
			inputBlock.append("<div class=\"b-form__row__input__error\">Укажите ниже</div>");
			$(".textearea_order").css("display","block");
			// очищаем значения, подставленные из адреса и подставляем город, выбранный в шапке
			if(cityContainer.length)
			{
				cityContainer.val(cityContainer.data('value'));
			}
			if(streetContainer.length)
			{
				streetContainer.val("");
			}
			if(homeContainer.length)
			{
				homeContainer.val("");
			}
			if(buildingContainer.length)
			{
				buildingContainer.val("");
			}
			if(blockContainer.length)
			{
				blockContainer.val("");
			}
			if(apartmentContainer.length)
			{
				apartmentContainer.val("");
			}
		}
    });
    //Если выбрали другого перевозчика
	$(document).on("change", "select[name='ORDER_PROP_"+transportCompanyVal+"']", function(){
		var selectVal = $("select[name='ORDER_PROP_"+transportCompanyVal+"']").val();
		if(selectVal.indexOf(otherCompanyVal)!=-1){
			var obj = $(this);
			var rowBlock = obj.closest(".b-form__row");
			var inputBlock = rowBlock.find(".b-form__row__input");
			var errorBlock = rowBlock.find(".b-form__row__input__error");
			inputBlock.addClass("error");
			errorBlock.remove();
			inputBlock.append("<div class=\"b-form__row__input__error\">Укажите в комментари ниже</div>");
			$(".textearea_order").css("display","block");
		}
	});
	function init()
	{
		var map;
		map = new ymaps.Map('map_wrapper2', {
			center:   [ 57.98, 56.26 ],
			zoom: 9,
			controls: ['smallMapDefaultSet','zoomControl'],
			scrollZoom: false,
			type: 'yandex#map'
		});
		var myPlacemark, selectedPlacemark;
		$('#PICK_UP_POINT_DELIVERY').find("option").each(function(){
			var self =$(this);
			myPlacemark = new ymaps.Placemark([self.data('lat'),self.data('log')],
				{
					hintContent: '<b>Адрес:'+self.data('address')+
					'</b><br/><b>Номер телефона: </b>'+self.data('phone')+
					'<br/><b>Время работы: </b>'+self.data('worktime')+
					'<br/><b>Как проехать: </b>'+self.data('proezd-info')+
					'<br/><b>Срок доставки: </b>'+self.data('srok-dostavki')+' дн.'
				},
				{
					preset: 'twirl#circleIcon',
					iconColor: '#0099ff'
				}
			)	;
			myPlacemark.events.add('click', function (e) {
				$(".b-delivery__punkt__sel select option").each(function () {
					$(this).attr('selected', false);
				});
				$(".b-delivery__punkt__sel select").find("option[value='"+self.val()+"']").attr("selected", "selected");
				$(".b-delivery__punkt__sel select").trigger('refresh');
				$(".b-delivery__punkt__sel select").trigger('change');
				var thisPlacemark = e.get('target');
				if (selectedPlacemark)
				{
					selectedPlacemark.options.set({
						iconColor: '#0099ff',
						zIndex:675
					});
				}
				selectedPlacemark = thisPlacemark;
				thisPlacemark.options.set({
					iconColor: '#cf5b61',
					zIndex: 700
				});
			});
			map.geoObjects.add(myPlacemark);
		});
		var prevPlacemark;
		$('.b-delivery__punkt__sel select').on('change',function(){
			var self = $(this);
			var index =self.find('option:selected').index();
			if(prevPlacemark)
			{
				prevPlacemark.options.set({
					iconColor: '#0099ff',
					zIndex: 675
				});
			}
			prevPlacemark = map.geoObjects.get(index);
			map.geoObjects.get(index).options.set({
				iconColor: '#cf5b61',
				zIndex: 700
			});
		});
		$(document).on('change','input[id*=ID_DELIVERY_ID]',function(){
			$('.b-form__row__input__error.delivery__error').css({
				display:'none'
			});
			var thisId = $(this).attr('id');
			if(thisId=="ID_DELIVERY_ID_7" || thisId=="ID_DELIVERY_ID_14")
			{
				$('.deliveryInfo').val($('#PICK_UP_POINT_DELIVERY').find('.pickup-description:visible .pickupDelivery').val());
				setTimeout(function () {
					map.setBounds(map.geoObjects.getBounds())
				},200);
			}
		});
	}

});

function ChangeGenerate(val)
{
    if(val)
        document.getElementById("sof_choose_login").style.display='none';
    else
        document.getElementById("sof_choose_login").style.display='block';

    try{document.order_reg_form.NEW_LOGIN.focus();}catch(e){}
}