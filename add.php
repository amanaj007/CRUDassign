<?php
require_once 'util.php';
requireLogin();

if (isset($_POST['first_name']) && isset($_POST['last_name']) &&
    isset($_POST['email']) && isset($_POST['headline']) &&
    isset($_POST['summary'])) {

    $msg = validateProfile();
    if ($msg !== true) {
        $_SESSION['error'] = $msg;
        header('Location: add.php');
        return;
    }

    $msg = validatePositions();
    if ($msg !== true) {
        $_SESSION['error'] = $msg;
        header('Location: add.php');
        return;
    }

    $msg = validateEducation();
    if ($msg !== true) {
        $_SESSION['error'] = $msg;
        header('Location: add.php');
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO Profile ' .
        '(user_id, first_name, last_name, email, headline, summary) ' .
        'VALUES (:uid, :fn, :ln, :em, :he, :su)'
    );
    $stmt->execute(
        array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary']
        )
    );
    $profile_id = $pdo->lastInsertId();

    insertPositions($pdo, $profile_id);
    insertEducation($pdo, $profile_id);

    $_SESSION['success'] = 'Profile added';
    header('Location: index.php');
    return;
}

headHtml('Add Profile - ' . STUDENT_NAME);
flashMessages();
?>

<h1>Add Profile</h1>
<form method="POST">
    <p>First Name:<br />
    <input type="text" name="first_name" size="60" value="" /></p>
    <p>Last Name:<br />
    <input type="text" name="last_name" size="60" value="" /></p>
    <p>Email:<br />
    <input type="text" name="email" size="60" value="" /></p>
    <p>Headline:<br />
    <input type="text" name="headline" size="80" value="" /></p>
    <p>Summary:<br />
    <textarea name="summary" rows="8" cols="80"></textarea></p>

    <p>Position: <input type="button" id="addPos" value="+" class="btn btn-success" /></p>
    <div id="position_fields"></div>

    <p>Education: <input type="button" id="addEdu" value="+" class="btn btn-success" /></p>
    <div id="education_fields"></div>

    <p>
        <input type="submit" class="btn btn-primary" value="Add" />
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
    $('#pos_count').val(0);
    $('#edu_count').val(0);
    $('#addPos').click(addPosition);
    $('#addEdu').click(addEducation);
    $('.school').autocomplete({ source: 'school.php' });
});
</script>

<input type="hidden" id="pos_count" value="0" />
<input type="hidden" id="edu_count" value="0" />

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
