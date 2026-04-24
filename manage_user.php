<?php
require "db_connect.php";
require "nav_bar.php";

if (isset($_SESSION['user_id']) && $role != 'admin') {
    header("Location: restricted.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $user_id = $_GET['id'] ?? null;

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
}

function get_all_users($conn) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE role != 'admin'");
    $stmt->execute();

    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $users;
}

function get_user_lost_item_count($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS num FROM items WHERE user_id = ? AND type = 'lost'");
    $stmt->execute([$user_id]);

    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    return $count['num'];
}

function get_user_found_item_count($conn, $user_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS num FROM items WHERE user_id = ? AND type = 'found'");
    $stmt->execute([$user_id]);

    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    return $count['num'];
}

$users = get_all_users($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mange All Users</title>
</head>
<body>
    <main class="container">
        <h1 class="fs-1 fw-light mt-3 text-center" id="title">
            Manage All Users
        </h1>
        <hr>

        <div class="py-3"></div>
        <div class="d-flex flex-wrap justify-content-center">

            <?php foreach($users as $user): ?>
                <div class="card shadow m-2 border-2" style="width: 18rem;">
                    <div class="card-body position-relative ">
                        <span class="badge bg-primary position-absolute top-0 start-0 translate-middle fs-6">
                            <?php echo htmlspecialchars($user['id']); ?>
                        </span>
                        <h5 class="card-title">
                            <?php echo htmlspecialchars($user['name']); ?>
                        </h5>
                        <p class="card-text text-decoration-underline">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </p>

                        <?php $count = get_user_lost_item_count($conn, $user['id']); ?>
                        <p class="card-text">Lost Items Reported: <span class="text-danger fw-bold">
                            <?php echo htmlspecialchars($count); ?>
                        </span></p>

                        <?php $count = get_user_found_item_count($conn, $user['id']); ?>
                        <p class="card-text">Found Items Reported: <span class="text-success fw-bold">
                            <?php echo htmlspecialchars($count); ?>
                        </span></p>

                        <form method='GET' onsubmit="return confirm('Do you want to delete this user?')">
                            <input type="text" name="id" value="<?php echo htmlspecialchars($user['id']); ?>" hidden>
                            <button type="submit" class="btn btn-outline-danger fw-bold d-block mx-auto px-5">Delete User</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
            
        </div>
    </main>

    <?php
    require "footer.php";
    require "bootstrap_bottom.php";
    ?>
</body>
</html>