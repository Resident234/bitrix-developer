$(function () {

    (function () {

        var pageIsLoading = false;
        var paginationContainerForTrigger = $('.js-pagination-container');
        var link = paginationContainerForTrigger.find('.ajax-link');
        var linkGlobal = link;
        var paginationWrap = $(".js-pagination-wrap");
        var page = link.data('page');
        var countPages = paginationContainerForTrigger.data('count-pages');

        $('body').on('click', '.ajax-link', function (e) {
            e.preventDefault();
            ajaxLoadContent($(this), "append");
        });

        $(document).scroll(function () {
            var targetPos = paginationContainerForTrigger.offset().top;
            var winHeight = $(window).height();
            var winScrollTop = $(document).scrollTop();
            var scrollToElem = targetPos - winHeight
            if ((typeof(pageIsLoading) != 'undefined') && pageIsLoading) {
                return false;
            }

            if(page > countPages) return false;

            if (winScrollTop >= (scrollToElem + ($(window).height() / 3))) {
                /** когда кнопка окажется на трети видимой части экрана,
                 * считая снизу, то сработает догрузка */
                if ($('.ajax-link').length) {
                    ajaxLoadContent(linkGlobal, "append");
                }
            }

        });

        $(document).on("click", ".js-pagination-wrap a", function (event) {
            var a = $(this);
            page = a.attr('data-page');
            ajaxLoadContent(a, "html");
            return false;
        });

        function ajaxLoadContent($link, type){
            var link = $link;
            var strTitleNormal = linkGlobal.data("title-normal");
            var strTitleLoading = linkGlobal.data("title-loading");

            pageIsLoading = true;
            $(".js-preloader-Wait").show();
            linkGlobal.html(strTitleLoading);

            var linkUrl = link.attr('href');
            $.get(link.attr('href'), function (data) {
                $(".js-preloader-Wait").hide();
                linkGlobal.html(strTitleNormal);
                pageIsLoading = false;

                history.pushState(null, null, linkUrl);

                paginationWrap.find(".btn.btn_green-border").removeClass("btn_green-border").addClass("btn_green");
                paginationWrap.find(".btn[data-page='" + page + "']").removeClass("btn_green").addClass("btn_green-border");

                var response = $(data);
                if (type == "append") {
                    $('.ajax-list').append(response.find('.ajax-list').html());
                } else {

                    $('.ajax-list').html(response.find('.ajax-list').html());
                    if (page < countPages) {
                        linkGlobal.show();
                    }
                }

                if (response.find('.ajax-link').length) {
                    linkGlobal.attr('href', response.find('.ajax-link').attr('href'));
                    page = Number(page) + 1;
                    linkGlobal.attr("data-page", String(page));//parseInt(page) + 1

                }
                else {
                    //link.remove();
                    linkGlobal.hide();
                    page = Number(page) + 1;
                }



            });
        }

    })();


});