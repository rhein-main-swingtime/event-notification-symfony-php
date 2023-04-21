<?php
header("Content-type: application/json; charset=utf-8");

echo json_encode([
    'status' => ($result ? 'success' : 'error'),
    'message' => ($result ? 'opcache was reset' : 'opcache not reset'),
    'additional' => ob_get_contents(),
], JSON_PRETTY_PRINT);