<?php
require_once 'util.php';

if (isset($_POST['email']) && isset($_POST['pass'])) {
    $stmt = $pdo->prepare('SELECT user_id, name, password FROM users WHERE email = :em');
    $stmt->execute(array(':em' => $_POST['email']));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row !== false && md5($_POST['pass']) === $row['password']) {
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['success'] = 'Logged in.';
        header('Location: index.php');
        return;
    } else {
        $_SESSION['error'] = 'Incorrect email or password';
        header('Location: login.php');
        return;
    }
}

headHtml('Login - ' . STUDENT_NAME);
flashMessages();
?>

<h1>Please Log In</h1>
<form method="POST">
    <p>Email: <input type="text" name="email" size="40" /></p>
    <p>Password: <input type="password" name="pass" size="40" /></p>
    <p><input type="submit" class="btn btn-primary" value="Log In" />
    <input type="submit" class="btn" value="Cancel" onclick="location.href='index.php'; return false;" /></p>
</form>

<?php
footHtml();
