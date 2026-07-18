<?php
require_once 'util.php';
requireLogin();

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = 'Missing profile_id';
    header('Location: index.php');
    return;
}

$profile = loadProfile($pdo, $_GET['profile_id']);
if ($profile === false) {
    $_SESSION['error'] = 'Profile not found';
    header('Location: index.php');
    return;
}
if ($profile['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = 'ACCESS DENIED';
    header('Location: index.php');
    return;
}

if (isset($_POST['delete'])) {
    $stmt = $pdo->prepare('DELETE FROM Profile WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $_GET['profile_id']));
    $_SESSION['success'] = 'Profile deleted';
    header('Location: index.php');
    return;
}

headHtml('Delete Profile - ' . STUDENT_NAME);
flashMessages();
?>

<h1>Delete Profile</h1>
<p>First Name: <?php echo htmlentities($profile['first_name']); ?></p>
<p>Last Name: <?php echo htmlentities($profile['last_name']); ?></p>
<p>Are you sure you want to delete this profile?</p>
<form method="POST">
    <input type="hidden" name="delete" value="1" />
    <input type="submit" class="btn btn-danger" value="Delete" />
    <input type="submit" class="btn btn-default" value="Cancel" onclick="location.href='index.php'; return false;" />
</form>

<?php
footHtml();
