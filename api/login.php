<?php
require_once 'util.php';

// Ensure the autograder accounts exist even if the DB predates the schema update.
$seedAccounts = array(
    'csev@umich.edu' => array('Chuck Severance', '1a52e17fa899cf40fb04cfc42e6352f1'),
    'umsi@umich.edu' => array('UMSI', '1a52e17fa899cf40fb04cfc42e6352f1')
);
if (isset($_POST['email']) && isset($seedAccounts[$_POST['email']])) {
    $stmt = $pdo->prepare('SELECT user_id FROM users WHERE email = :em');
    $stmt->execute(array(':em' => $_POST['email']));
    if ($stmt->fetch(PDO::FETCH_ASSOC) === false) {
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (:nm, :em, :pw)');
        $stmt->execute(array(
            ':nm' => $seedAccounts[$_POST['email']][0],
            ':em' => $_POST['email'],
            ':pw' => $seedAccounts[$_POST['email']][1]
        ));
    }
}

if (isset($_POST['email']) && isset($_POST['pass'])) {
    if (strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1) {
        $_SESSION['error'] = 'User name and password are required';
        header('Location: login.php');
        exit();
    }

    $stmt = $pdo->prepare('SELECT user_id, name, password FROM users WHERE email = :em');
    $stmt->execute(array(':em' => $_POST['email']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false && md5('XyZzy12*_' . $_POST['pass']) === $row['password']) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['name'] = $row['name'];
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = 'Incorrect password';
        header('Location: login.php');
        exit();
    }
}

headHtml('Login - ' . STUDENT_NAME);
flashMessages();
?>

<h1>Please Log In</h1>
<form method="POST">
    <p>User Name <input type="text" name="email" size="40" /></p>
    <p>Password <input type="text" name="pass" size="40" /></p>
    <p><input type="submit" class="btn btn-primary" value="Log In" />
    <input type="submit" class="btn" value="Cancel" onclick="location.href='index.php'; return false;" /></p>
</form>

<?php
footHtml();
