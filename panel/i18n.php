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
