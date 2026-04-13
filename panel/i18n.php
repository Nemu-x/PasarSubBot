<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $requestedLang = strtolower((string) $_GET['lang']);
    if (in_array($requestedLang, ['fa', 'en', 'ru'], true)) {
        $_SESSION['panel_lang'] = $requestedLang;
    }
}

if (!isset($_SESSION['panel_lang'])) {
    $_SESSION['panel_lang'] = 'en';
}

function panelCurrentLanguage(): string
{
    $lang = strtolower((string) ($_SESSION['panel_lang'] ?? 'en'));
    if (!in_array($lang, ['fa', 'en', 'ru'], true)) {
        return 'en';
    }
    return $lang;
}

/**
 * Login page copy (explicit EN/RU/FA — does not rely on runtime_i18n substring replace).
 */
function panelLoginUiStrings(): array
{
    $lang = panelCurrentLanguage();
    $all = [
        'en' => [
            'html_title' => 'Admin panel login',
            'lang_switch' => 'Language',
            'access_title' => 'Login blocked: IP not whitelisted',
            'access_body' => 'This is not a bug. For security, the web panel only opens from one IP at a time. In Telegram, open your bot as admin, find the menu item for setting the panel login IP (sometimes “IP login” / “iplogin”), send the IP address below there, then refresh this page.',
            'your_ip_label' => 'Your current IP (copy this into the bot)',
            'form_title' => 'Bot admin panel',
            'username_ph' => 'Username',
            'password_ph' => 'Password',
            'submit' => 'Sign in',
            'err_user' => 'Invalid username or password.',
            'err_pass' => 'Wrong password.',
        ],
        'ru' => [
            'html_title' => 'Вход в панель',
            'lang_switch' => 'Язык',
            'access_title' => 'Вход закрыт: IP не в белом списке',
            'access_body' => 'Это не ошибка программы. Панель открывается только с разрешённого IP. В Telegram откройте бота как администратор, найдите настройку IP для входа в веб-панель (iplogin), отправьте боту IP ниже, затем обновите страницу.',
            'your_ip_label' => 'Ваш текущий IP (отправьте это боту)',
            'form_title' => 'Панель управления ботом',
            'username_ph' => 'Логин',
            'password_ph' => 'Пароль',
            'submit' => 'Войти',
            'err_user' => 'Неверный логин или пароль.',
            'err_pass' => 'Неверный пароль.',
        ],
        'fa' => [
            'html_title' => 'ورود به پنل مدیریت',
            'lang_switch' => 'زبان',
            'access_title' => 'دسترسی محدود شده (IP)',
            'access_body' => 'این یک خطای برنامه نیست؛ برای امنیت، پنل فقط از یک آی‌پی مشخص باز می‌شود. در تلگرام، ربات را به‌عنوان ادمین باز کنید، گزینهٔ تنظیم آی‌پی ورود به پنل (iplogin) را بزنید، آی‌پی زیر را همانجا بفرستید، سپس این صفحه را رفرش کنید.',
            'your_ip_label' => 'آی‌پی فعلی شما',
            'form_title' => 'پنل مدیریت ربات',
            'username_ph' => 'نام کاربری',
            'password_ph' => 'کلمه عبور',
            'submit' => 'ورود',
            'err_user' => 'نام کاربری یا رمزعبور وارد شده اشتباه است!',
            'err_pass' => 'رمز صحیح نمی باشد',
        ],
    ];
    return $all[$lang] ?? $all['en'];
}

/**
 * Shared admin chrome + dashboard (explicit EN/RU/FA — not runtime_i18n substring replace).
 */
