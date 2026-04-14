<?php
require_once '../includes/db.php';
require_once '../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$email    = trim($data['email'] ?? '');
$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

if (!$email || !$username || !$password) {
    json_response(['error' => 'All fields are required'], 400);
}

$pdo  = create_pdo();
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);

if ($stmt->fetch()) {
    json_response(['error' => 'Cette adresse email est déjà utilisée.'], 409);
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO users (username, email, role, password_hash) VALUES (:username, :email, 'user', :hash)")
    ->execute(['username' => $username, 'email' => $email, 'hash' => $hash]);

json_response(['success' => true]);
