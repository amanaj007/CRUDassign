<?php
require_once 'util.php';
requireLogin();

if (isset($_POST['profile_id']) && isset($_POST['first_name']) &&
    isset($_POST['last_name']) && isset($_POST['email']) &&
    isset($_POST['headline']) && isset($_POST['summary'])) {

    $profile_id = $_POST['profile_id'];

    $profile = loadProfile($pdo, $profile_id);
    if ($profile === false || $profile['user_id'] != $_SESSION['user_id']) {
        $_SESSION['error'] = 'ACCESS DENIED';
        header('Location: index.php');
        return;
    }

    $msg = validateProfile();
    if ($msg !== true) {
        $_SESSION['error'] = $msg;
        header('Location: edit.php?profile_id=' . urlencode($profile_id));
        return;
    }

    $msg = validatePositions();
    if ($msg !== true) {
        $_SESSION['error'] = $msg;
        header('Location: edit.php?profile_id=' . urlencode($profile_id));
        return;
    }

    $msg = validateEducation();
    if ($msg !== true) {
        $_SESSION['error'] = $msg;
        header('Location: edit.php?profile_id=' . urlencode($profile_id));
        return;
    }

    $stmt = $pdo->prepare(
        'UPDATE Profile SET ' .
        'first_name = :fn, last_name = :ln, email = :em, ' .
        'headline = :he, summary = :su ' .
        'WHERE profile_id = :pid'
    );
    $stmt->execute(
        array(
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'],
            ':pid' => $profile_id
        )
    );

    deletePositions($pdo, $profile_id);
    insertPositions($pdo, $profile_id);

    deleteEducation($pdo, $profile_id);
    insertEducation($pdo, $profile_id);

    $_SESSION['success'] = 'Profile updated';
    header('Location: view.php?profile_id=' . urlencode($profile_id));
    return;
}

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

$positions = loadPositions($pdo, $profile['profile_id']);
$education = loadEducation($pdo, $profile['profile_id']);

headHtml('Edit Profile - ' . STUDENT_NAME);
flashMessages();
?>

<h1>Edit Profile</h1>
<form method="POST">
    <input type="hidden" name="profile_id" value="<?php echo htmlentities($profile['profile_id'], ENT_QUOTES, 'UTF-8'); ?>" />
    <p>First Name:<br />
    <input type="text" name="first_name" size="60" value="<?php echo htmlentities($profile['first_name'], ENT_QUOTES, 'UTF-8'); ?>" /></p>
    <p>Last Name:<br />
    <input type="text" name="last_name" size="60" value="<?php echo htmlentities($profile['last_name'], ENT_QUOTES, 'UTF-8'); ?>" /></p>
    <p>Email:<br />
    <input type="text" name="email" size="60" value="<?php echo htmlentities($profile['email'], ENT_QUOTES, 'UTF-8'); ?>" /></p>
    <p>Headline:<br />
    <input type="text" name="headline" size="80" value="<?php echo htmlentities($profile['headline'], ENT_QUOTES, 'UTF-8'); ?>" /></p>
    <p>Summary:<br />
    <textarea name="summary" rows="8" cols="80"><?php echo htmlentities($profile['summary'], ENT_QUOTES, 'UTF-8'); ?></textarea></p>

    <p>Position: <input type="button" id="addPos" value="+" class="btn btn-success" /></p>
    <div id="position_fields">
    <?php
    $posCount = 0;
    foreach ($positions as $pos) {
        $posCount++;
        echo positionRowHtml($posCount, $pos['year'], $pos['description']);
    }
    ?>
    </div>

    <p>Education: <input type="button" id="addEdu" value="+" class="btn btn-success" /></p>
    <div id="education_fields">
    <?php
    $eduCount = 0;
    foreach ($education as $edu) {
        $eduCount++;
        echo educationRowHtml($eduCount, $edu['year'], $edu['school']);
    }
    ?>
    </div>

    <p>
        <input type="submit" class="btn btn-primary" value="Save" />
        <input type="submit" class="btn btn-default" value="Cancel" onclick="location.href='index.php'; return false;" />
    </p>
</form>

<script>
function addPosition() {
    var countPos = parseInt($('#pos_count').val()) || 0;
    countPos++;
    if (countPos > 9) {
        alert('Maximum of nine position entries exceeded');
        return;
    }
    $('#pos_count').val(countPos);
    var source = $('#position_template').html().replace(/@COUNT@/g, countPos);
    $('#position_fields').append(source);
}

function addEducation() {
    var countEdu = parseInt($('#edu_count').val()) || 0;
    countEdu++;
    if (countEdu > 9) {
        alert('Maximum of nine education entries exceeded');
        return;
    }
    $('#edu_count').val(countEdu);
    var source = $('#education_template').html().replace(/@COUNT@/g, countEdu);
    $('#education_fields').append(source);
    $('#education' + countEdu + ' .school').autocomplete({ source: 'school.php' });
}

$(document).ready(function(){
    $('#pos_count').val(<?php echo $posCount; ?>);
    $('#edu_count').val(<?php echo $eduCount; ?>);
    $('#addPos').click(addPosition);
    $('#addEdu').click(addEducation);
    $('.school').autocomplete({ source: 'school.php' });
});
</script>

<input type="hidden" id="pos_count" value="<?php echo $posCount; ?>" />
<input type="hidden" id="edu_count" value="<?php echo $eduCount; ?>" />

<div style="display:none;">
    <div id="position_template">
        <div id="position@COUNT@" class="position-entry">
            <p>Year: <input type="text" name="year@COUNT@" value="" size="10" />
            <input type="button" value="-" onclick="$('#position@COUNT@').remove(); return false;" /></p>
            <textarea name="desc@COUNT@" rows="8" cols="80"></textarea>
        </div>
    </div>
    <div id="education_template">
        <div id="education@COUNT@" class="education-entry">
            <p>Year: <input type="text" name="edu_year@COUNT@" value="" size="10" />
            <input type="button" value="-" onclick="$('#education@COUNT@').remove(); return false;" /></p>
            <p>School: <input type="text" size="80" name="edu_school@COUNT@" class="school" value="" /></p>
        </div>
    </div>
</div>

<?php
footHtml();