function panelShellStrings(): array
{
    static $all = null;
    if ($all !== null) {
        return $all;
    }

    $all = [
        'logo_prefix' => [
            'en' => 'Bot',
            'ru' => 'Бот',
            'fa' => 'ربات',
        ],
        'logo_name' => [
            'en' => 'Mirza',
            'ru' => 'Мирза',
            'fa' => 'میرزا',
        ],
        'hello' => [
            'en' => 'Hello',
            'ru' => 'Привет',
            'fa' => 'سلام',
        ],
        'settings' => [
            'en' => 'Settings',
            'ru' => 'Настройки',
            'fa' => 'تنظیمات',
        ],
        'logout' => [
            'en' => 'Log out',
            'ru' => 'Выход',
            'fa' => 'خروج',
        ],
        'nav_home' => [
            'en' => 'Home',
            'ru' => 'Главная',
            'fa' => 'صفحه اصلی',
        ],
        'nav_users' => [
            'en' => 'Users',
            'ru' => 'Пользователи',
            'fa' => 'کاربران',
        ],
        'nav_orders' => [
            'en' => 'Orders',
            'ru' => 'Заказы',
            'fa' => 'سفارشات',
        ],
        'nav_services' => [
            'en' => 'Services',
            'ru' => 'Сервисы',
            'fa' => 'سرویس ها',
        ],
        'nav_products' => [
            'en' => 'Products',
            'ru' => 'Товары',
            'fa' => 'محصولات',
        ],
        'nav_transactions' => [
            'en' => 'Transactions',
            'ru' => 'Транзакции',
            'fa' => 'تراکنش ها',
        ],
        'nav_cancel_service' => [
            'en' => 'Delete service',
            'ru' => 'Удаление сервиса',
            'fa' => 'حذف سرویس',
        ],
        'nav_keyboard' => [
            'en' => 'Keyboard layout',
            'ru' => 'Раскладка клавиатуры',
            'fa' => 'چیدمان کیبورد',
        ],
        'page_title_dashboard' => [
            'en' => 'Mirza bot admin panel',
            'ru' => 'Панель управления ботом Мирза',
            'fa' => 'پنل مدیریت ربات میرزا',
        ],
        'stat_users' => [
            'en' => 'Users',
            'ru' => 'Пользователей',
            'fa' => 'تعداد کاربران',
        ],
        'stat_total_sales' => [
            'en' => 'Total sales (count)',
            'ru' => 'Всего продаж (шт.)',
            'fa' => 'تعداد فروش کل',
        ],
        'stat_sum_sales' => [
            'en' => 'Total sales amount',
            'ru' => 'Сумма продаж',
            'fa' => 'جمع کل فروش',
        ],
        'stat_new_users_today' => [
            'en' => 'New users today',
            'ru' => 'Новых пользователей сегодня',
            'fa' => 'کاربران جدید امروز',
        ],
        'chart_sales' => [
            'en' => 'Sales chart',
            'ru' => 'График продаж',
            'fa' => 'چارت فروش',
        ],
        'currency_toman' => [
            'en' => 'Toman',
            'ru' => 'томан',
            'fa' => 'تومان',
        ],
    ];

    return $all;
}

function panelT(string $key): string
{
    $lang = panelCurrentLanguage();
    $all = panelShellStrings();
    if (!isset($all[$key])) {
        return $key;
    }
    $row = $all[$key];

    return $row[$lang] ?? $row['fa'] ?? $key;
}

function panelHtmlAttrs(): array
{
    $lang = panelCurrentLanguage();
    if ($lang === 'fa') {
        return ['lang' => 'fa', 'dir' => 'rtl'];
    }
    if ($lang === 'ru') {
        return ['lang' => 'ru', 'dir' => 'ltr'];
    }

    return ['lang' => 'en', 'dir' => 'ltr'];
}

function panelRuntimeMap(): array
{
    static $map = null;
    if ($map !== null) {
        return $map;
    }

    $runtimePath = __DIR__ . '/../runtime_i18n.json';
    if (!file_exists($runtimePath)) {
        $map = [];
        return $map;
    }

    $decoded = json_decode(file_get_contents($runtimePath), true);
    $map = is_array($decoded) ? $decoded : [];
    return $map;
}

function panelTranslateText(string $text): string
{
    $lang = panelCurrentLanguage();
    if ($lang === 'fa') {
        return $text;
    }

    $hash = sha1($text);
    $map = panelRuntimeMap();
    if (!isset($map[$hash]) || !is_array($map[$hash])) {
        return $text;
    }

    return $map[$hash][$lang] ?? $map[$hash]['fa'] ?? $text;
}

function panelTranslateBuffer(string $buffer): string
{
    $lang = panelCurrentLanguage();
    if ($lang === 'fa') {
        return $buffer;
    }

    static $replacePairs = null;
    if ($replacePairs === null) {
        $replacePairs = [];
        foreach (panelRuntimeMap() as $item) {
            if (!is_array($item)) {
                continue;
            }
            $fa = $item['fa'] ?? null;
            $translated = $item[$lang] ?? null;
            if (!is_string($fa) || $fa === '' || !is_string($translated) || $translated === '') {
                continue;
            }
            $replacePairs[$fa] = $translated;
        }
        // Replace longer strings first to reduce overlap risks.
        uksort($replacePairs, static fn($a, $b) => strlen($b) <=> strlen($a));
    }

    if (empty($replacePairs)) {
        return $buffer;
    }

    return str_replace(array_keys($replacePairs), array_values($replacePairs), $buffer);
}

if (!defined('PANEL_I18N_BUFFER_STARTED')) {
    define('PANEL_I18N_BUFFER_STARTED', true);
    ob_start('panelTranslateBuffer');
}
