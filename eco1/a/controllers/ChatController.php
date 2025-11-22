<?php
session_start();
require_once __DIR__ . '/../models/Database.php';

header('Content-Type: application/json');

$pdo = (new Database())->getConnection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

function json_ok($data) {
  echo json_encode($data, JSON_UNESCAPED_UNICODE);
  exit;
}

function json_error($message, $code = 400) {
  http_response_code($code);
  echo json_encode(['error' => $message]);
  exit;
}

switch ($action) {
  case 'get_customer_messages': {
    $customerId = $_SESSION['user_id'] ?? 0;
    if (!$customerId) json_error('Not authenticated', 401);
    $stmt = $pdo->prepare('SELECT id, sender_type, sender_id, customer_id, text, created_at FROM messages WHERE customer_id = :cid ORDER BY id ASC');
    $stmt->execute([':cid' => $customerId]);
    json_ok($stmt->fetchAll(PDO::FETCH_ASSOC));
  }
  case 'send_customer_message': {
    $customerId = $_SESSION['user_id'] ?? 0;
    if (!$customerId) json_error('Not authenticated', 401);
    $text = trim($_POST['text'] ?? '');
    if ($text === '') json_error('Empty message');
    $stmt = $pdo->prepare('INSERT INTO messages (sender_type, sender_id, customer_id, text) VALUES (\'customer\', :sid, :cid, :text)');
    $stmt->execute([':sid' => $customerId, ':cid' => $customerId, ':text' => $text]);
    json_ok(['success' => true]);
  }
  case 'list_customers': {
    $stmt = $pdo->query('SELECT s.id as customer_id, s.name, s.email FROM signup s WHERE EXISTS (SELECT 1 FROM messages m WHERE m.customer_id = s.id) ORDER BY s.name');
    json_ok($stmt->fetchAll(PDO::FETCH_ASSOC));
  }
  case 'get_messages': {
    $customerId = (int)($_GET['customer_id'] ?? 0);
    if ($customerId <= 0) json_error('Missing customer_id');
    $stmt = $pdo->prepare('SELECT id, sender_type, sender_id, customer_id, text, created_at FROM messages WHERE customer_id = :cid ORDER BY id ASC');
    $stmt->execute([':cid' => $customerId]);
    json_ok($stmt->fetchAll(PDO::FETCH_ASSOC));
  }
  case 'send_admin_message': {
    $adminId = $_SESSION['admin_id'] ?? 0;
    if (!$adminId) json_error('Admin not authenticated', 401);
    $customerId = (int)($_POST['customer_id'] ?? 0);
    $text = trim($_POST['text'] ?? '');
    if ($customerId <= 0) json_error('Missing customer_id');
    if ($text === '') json_error('Empty message');
    $stmt = $pdo->prepare('INSERT INTO messages (sender_type, sender_id, customer_id, text) VALUES (\'admin\', :sid, :cid, :text)');
    $stmt->execute([':sid' => $adminId, ':cid' => $customerId, ':text' => $text]);
    json_ok(['success' => true]);
  }
  default:
    json_error('Unknown action');
}