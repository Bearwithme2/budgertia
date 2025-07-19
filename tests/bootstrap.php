<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
}

$secret = $_SERVER['JWT_SECRET_KEY'] ?? null;
$public = $_SERVER['JWT_PUBLIC_KEY'] ?? null;
if ($secret && str_contains($secret, '%kernel.project_dir%')) {
    $secret = str_replace('%kernel.project_dir%', dirname(__DIR__), $secret);
    $_SERVER['JWT_SECRET_KEY'] = $secret;
    putenv('JWT_SECRET_KEY=' . $secret);
}
if ($public && str_contains($public, '%kernel.project_dir%')) {
    $public = str_replace('%kernel.project_dir%', dirname(__DIR__), $public);
    $_SERVER['JWT_PUBLIC_KEY'] = $public;
    putenv('JWT_PUBLIC_KEY=' . $public);
}
if ($secret && $public && !file_exists($secret)) {
    $dir = dirname($secret);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $config = [
        'private_key_bits' => 4096,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ];
    $res = openssl_pkey_new($config);
    if ($res !== false) {
        openssl_pkey_export($res, $privateKey);
        $details = openssl_pkey_get_details($res);
        $publicKey = $details['key'] ?? '';
        file_put_contents($secret, $privateKey);
        file_put_contents($public, $publicKey);
    }
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}
