<?php
session_start();

if(isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'];
}
?>

<h1>You Cannot Visit This Page</h1>
<?php if(!isset($_SESSION['user_id'])): ?>
    <h3>To Access This Page Please 
        <a href="login.php">Login</a>
        .
    </h3>

<?php elseif($_SESSION['role'] === 'user'): ?>
    <h3>This is an Admin Page. Please go Back to  
        <a href="index.php">Home</a>
        Page.
    </h3>

<?php else: ?>
    <h3>To Access This Page Please login as a user. Go back to  
        <a href="index.php">Home</a>
    Page
    </h3>
<?php endif; ?>