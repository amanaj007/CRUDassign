<?php
session_start();
require_once 'pdo.php';

function headHtml($title)
{
    echo '<!DOCTYPE html>' . "\n";
    echo '<html>' . "\n";
    echo '<head>' . "\n";
    echo '<title>' . htmlentities($title) . '</title>' . "\n";
    echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"';
    echo ' integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">' . "\n";
    echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"';
    echo ' integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">' . "\n";
    echo '<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">' . "\n";
    echo '<script src="https://code.jquery.com/jquery-3.2.1.js"';
    echo ' integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>' . "\n";
    echo '<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"';
    echo ' integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>' . "\n";
    echo '</head>' . "\n";
    echo '<body>' . "\n";
    echo '<div class="container">' . "\n";
}

function footHtml()
{
    echo '</div>' . "\n";
    echo '</body>' . "\n";
    echo '</html>' . "\n";
}

function flashMessages()
{
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red;">' . htmlentities($_SESSION['error']) . "</p>\n";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo '<p style="color:green;">' . htmlentities($_SESSION['success']) . "</p>\n";
        unset($_SESSION['success']);
    }
}

function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        die('ACCESS DENIED');
    }
}

function validateProfile()
{
    if (
        strlen($_POST['first_name']) < 1 ||
        strlen($_POST['last_name']) < 1 ||
        strlen($_POST['email']) < 1 ||
        strlen($_POST['headline']) < 1 ||
        strlen($_POST['summary']) < 1
    ) {
        return 'All fields are required';
    }
    if (strpos($_POST['email'], '@') === false) {
        return 'Email must have an at-sign (@)';
    }
    return true;
}

function validatePositions()
{
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) {
            continue;
        }
        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];
        if (strlen($year) == 0 && strlen($desc) == 0) {
            continue;
        }
        if (strlen($year) == 0 || strlen($desc) == 0) {
            return 'All fields are required';
        }
        if (!is_numeric($year)) {
            return 'Year must be numeric';
        }
    }
    return true;
}

function validateEducation()
{
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['edu_year' . $i])) {
            continue;
        }
        $year = $_POST['edu_year' . $i];
        $school = $_POST['edu_school' . $i];
        if (strlen($year) == 0 && strlen($school) == 0) {
            continue;
        }
        if (strlen($year) == 0 || strlen($school) == 0) {
            return 'All fields are required';
        }
        if (!is_numeric($year)) {
            return 'Year must be numeric';
        }
    }
    return true;
}

function loadProfile($pdo, $profile_id)
{
    $stmt = $pdo->prepare('SELECT * FROM Profile WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profile_id));
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function loadPositions($pdo, $profile_id)
{
    $stmt = $pdo->prepare('SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank');
    $stmt->execute(array(':pid' => $profile_id));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function loadEducation($pdo, $profile_id)
{
    $stmt = $pdo->prepare(
        'SELECT E.rank, E.year, I.name AS school ' .
        'FROM Education E JOIN Institution I ON E.institution_id = I.institution_id ' .
        'WHERE E.profile_id = :pid ORDER BY E.rank'
    );
    $stmt->execute(array(':pid' => $profile_id));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deletePositions($pdo, $profile_id)
{
    $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profile_id));
}

function deleteEducation($pdo, $profile_id)
{
    $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id = :pid');
    $stmt->execute(array(':pid' => $profile_id));
}

function insertPositions($pdo, $profile_id)
{
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['year' . $i])) {
            continue;
        }
        $year = $_POST['year' . $i];
        $desc = $_POST['desc' . $i];
        if (strlen($year) == 0 && strlen($desc) == 0) {
            continue;
        }
        $stmt = $pdo->prepare(
            'INSERT INTO Position (profile_id, rank, year, description) ' .
            'VALUES (:pid, :rank, :year, :desc)'
        );
        $stmt->execute(
            array(
                ':pid' => $profile_id,
                ':rank' => $rank,
                ':year' => $year,
                ':desc' => $desc
            )
        );
        $rank++;
    }
}

function insertEducation($pdo, $profile_id)
{
    $rank = 1;
    for ($i = 1; $i <= 9; $i++) {
        if (!isset($_POST['edu_year' . $i])) {
            continue;
        }
        $year = $_POST['edu_year' . $i];
        $school = $_POST['edu_school' . $i];
        if (strlen($year) == 0 && strlen($school) == 0) {
            continue;
        }

        $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :name');
        $stmt->execute(array(':name' => $school));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row === false) {
            $stmt = $pdo->prepare('INSERT INTO Institution (name) VALUES (:name)');
            $stmt->execute(array(':name' => $school));
            $institution_id = $pdo->lastInsertId();
        } else {
            $institution_id = $row['institution_id'];
        }

        $stmt = $pdo->prepare(
            'INSERT INTO Education (profile_id, institution_id, rank, year) ' .
            'VALUES (:pid, :iid, :rank, :year)'
        );
        $stmt->execute(
            array(
                ':pid' => $profile_id,
                ':iid' => $institution_id,
                ':rank' => $rank,
                ':year' => $year
            )
        );
        $rank++;
    }
}

function positionRowHtml($i, $year = '', $desc = '')
{
    return
        '<div id="position' . $i . '" class="position-entry">' .
        '<p>Year: <input type="text" name="year' . $i . '" value="' . htmlentities($year, ENT_QUOTES, 'UTF-8') . '" size="10" /> ' .
        '<input type="button" value="-" onclick="$(\'#position' . $i . '\').remove(); return false;" /></p>' .
        '<textarea name="desc' . $i . '" rows="8" cols="80">' . htmlentities($desc, ENT_QUOTES, 'UTF-8') . '</textarea>' .
        '</div>';
}

function educationRowHtml($i, $year = '', $school = '')
{
    return
        '<div id="education' . $i . '" class="education-entry">' .
        '<p>Year: <input type="text" name="edu_year' . $i . '" value="' . htmlentities($year, ENT_QUOTES, 'UTF-8') . '" size="10" /> ' .
        '<input type="button" value="-" onclick="$(\'#education' . $i . '\').remove(); return false;" /></p>' .
        '<p>School: <input type="text" size="80" name="edu_school' . $i . '" class="school" value="' . htmlentities($school, ENT_QUOTES, 'UTF-8') . '" /></p>' .
        '</div>';
}
