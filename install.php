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

    $password = 'php123';
    $hash = md5('XyZzy12*_' . $password);

    $stmt = $pdo->prepare('DELETE FROM users WHERE email = :em');

    $users = array(
        array('Chuck Severance', 'csev@umich.edu'),
        array('UMSI', 'umsi@umich.edu')
    );
    foreach ($users as $user) {
        $stmt->execute(array(':em' => $user[1]));
        $stmt2 = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:nm, :em, :pw)');
        $stmt2->execute(array(':nm' => $user[0], ':em' => $user[1], ':pw' => $hash));
    }

    echo '<p>Database tables created.</p>' . "\n";
    echo '<p>Default accounts: csev@umich.edu / ' . htmlentities($password) . ' and umsi@umich.edu / ' . htmlentities($password) . '</p>' . "\n";
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
