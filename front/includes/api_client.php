<?php

function api(string $endpoint, string $method = 'GET', array $postData = [], array $files = []): array {
    $apiBase = getenv('API_URL') ?: 'http://api:80';
    $url     = rtrim($apiBase, '/') . '/' . ltrim($endpoint, '/');

    $cookieHeader = '';
    if (session_id()) {
        $cookieHeader = session_name() . '=' . session_id();
    }

    $method = strtoupper($method);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    if ($cookieHeader) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookieHeader);
    }

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($files)) {
            $payload = $postData;
            foreach ($files as $key => $file) {
                $payload[$key] = new CURLFile($file['tmp_name'], $file['type'], $file['name']);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }
    } elseif ($method === 'PUT') {
        $postData['_method'] = 'PUT';
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($files)) {
            $payload = $postData;
            foreach ($files as $key => $file) {
                $payload[$key] = new CURLFile($file['tmp_name'], $file['type'], $file['name']);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        }
    } elseif ($method === 'DELETE') {
        $postData['_method'] = 'DELETE';
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    }

    $body   = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $decoded = json_decode($body, true);
    return $decoded ?? ['error' => 'Invalid API response', '_status' => $status];
}
