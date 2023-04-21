<?php
header("Content-type: application/json; charset=utf-8");

if ($_SERVER["REMOTE_ADDR"] !== "127.0.0.1") {
    echo json_encode(
        [
            'status' => "nope."
        ]
    );
    die;
}

ob_start();
$result =  opcache_reset();
echo json_encode([
    'status' => ($result ? 'success' : 'error'),
    'message' => ($result ? 'opcache was reset' : 'opcache not reset'),
    'additional' => ob_get_contents(),
], JSON_PRETTY_PRINT);