<?php
require_once 'util.php';

headHtml('Resume Database - ' . STUDENT_NAME);
flashMessages();

if (isset($_SESSION['name'])) {
    echo '<p>Welcome ' . htmlentities($_SESSION['name']) . '</p>' . "\n";
}
?>

<h1>Profiles</h1>
<?php if (isset($_SESSION['user_id'])): ?>
    <p><a href="add.php" class="btn btn-primary">Add New Profile</a></p>
<?php endif; ?>

<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Headline</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $stmt = $pdo->query('SELECT profile_id, first_name, last_name, headline, user_id FROM Profile');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<tr>' . "\n";
        echo '<td><a href="view.php?profile_id=' . htmlentities($row['profile_id']) . '">' .
             htmlentities($row['first_name']) . ' ' . htmlentities($row['last_name']) .
             '</a></td>' . "\n";
        echo '<td>' . htmlentities($row['headline']) . '</td>' . "\n";
        echo '<td>';
        if (isset($_SESSION['user_id'])) {
            echo '<a href="view.php?profile_id=' . htmlentities($row['profile_id']) . '">View</a> ';
            echo '<a href="edit.php?profile_id=' . htmlentities($row['profile_id']) . '">Edit</a> ';
            echo '<a href="delete.php?profile_id=' . htmlentities($row['profile_id']) . '">Delete</a>';
        } else {
            echo '<a href="view.php?profile_id=' . htmlentities($row['profile_id']) . '">View</a>';
        }
        echo '</td>' . "\n";
        echo '</tr>' . "\n";
    }
    ?>
    </tbody>
</table>

<?php if (isset($_SESSION['user_id'])): ?>
    <p><a href="logout.php" class="btn btn-default">Logout</a></p>
<?php else: ?>
    <p><a href="login.php" class="btn btn-default">Please log in</a></p>
<?php endif; ?>

<?php
footHtml();
