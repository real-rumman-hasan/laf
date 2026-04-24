<?php
session_start();
require "db_connect.php";
require "nav_bar.php";

if (!$role || $role === 'admin') {
    header("Location: restricted.php");
    exit;
}

function get_lost_items($conn) {
    $stmt = $conn->prepare("SELECT * FROM items WHERE user_id=? AND type='lost'");
    $stmt->execute([$_SESSION['user_id']]);
    $data = $stmt->fetchall(PDO::FETCH_ASSOC);

    return $data;
}

function get_found_items($conn) {
    $stmt = $conn->prepare("SELECT * FROM items WHERE user_id=? AND type='found'");
    $stmt->execute([$_SESSION['user_id']]);
    $data = $stmt->fetchall(PDO::FETCH_ASSOC);

    return $data;
}

$lost_items = get_lost_items($conn);
$found_items = get_found_items($conn);

// delete item
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);

    header("Location: my_items.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View all you lost and found item posts</title>
</head>
<body>
    <main class="container">
        
        <h1 class="fs-1 fw-light text-center my-4">All Posts You Have Made</h1>
        
        <hr>
        <h2 class="fs-4 fw-light my-4">Posts for Lost Items</h2>

        <hr>

        <!-- to show lost item posts -->
        <?php foreach($lost_items as $item): ?>
            <div class="card text-center shadow">
                <div class="card-body">

                    <h3 class="card-title fs-4 fw-bold">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </h3>

                    <p class="card-text">
                        <?php echo htmlspecialchars($item['description']); ?>
                    </p>

                    <!-- to go to item.php page for a particular item -->
                    <a 
                    href="item.php?id=<?php echo htmlspecialchars($item['id']); ?>" 
                    class="btn btn-primary"
                    >
                    View
                    </a>


                    <!-- to go to edit_item.php page for a particular item -->
                    <a 
                    href="edit_item.php?id=<?php echo htmlspecialchars($item['id']); ?>" 
                    class="btn btn-success"
                    >
                    Edit
                    </a>

                    <!-- to delete item must use form -->
                    <form method="POST" onsubmit="return confirm('Delete this item?');" class="d-inline">
                        <input type="hidden" name="delete_id" value="<?php echo $item['id']; ?>">
                        <button class="btn btn-danger" type="submit">Delete</button>
                    </form>
                </div>
            </div>
            <div class="mb-3"></div>
        <?php endforeach; ?>
        <div class="my-5"></div>

        <hr>

        <h2 class="fs-4 fw-light my-4">Posts for Found Items</h2>
            
        <hr>

        <!-- to show found item posts -->
        <?php foreach($found_items as $item): ?>
            <div class="card text-center shadow">
                <div class="card-body">

                    <h3 class="card-title fs-4 fw-bold">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </h3>

                    <p class="card-text">
                        <?php echo htmlspecialchars($item['description']); ?>
                    </p>

                    <!-- to go to item.php page for a particular item -->
                    <a 
                    href="item.php?id=<?php echo htmlspecialchars($item['id']); ?>" 
                    class="btn btn-primary"
                    >
                    View
                    </a>


                    <!-- to go to edit_item.php page for a particular item -->
                    <a 
                    href="edit_item.php?id=<?php echo htmlspecialchars($item['id']); ?>" 
                    class="btn btn-success"
                    >
                    Edit
                    </a>
                    
                    <!-- to delete item must use form -->
                    <form method="POST" onsubmit="return confirm('Delete this item?');" class="d-inline">
                        <input type="hidden" name="delete_id" value="<?php echo $item['id']; ?>">
                        <button class="btn btn-danger" type="submit">Delete</button>
                    </form>
                </div>
            </div>
            <div class="mb-3"></div>
        <?php endforeach; ?>
        <div class="my-5"></div>
    </main>

    <?php
    require "footer.php";
    require "bootstrap_bottom.php";
    ?>
</body>
</html>