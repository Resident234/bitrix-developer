$(function () {
    var url = $('.tab-wrapper').data('url');
    ymaps.ready(init);

    $(document).on('click', '.b-popup__city-change__item', function (e) {
        e.preventDefault();
        $(".preloader_delivery").show();
        var self = $(this);
        $.ajax({
            type: "POST",
            data: {
                'CITY_ID': $(this).data("id")
            },
            url: '/local/components/peppers/city_change/ajax/change_city.php',
            success: function (data) {
                if (data.CITY) {
                    $(".b-header__place__current__link").text(data.CITY);
                    $(".b-sticky__city .open_popup").text(data.CITY);
                    $(".b-order__city-fsel .open_popup").text(data.CITY);
                    $(".fancybox-close").click();
                }
            },
            error: function (e) {
                console.log(e);
            },
            complete: function (e) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        'is_ajax': 'Y'
                    },
                    success: function (data) {
                        
                        $('.js-template').html(data);
                        $('input, select').styler();
                        $(document).on('change', '.b-delivery__radio input', function () {
                            var obj = $(this);
                            var del = obj.closest('.b-delivery__item');
                            if (!del.hasClass('dropped')) {
                                $(".b-delivery__item").removeClass('dropped');
                                del.addClass('dropped');
                                $('.b-delivery__wrap').slideUp();
                                del.find('.b-delivery__wrap').slideDown();
                            }
                        });
                        $('.tab-buttons').find('a').on('click', function (e) {
                            var obj = $(this);
                            var buttons_wrapper = obj.closest(".tab-buttons");
                            var tabs_wrapper = $(obj.closest(".tab-buttons").data('tabwrapper'));
                            var obj_to_show = tabs_wrapper.find('>.' + obj.data('tab'));

                            buttons_wrapper.find('a').removeClass('current');
                            obj.addClass('current');
                            tabs_wrapper.find('>.tab').removeClass('current');
                            obj_to_show.addClass('current');

                            e.preventDefault();
                        });
                        init();

                        $('.preloader_delivery').hide();
                    }
                });
            }
        });
    });

    function init() {
        var mapContainer = $('.pvz_map');
        if (mapContainer.length) {
            var map, mapId = mapContainer.attr('id');
            map = new ymaps.Map(mapId, {
                center: [57.98, 56.26],
                zoom: 9,
                controls: ['smallMapDefaultSet', 'zoomControl'],
                scrollZoom: false,
                type: 'yandex#map'
            });

            $(document).on('change', 'input[id*=RETAIL_DELIVERY_ID]', function () {
                var thisId = $(this).attr('id');
                if (thisId == "RETAIL_DELIVERY_ID_7" || thisId == "RETAIL_DELIVERY_ID_14") {
                    setTimeout(function () {
                        map.setBounds(map.geoObjects.getBounds(), {checkZoomRange: true}).then(
                            function () {
                                "use strict";
                                map.setZoom(map.getZoom() - 1);
                            }
                        );
                    }, 100);
                }
            });

            var myPlacemark, selectedPlacemark, pointDescDisplay;
            $('#RETAIL_PICK_UP_POINT_DELIVERY').find("option").each(function () {
                var self = $(this), hintContent = '<div class="pickup-placemark-description">';
                if (self.data('address')) {
                    hintContent += '<b>Адрес:</b>' + self.data('address');
                }
                if (self.data('phone')) {
                    hintContent += '<br/><b>Номер телефона: </b>' + self.data('phone');
                }
                if (self.data('worktime')) {
                    hintContent += '<br/><b>Время работы: </b>' + self.data('worktime');
                }
                if (self.data('proezd-info')) {
                    hintContent += '<br/><b>Как проехать: </b>' + self.data('proezd-info');
                }
                if (self.data('srok-dostavki')) {
                    hintContent += '<br/><b>Срок доставки: </b>' + self.data('srok-dostavki') + ' дн.';
                }
                hintContent += '</div>';
                pointDescDisplay = $('.pickup-description.pickup' + self.val()).css('display');
                myPlacemark = new ymaps.Placemark([self.data('lat'), self.data('log')],
                    {
                        hintContent: hintContent
                    },
                    {
                        preset: 'twirl#circleIcon',
                        iconColor: (pointDescDisplay !== 'block') ? '#0099ff' : '#cf5b61',
                        zIndex: (pointDescDisplay !== 'block') ? 675 : 700
                    }
                );
                if (pointDescDisplay === 'block') {
                    selectedPlacemark = myPlacemark;
                }
                myPlacemark.events.add('click', function (e) {
                    $('.pickup-description').css({
                        'display': 'none'
                    });
                    $('.pickup-description.pickup' + self.val()).css({
                        'display': 'block'
                    });
                    var thisPlacemark = e.get('target');
                    if (selectedPlacemark) {
                        selectedPlacemark.options.set({
                            iconColor: '#0099ff',
                            zIndex: 675
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
            $('.b-delivery__punkt__sel select').on('change', function () {
                var self = $(this);
                var index = self.find('option:selected').index();
                if (prevPlacemark) {
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

            // масштабируем карту после обновления аяксом
            if ($('.b-delivery__item__pick_up_point').hasClass('dropped')) {
                setTimeout(function () {
                    map.setBounds(map.geoObjects.getBounds(), {checkZoomRange: true}).then(
                        function () {
                            "use strict";
                            map.setZoom(map.getZoom() - 1);
                        }
                    );
                }, 100);
            }
        }
    }

});