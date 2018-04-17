<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);?>

    <h2 class="h2">Заявка</h2>

    <div class="about-price">
        Заполните форму и получите доступ к оптовым ценам.
        <br>На указанную Вами почту в течении нескольких минут будет выслан прайс со всеми позициями склада.
        Полученный прайс-лист так же используется в качестве заявки.
        <br>Кроме этого Вы становитесь участником акции «2 месяца бесплатных доставок».
    </div>
    <form class="form" action="" method="POST">
        <div class="txt-justify">
            <div class="form-element">
                <input id="FIO" type="text" name="name" class="inp-txt" required placeholder="ФИО">
            </div>
            <div class="form-element">
                <input id="Phone" type="text" name="phone" class="inp-txt" required placeholder="Телефон">
            </div>
            <div class="form-element">
                <input id="Mail" type="email" name="email" class="inp-txt" required
                       placeholder="Электронная почта">
            </div>
        </div>

        <input type="hidden" id="utm_source" value="<?= $arResult["UTM_SOURCE"] ?>">
        <input type="hidden" id="utm_medium" value="<?= $arResult["UTM_MEDIUM"] ?>">
        <input type="hidden" id="utm_campaign" value="<?= $arResult["UTM_CAMPAIGN"] ?>">
        <input type="hidden" id="utm_content" value="<?= $arResult["UTM_CONTENT"] ?>">
        <input type="hidden" id="utm_term" value="<?= $arResult["UTM_TERM"]?>">


        <input type="hidden" id="ajax" value="<?=$componentPath."/ajax.php"?>">
        <input type="hidden" id="formtype" value="" />
        <input type="hidden" id="site" value="<?="http://".$_SERVER['HTTP_HOST']?>" />
        <input type="hidden" id="ip" value="<?=$_SERVER['REMOTE_ADDR']?>" />

        <button id="get-price" class="link-filled">ПОЛУЧИТЬ ПРАЙС</button>

        <!--input type="submit" class="link-filled" value="ПОЛУЧИТЬ ПРАЙС"-->
        <div class="form-hint">Все поля обязательны для заполнения.</div>
    </form>
    <div class="txt-center">
        <a href="#" title="" class="logo-foot"></a>

        <div class="otdel-head">Отдел оптовых продаж</div>
        <div class="address-foot">
            г. Москва, ул. Складочная, д. 1, стр. 18, оф. 118, +7 (499) 322-03-81, +7 (499) 322-03-84
            <br>e-mail: <a href="mailto:opt@yardcompany.ru" title="">opt@yardcompany.ru</a>
        </div>
    </div>

<!-- /Price-list -->