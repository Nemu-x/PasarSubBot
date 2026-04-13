<?php
/**
 * Применить глобальный язык бота в таблице `setting` (как в админке: EN и RU взаимоисключающие).
 *
 * Использование:
 *   php scripts/apply-default-language.php en|ru|fa
 *
 * Примечание: при обоих флагах 0 в languagechange() используется глобальный персидский (FA).
 * EN/RU у отдельных пользователей — через Telegram language_code / поле user.language.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

$root = dirname(__DIR__);
require_once $root . '/config.php';

$lang = strtolower($argv[1] ?? '');
if (!in_array($lang, ['en', 'ru', 'fa'], true)) {
    fwrite(STDERR, "Usage: php scripts/apply-default-language.php en|ru|fa\n");
    exit(1);
}

if ($lang === 'fa') {
    $languageen = '0';
    $languageru = '0';
} else {
    $languageen = $lang === 'en' ? '1' : '0';
    $languageru = $lang === 'ru' ? '1' : '0';
}

$stmt = $pdo->prepare('UPDATE setting SET languageen = :en, languageru = :ru LIMIT 1');
$stmt->execute(['en' => $languageen, 'ru' => $languageru]);

echo "OK: setting.languageen={$languageen}, setting.languageru={$languageru}\n";
exit(0);
