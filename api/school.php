<?php
require_once 'util.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(array(), JSON_PRETTY_PRINT);
    return;
}

$retval = array();
if (isset($_REQUEST['term'])) {
    $stmt = $pdo->prepare('SELECT name FROM Institution WHERE name LIKE :prefix');
    $stmt->execute(array(':prefix' => $_REQUEST['term'] . '%'));
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $retval[] = $row['name'];
    }
}

echo json_encode($retval, JSON_PRETTY_PRINT);
