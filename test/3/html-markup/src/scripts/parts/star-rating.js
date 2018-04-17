$(function () {
    (function () {
        var $link = $('.js-star-rating__item');
        var strStarActiveClass = "star-rating__item_active";
        var $button = $('.js-star-rating__button');

        $link.click(function () {

            var $parent = $(this).closest(".js-star-rating__list");

            $link.removeClass(strStarActiveClass);
            $(this).addClass(strStarActiveClass);

            var intCurrentNum = $(this).data("num");
            setRating($parent, intCurrentNum);

            return false;

        });

        $button.click(function () {
            var intCurrentRating = $(this).closest(".js-star-rating__container").find(".js-star-rating__list").data("current-rating");
            localStorage.setItem("currentRating", intCurrentRating);

            return false;

        });

        $(document).on("ready", function() {
            if(localStorage.getItem('currentRating') !== null){
                setRating($(".js-star-rating__list"), localStorage.getItem("currentRating"));
                /** если на странице будет несколько контролов , то к блоку нужно будет обращаться не по
                 * js-star-rating__list , а добавить класс типа js-star-rating-[id контролла] и
                 * обращаться уже по нему */
            }

        });


        function setRating(parent, value){
            for(var i = 1; i <= value; i++){
                parent.find(".js-star-rating__item-" + i).addClass(strStarActiveClass);
            };

            parent.attr("data-current-rating", value);
            var $container = parent.closest(".js-star-rating__container");
            $container.find(".js-rating-selector").prop('selectedIndex', parseInt(value));
        }

    })();
    
});