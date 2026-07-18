<?php
require_once 'util.php';

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
