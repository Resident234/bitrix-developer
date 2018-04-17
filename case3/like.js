$(function () {

    //Click on filters
    (function () {
        $(document).on("click", ".js-like", function () {
            var $button = $(this);
            var $parent = $button.closest(".js-news-wrap__item");
            var $blockUsers = $parent.find(".js-news-item__line");

            $.post($button.data("handler-path"), { newId: $button.data("new-id"), iblockId: $button.data("iblock-id") }, function (data) {
                if (data.content){ $blockUsers.html(data.content); }
                if (data.action === "like") {
                    $button.addClass("btn_red");
                    $button.removeClass("btn_green");
                    $button.html($button.data("dislike-label"));
                } else if (data.action === "dislike") {
                    $button.removeClass("btn_red");
                    $button.addClass("btn_green");
                    $button.html($button.data("like-label"));
                }
            }, "json");
        });
    })();



});