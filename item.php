<?php
// session_start();
require "db_connect.php";
require "nav_bar.php";

if (!$role) {
    header("Location: restricted.php");
    exit;
}

// retrieve from item.php?id=3
// we can also send two superglobals: item.php?id=3&type=found
$item_id = null;
$match_item_id = null;

if (isset($_GET['id'])) {
    $item_id = intval($_GET['id']);
} else {
    die("Invalid request");
}

if (isset($_GET['match_id'])) {
    $match_item_id = intval($_GET['match_id']);
}

function get_one_item($conn, $item_id) {
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->execute([$item_id]);

    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    return $item;
}

function get_user($conn, $user_id) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user;
}

if (isset($_GET['match_id'])) {
    $item = get_one_item($conn, $match_item_id);
}
else {
    $item = get_one_item($conn, $item_id);

}

$item_user = get_user($conn, $item['user_id']);

// get suggested matches

function get_matching_items($conn, $base_item) {
    $type = ($base_item['type'] === 'lost' ? 'found' : 'lost');

    $stmt = $conn->prepare("SELECT * FROM items WHERE type = ? AND category = ? AND user_id != ? AND status != 'closed'");
    $stmt->execute([$type, $base_item['category'], $base_item['user_id']]);

    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $items;
}

$suggested_items = get_matching_items($conn, $item);

// Create a match record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("INSERT INTO matches(lost_item_id, found_item_id) VALUES(?, ?)");

    $stmt->execute([$item_id, $match_item_id]);
    
    $stmt = $conn->prepare("
    UPDATE items 
    SET status = 'matched'
    WHERE id = ? OR id = ?
    ");

    $stmt->execute([$item_id, $match_item_id]);

    header("Location: my_matches.php");

}

// only works in item.php?id=3&match_id=4
function is_already_matched($conn, $base_item_id, $match_item_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS occurrences FROM matches WHERE lost_item_id = ?  AND found_item_id = ?");

    $stmt->execute([$base_item_id, $match_item_id]);

    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    return $count['occurrences'];
}

function get_found_item_match_count($conn, $found_item_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS occurrences FROM matches WHERE found_item_id = ?");

    $stmt->execute([$found_item_id]);

    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    return $count['occurrences'];
}


$count = null;
$f_count = null;

if (isset($_GET['match_id'])) {
    $count = is_already_matched($conn, $item_id, $match_item_id);
    $f_count = get_found_item_match_count($conn, $match_item_id);
}
else {
   $f_count = get_found_item_match_count($conn, $item_id); 
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Information</title>
</head>
<body>
    <main class="container">
        <div class="m-4"></div>

        <h1 class="fs-1" id="title">
            <?php echo htmlspecialchars($item['title']); ?>
        </h1>
        
        <hr>
        <p id="description">
            <?php echo htmlspecialchars($item['description']); ?>
        </p>
        <hr>

        <p class="fw-bold" id="name">Poster: <span class="fw-normal">
            <?php echo htmlspecialchars($item_user['name']); ?>
        </span></p>

        <p class="fw-bold" id="email">Email: <span class="fw-normal">
            <?php echo htmlspecialchars($item_user['email']); ?>
        </span></p>

        <p class="fw-bold" id="date">Post Created On: <span class="fw-normal">
            <?php echo htmlspecialchars($item['date']); ?>
        </span></p>

        <p class="fw-bold" id="category">Category: <span class="fw-normal">
            <?php echo htmlspecialchars($item['category']); ?>
        </span></p>

        <p class="fw-bold" id="type">Item Type: <span 
        class="badge 
        <?php echo ($item['type'] === 'found' ? 'bg-success' : 'bg-danger') ?>"
        >
            <?php echo ucfirst(htmlspecialchars($item['type'])); ?>
        </span></p>

        <p class="fw-bold" id="location">Item Location: <span class="fw-normal">
            <?php echo htmlspecialchars($item['location']); ?>
        </span></p>

        <p class="fw-bold" id="status">Item Status: <span 
        class="badge 
        <?php echo ($item['status'] === 'open' ? 'bg-danger' : ($item['status'] === 'closed' ? 'bg-success' : 'bg-warning text-dark')); ?>"
        >
            <?php echo ucfirst(htmlspecialchars($item['status'])); ?>
            <?php if($f_count > 0) echo "($f_count)"; ?>
        </span></p>
        <div class="m-5"></div>
        
        <!-- Gettin suggested items -->

        <?php if($_SESSION['user_id'] === $item_user['id'] && $item['type'] == 'lost' && count($suggested_items) > 0): ?>
            <hr>
            <h2 class="fs-2 fw-light">Suggested Matches</h2>
            <hr>
            <?php foreach($suggested_items as $sub_item): ?>
                <div class="card text-center shadow">
                    <div class="card-body">
    
                        <h3 class="card-title fs-4 fw-bold">
                            <?php echo htmlspecialchars($sub_item['title']); ?>
                        </h3>
    
                        <p class="card-text">
                            <?php echo htmlspecialchars($sub_item['description']); ?>
                        </p>
    
                        <!-- to go to item.php page for a particular sub_item -->
                        <a 
                        href="
                        item.php?id=<?php echo $item_id; ?>&match_id=<?php echo $sub_item['id']; ?>" 
                        class="btn btn-primary"
                        >
                        View
                        </a>
                        
                    </div>
                </div>
                <div class="mb-3"></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if(isset($_GET['match_id']) && $count === 0): ?>
            <form method="POST" onsubmit="return confirm('Are you sure this is your item?');">
                <button type="submit" class="btn btn-success btn-lg fw-bold d-block mx-auto">This is My Item</button>
            </form>
        <?php endif; ?>
    </main>

    <?php
    require "footer.php";
    require "bootstrap_bottom.php";
    ?>
</body>
</html>