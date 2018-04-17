<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); ?>
<?
use Bitrix\Main\Localization\Loc as Loc;
Loc::loadMessages(__FILE__);
$this->setFrameMode(true);
?>



<div class="social_widget_container">
    <?if(trim($arResult['HREF_VK']) != ''){?>
        <a href="<?=$arResult['HREF_VK']?>" target="_blank"><img class="icons-social" src="<?=$templateFolder?>/images/vk.png"></a>
    <?} else {?>
        <div class="social_widget social_widget1" style="width: <?=$arResult['WIDTH_VK'];?>px;">

            <script type="text/javascript" src="//vk.com/js/api/openapi.js?136"></script>

            <!-- VK Widget -->
            <div id="vk_groups"></div>
            <script type="text/javascript">
                VK.Widgets.Group("vk_groups", {mode: 4, width: <?=$arResult['WIDTH_VK'];?>, height: <?=$arResult['HEIGHT_VK'];?>}, <?=$arResult['GROUP_VK'];?>);
            </script>
        </div>
    <?}?>

    <?if(trim($arResult['HREF_OK']) != ''){?>
        <a href="<?=$arResult['HREF_OK']?>" target="_blank"><img class="icons-social" src="<?=$templateFolder?>/images/ok.png"></a>
    <?} else {?>
        <div class="social_widget social_widget2" style="width: <?=$arResult['WIDTH_OK'];?>px; ">

            <div id="ok_group_widget"></div>
            <script>
                !function (d, id, did, st) {
                    var js = d.createElement("script");
                    js.src = "https://connect.ok.ru/connect.js";
                    js.onload = js.onreadystatechange = function () {
                        if (!this.readyState || this.readyState == "loaded" || this.readyState == "complete") {
                            if (!this.executed) {
                                this.executed = true;
                                setTimeout(function () {
                                    OK.CONNECT.insertGroupWidget(id,did,st);
                                }, 0);
                            }
                        }}
                    d.documentElement.appendChild(js);
                }(document,"ok_group_widget",<?=$arResult['GROUP_OK'];?>,'{"width":"<?=$arResult['WIDTH_OK'];?>","height":"<?=$arResult['HEIGHT_OK'];?>"}');
            </script>

        </div>
    <?}?>

    <?if(trim($arResult['HREF_INSTAGRAM']) != ''){?>
        <a href="<?=$arResult['HREF_INSTAGRAM']?>" target="_blank"><img class="icons-social" src="<?=$templateFolder?>/images/ins.png"></a>
    <?} else {?>
        <div class="social_widget social_widget3" style="width: <?=$arResult['WIDTH_INSTAGRAM'];?>px;">

            <iframe src='//<?=$_SERVER["HTTP_HOST"]?><?=$componentPath; ?>/inwidget/index.php?login=<?=$arResult['LOGIN_INSTAGRAM'];?>&token=<?=$arResult['TOKEN_INSTAGRAM'];?>' scrolling='no'
                    frameborder='no' style='border:none;width:<?=$arResult['WIDTH_INSTAGRAM'];?>px;height:<?=$arResult['HEIGHT_INSTAGRAM'];?>px;overflow:hidden;'></iframe>
        </div>
    <?}?>

    <?if(trim($arResult['HREF_FACEBOOK']) != ''){?>
        <a href="<?=$arResult['HREF_FACEBOOK']?>" target="_blank"><img class="icons-social" src="<?=$templateFolder?>/images/fb.png"></a>
    <?} else {?>
        <div class="social_widget social_widget4" style="width: <?=$arResult['WIDTH_FACEBOOK'];?>px;">
            <div id="fb-root"></div>
            <script>(function(d, s, id) {
                    var js, fjs = d.getElementsByTagName(s)[0];
                    if (d.getElementById(id)) return;
                    js = d.createElement(s); js.id = id;
                    js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.8&appId=864194156925452";
                    fjs.parentNode.insertBefore(js, fjs);
                }(document, 'script', 'facebook-jssdk'));</script>
            <div class="fb-page" data-href="<?=$arResult['GROUP_FACEBOOK'];?>"
                 data-tabs="timeline" data-width="<?=$arResult['WIDTH_FACEBOOK'];?>" data-height="<?=$arResult['HEIGHT_FACEBOOK'];?>" data-small-header="false" data-adapt-container-width="true"
                 data-hide-cover="false" data-show-facepile="true">
                <blockquote cite="<?=$arResult['GROUP_FACEBOOK'];?>" class="fb-xfbml-parse-ignore">
                    <a href="<?=$arResult['GROUP_FACEBOOK'];?>"></a>
                </blockquote>
            </div>

        </div>
    <?}?>

    <?if(trim($arResult['HREF_YOUTUBE']) != ''){?>
        <a href="<?=$arResult['HREF_YOUTUBE']?>" target="_blank"><img class="icons-social" src="<?=$templateFolder?>/images/yt.png"></a>
    <?} else {?>
        <div class="social_widget social_widget5" style="width: <?=$arResult['WIDTH_YOUTUBE'];?>px;">
            <script src="https://apis.google.com/js/platform.js"></script>

            <script>
                function onYtEvent(payload) {
                    if (payload.eventType == 'subscribe') {
                        // Add code to handle subscribe event.
                    } else if (payload.eventType == 'unsubscribe') {
                        // Add code to handle unsubscribe event.
                    }
                    if (window.console) { // for debugging only
                        window.console.log('YT event: ', payload);
                    }
                }
            </script>
            <div class="block">
                <a href="https://www.youtube.com/channel/<?=$arResult['CHANNEL_YOUTUBE'];?>" class="ico_ytb ico ytb_bottom" target="_blank" rel="nofollow"></a>
                <a href="https://www.youtube.com/channel/<?=$arResult['CHANNEL_YOUTUBE'];?>" class="title ytb_bottom" target="_blank" rel="nofollow">youtube</a>
                <p class="count">¬»ƒ≈Œ- ¿Õ¿À <br>
                    “ƒ  ”«Õ≈÷€ –Œ——»»</p>


            </div>
            <div class="ytsubscribe_container">
                <div class="g-ytsubscribe" data-channelid="<?=$arResult['CHANNEL_YOUTUBE'];?>" data-layout="full" data-theme="dark"
                     data-count="default" data-onytevent="onYtEvent"></div>
            </div>
        </div>
    <?}?>


</div>