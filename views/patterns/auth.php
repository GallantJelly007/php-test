<div id="auth-panel-load" class="load-cont load-back d-flex ajc-center d-none">
    <div class="load">
        <div class="load-center"></div>
    </div>
</div>
<ul class="switch-menu d-flex w-100 bb-1 pos-rel" data-menu-area="menu-container">
    <li class="switch-menu-item ph-20 pv-10 w-50 flex-1 active-switch-menu-item t-center" data-panel="auth-panel">Вход</li>
    <li class="switch-menu-item ph-20 pv-10 w-50 flex-1 t-center" data-panel="reg-panel">Регистрация</li>
    <div class="switch-h-hr"></div>
</ul>
<div id="menu-container" class="ph-20 pv-10 w-100 pos-rel">
    <button class="popup-button close-button" data-target="auth">&#10006;</button>
    <form id="auth-panel" class="menu-block d-flex fd-col ai-center">
        <h3 class="fw-bold t-center mt-10 mb-05">Авторизация</h3>
        <div id="auth-result" class="result p-10 w-100 fsz-08 d-none"></div>
        <input class="text-input mb-10 mt-20 w-100" type="text" name="login" placeholder="E-mail">
        <input class="text-input mb-10 w-100" type="password" name="pass" placeholder="Пароль">
        <a href="#">Забыли пароль?</a>
        <button type="button" id="auth-button" class="fill-button mt-20">Войти</button>
    </form>
    <form id="reg-panel" class="menu-block d-flex fd-col ai-center d-none">
        <h3 class="fw-bold t-center mt-10 mb-05">Регистрация</h3>
        <div id="reg-result" class="result p-10 w-100 fsz-08 d-none"></div>
        <input class="text-input w-100 mt-20" type="text" name="name" placeholder="Имя">
        <p class="warning mb-05 as-start" data-target="name"></p>
        <input class="text-input w-100" type="text" name="surname" placeholder="Фамилия">
        <p class="warning mb-05 as-start" data-target="surname"></p>
        <input class="text-input w-100" type="text" name="email" placeholder="E-mail">
        <p class="warning mb-05 as-start" data-target="email"></p>
        <input id="new-pass" class="text-input w-100" type="new-password" name="password" placeholder="Пароль" data-relative="repeat-pass">
        <p class="warning mb-05 as-start" data-target="password"></p>
        <input id="repeat-pass" class="text-input w-100" type="new-password" name="repeat-pass" placeholder="Повторите пароль" data-relative="new-pass">
        <p class="warning mb-05 as-start" data-target="repeat-pass"></p>
        <div class="d-flex ai-center mt-10 as-start">
            <input type="checkbox" name="policy" data-required>
            <p class="fsz-06 ml-05">Я согласен с <a href="#">политикой конфиденциальности</a> и <a href="#">правилами использования</a> сайта</p>
        </div>
        <button type="button" id="reg-button" class="fill-button mt-20" disabled>Зарегистрироваться</button>
    </form>
</div>