<?php
require_once 'util.php';

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

$positions = loadPositions($pdo, $profile['profile_id']);
$education = loadEducation($pdo, $profile['profile_id']);

headHtml('View Profile - ' . STUDENT_NAME);
flashMessages();
?>

<h1>Profile for <?php echo htmlentities($profile['first_name'] . ' ' . $profile['last_name']); ?></h1>

<p><strong>Email:</strong> <?php echo htmlentities($profile['email']); ?></p>
<p><strong>Headline:</strong> <?php echo htmlentities($profile['headline']); ?></p>
<p><strong>Summary:</strong><br />
<?php echo nl2br(htmlentities($profile['summary'])); ?></p>

<h2>Education</h2>
<ul>
<?php
if (count($education) == 0) {
    echo '<li>None</li>' . "\n";
}
foreach ($education as $edu) {
    echo '<li>' . htmlentities($edu['year']) . ': ' . htmlentities($edu['school']) . '</li>' . "\n";
}
?>
</ul>

<h2>Positions</h2>
<ul>
<?php
if (count($positions) == 0) {
    echo '<li>None</li>' . "\n";
}
foreach ($positions as $pos) {
    echo '<li>' . htmlentities($pos['year']) . ': ' . htmlentities($pos['description']) . '</li>' . "\n";
}
?>
</ul>

<p><a href="index.php" class="btn btn-default">Back</a></p>

<?php
footHtml();
