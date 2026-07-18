<?php
require_once 'pdo.php';

headHtml('Install Database - ' . STUDENT_NAME);

try {
    $sql = file_get_contents('database.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $statement) {
        if (strlen($statement) > 0) {
            $pdo->exec($statement);
        }
    }

    $email = 'admin@example.com';
    $password = 'php123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $name = 'Administrator';

    $stmt = $pdo->prepare('DELETE FROM users WHERE email = :em');
    $stmt->execute(array(':em' => $email));

    $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:nm, :em, :pw)');
    $stmt->execute(array(':nm' => $name, ':em' => $email, ':pw' => $hash));

    echo '<p>Database tables created.</p>' . "\n";
    echo '<p>Default account: ' . htmlentities($email) . ' / ' . htmlentities($password) . '</p>' . "\n";
    echo '<p><a href="index.php">Go to Index</a></p>' . "\n";
} catch (PDOException $e) {
    echo '<p style="color:red;">Error: ' . htmlentities($e->getMessage()) . '</p>' . "\n";
}

footHtml();

function headHtml($title)
{
    echo '<!DOCTYPE html>' . "\n";
    echo '<html><head><title>' . htmlentities($title) . '</title></head><body>' . "\n";
    echo '<div class="container">' . "\n";
}

function footHtml()
{
    echo '</div></body></html>' . "\n";
}
