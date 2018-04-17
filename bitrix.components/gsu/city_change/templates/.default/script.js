$().ready(function() {
    $(".b-popup__city-change__search input").keyup(function(e){
        $.ajax({
            type: "POST",
            data: {'INPUT': $(".b-popup__city-change__search input").val() },
            url: '/local/components/peppers/city_change/ajax/change_input.php',
            success: function (data) {
                $(".b-popup__city-change__item").remove();
                $(".b-popup__city-error").remove();
                $(".b-popup__city-change__list").append(data);
            },
            error: function(e) {
                console.log(e);
            }
        });
    });

    $(document).on("click", '.b-popup__city-change__item', function(e) {
		e.preventDefault();
        $.ajax({
            type: "POST",
            data: {
				'CITY_ID': $(this).data("id")
			},
            url: '/local/components/peppers/city_change/ajax/change_city.php',
            success: function (data) {
                if(data.CITY) {
                    $(".b-header__place__current__link").text(data.CITY);
                    $(".b-sticky__city .open_popup").text(data.CITY);
                    $(".b-order__city-fsel .open_popup").text(data.CITY);
                    $(".fancybox-close").click();
                }
            },
            error: function(e) {
                console.log(e);
            }
        });
    });
});