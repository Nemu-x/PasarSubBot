<?php
/**
 * Применить глобальный язык бота в таблице `setting` (как в админке: EN и RU взаимоисключающие).
 *
 * Использование:
 *   php scripts/apply-default-language.php en|ru|fa
 *
 * Не подключает config.php целиком — на части серверов в CLI нет ext-mysqli, а там вызывается mysqli_connect().
 * Парсятся $dbhost, $dbname, $usernamedb, $passworddb из config.php и используется только PDO.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

if (!extension_loaded('pdo_mysql')) {
    fwrite(STDERR, "PHP CLI has no pdo_mysql driver (needed for PDO to MySQL).\n");
    fwrite(STDERR, "Install: sudo apt install php-mysql\n");
    fwrite(STDERR, "Or match your PHP version: php -v  then  sudo apt install php8.3-mysql\n");
    fwrite(STDERR, "Verify: php -m | grep pdo_mysql\n");
    exit(1);
}

$root = dirname(__DIR__);
$configPath = $root . '/config.php';
if (!is_readable($configPath)) {
    fwrite(STDERR, "Cannot read: $configPath\n");
    exit(1);
}
$configRaw = file_get_contents($configPath);

$readVar = static function (string $name, string $raw): ?string {
    if (preg_match('/\$' . preg_quote($name, '/') . "\s*=\s*'((?:\\\\.|[^'\\\\])*)'\s*;/", $raw, $m)) {
        return stripcslashes($m[1]);
    }
    return null;
};

$dbhost = $readVar('dbhost', $configRaw);
$dbname = $readVar('dbname', $configRaw);
$usernamedb = $readVar('usernamedb', $configRaw);
$passworddb = $readVar('passworddb', $configRaw);

if ($dbhost === null || $dbname === null || $usernamedb === null || $passworddb === null) {
    fwrite(STDERR, "Could not parse db credentials from config.php (expected \$dbhost, \$dbname, \$usernamedb, \$passworddb with single-quoted values).\n");
    exit(1);
}

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
$dsn = "mysql:host={$dbhost};dbname={$dbname};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $usernamedb, $passworddb, $options);
} catch (PDOException $e) {
    fwrite(STDERR, 'PDO connection failed: ' . $e->getMessage() . "\n");
    if (stripos($e->getMessage(), 'could not find driver') !== false) {
        fwrite(STDERR, "Hint: enable pdo_mysql for CLI (see above).\n");
    }
    exit(1);
}

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
