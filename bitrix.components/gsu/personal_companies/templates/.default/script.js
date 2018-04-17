$(function(){
    $(document).on("click",".add_company",function(e) {
        e.preventDefault();

        obj = $(this);
        $('.b-vacancies__item.new').slideDown();
        obj.css("display","none");
    });
});/**
 * Created by Рома on 18.06.2015.
 */
