<?php
require_once 'util.php';

headHtml('Resume Database - ' . STUDENT_NAME);
flashMessages();
?>

<h1>Welcome to the Database</h1>

<?php if (isset($_SESSION['user_id'])): ?>
    <p><a href="add.php" class="btn btn-primary">Add New Entry</a></p>
<?php else: ?>
    <p><a href="login.php" class="btn btn-primary">Please log in</a></p>
<?php endif; ?>

<?php if (isset($_SESSION['user_id'])): ?>
    <?php
    $stmt = $pdo->query('SELECT profile_id, first_name, last_name, headline, user_id FROM Profile');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) == 0) {
        echo '<p>No rows found</p>' . "\n";
    } else {
    ?>
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
        foreach ($rows as $row) {
            echo '<tr>' . "\n";
            echo '<td><a href="view.php?profile_id=' . htmlentities($row['profile_id']) . '">' .
                 htmlentities($row['first_name']) . ' ' . htmlentities($row['last_name']) .
                 '</a></td>' . "\n";
            echo '<td>' . htmlentities($row['headline']) . '</td>' . "\n";
            echo '<td>';
            echo '<a href="view.php?profile_id=' . htmlentities($row['profile_id']) . '">View</a> ';
            echo '<a href="edit.php?profile_id=' . htmlentities($row['profile_id']) . '">Edit</a> ';
            echo '<a href="delete.php?profile_id=' . htmlentities($row['profile_id']) . '">Delete</a>';
            echo '</td>' . "\n";
            echo '</tr>' . "\n";
        }
        ?>
        </tbody>
    </table>
    <?php } ?>
    <p><a href="logout.php" class="btn btn-default">Logout</a></p>
<?php endif; ?>

<?php
footHtml();
