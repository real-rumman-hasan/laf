<?php
require "db_connect.php";
require "nav_bar.php";

if (isset($_SESSION['user_id']) && $role != 'admin') {
    header("Location: restricted.php");
}

function get_found_item_match_count($conn, $found_item_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS occurrences FROM matches WHERE found_item_id = ? AND status = 'open'");

    $stmt->execute([$found_item_id]);

    $count = $stmt->fetch(PDO::FETCH_ASSOC);

    return $count['occurrences'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $match_id = intval($_POST['match_id']);
    $lost_item_id = intval($_POST['lost_item_id']);
    $found_item_id = intval($_POST['found_item_id']);
    $count = intval(get_found_item_match_count($conn, $found_item_id));

    if (isset($_POST['delete']) && $_POST['delete'] === 'yes') {    
        $stmt = $conn->prepare("UPDATE items SET status = 'open' WHERE id = ?");
        $stmt->execute([$lost_item_id]);

        if ($count <= 1) {
            $stmt = $conn->prepare("UPDATE items SET status = 'open' WHERE id = ?");
            $stmt->execute([$found_item_id]);
        } 

        $stmt = $conn->prepare("DELETE FROM matches WHERE id = ?");
        $stmt->execute([$match_id]);

    }
    
    elseif (isset($_POST['confirm']) && $_POST['confirm'] === 'yes') {
        $stmt = $conn->prepare("UPDATE matches SET status = 'closed' WHERE id = ?");
        $stmt->execute([$match_id]);
        
        $stmt = $conn->prepare("UPDATE items SET status = 'closed' WHERE id = ?");
        $stmt->execute([$lost_item_id]);
        
        $stmt = $conn->prepare("UPDATE items SET status = 'closed' WHERE id = ?");
        $stmt->execute([$found_item_id]);

        if ($count > 1) {
            $stmt = $conn->prepare("UPDATE items SET status = 'open' WHERE id IN (SELECT lost_item_id FROM matches WHERE found_item_id = ? AND status = 'open')");
            $stmt->execute([$found_item_id]);

            $stmt = $conn->prepare("UPDATE matches SET status = 'closed' WHERE found_item_id = ? AND status = 'open'");
            $stmt->execute([$found_item_id]);
        }
    }
    
    header('Location: manage_matches.php');
}

function get_all_matches($conn) {
    $stmt = $conn->prepare("SELECT * FROM matches");
    $stmt->execute();

    $matches = $stmt->fetchALL(PDO::FETCH_ASSOC);

    return $matches;
}

function get_one_item($conn, $item_id) {
    $stmt = $conn->prepare("SELECT title, description FROM items WHERE id = ?");
    $stmt->execute([$item_id]);

    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    return $item;
}

$matches = get_all_matches($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage All Matches</title>
</head>
<body>
    <main class="container">
        <h1 class="fs-1 fw-light mt-3 text-center" id="title">
            Manage All Matches
        </h1>
        <hr>

        <div class="py-3"></div>

        <?php foreach($matches as $match): ?>
            <div class="container mt-3 p-2 border border-2 rounded rounded-3 shadow">
                <div class="py-1">
                        ID: <span class="badge bg-primary"><?php echo htmlspecialchars($match['id']); ?></span>
                </div>
                <div class="py-1">
                        Status: <span class="fw-bold <?php echo  htmlspecialchars($match['status'] === 'open' ? 'text-danger' : 'text-success'); ?>">
                            <?php echo htmlspecialchars($match['status']); ?>
                        </span>
                </div>

                <div class="row">
                    <div class="col col-6">
                        <span class="badge bg-danger text-center fs-6 d-block px-auto">Lost</span>

                        <?php $lost_item = get_one_item($conn, $match['lost_item_id']); ?>

                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($lost_item['title']); ?>
                                </h5>
                                <p class="card-text">
                                    <?php echo htmlspecialchars($lost_item['description']); ?>
                                </p>
                            </div>
    
                            <a 
                            href="item.php?id=<?php echo htmlspecialchars($match['lost_item_id']); ?>" class="btn btn-primary my-2 mx-auto"
                            >
                                View
                            </a>
                        </div>
                    </div>
                    <div class="col col-6">
                        <span class="badge bg-success text-center fs-6 d-block px-auto">Found</span>

                        <?php $found_item = get_one_item($conn, $match['found_item_id']); ?>

                        <div class="card shadow">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <?php echo htmlspecialchars($found_item['title']); ?>
                                </h5>
                                <p class="card-text">
                                    <?php echo htmlspecialchars($found_item['description']); ?>
                                </p>
                            </div>
    
                            <a 
                            href="item.php?id=<?php echo htmlspecialchars($match['found_item_id']); ?>" class="btn btn-primary my-2 mx-auto"
                            >
                                View
                            </a>
                        </div>
                    </div>
                </div>
    
                <div class="text-center mt-5">
                    <!-- delete match button -->
                    <form method='POST' class="d-inline" onsubmit="return confirm('Are you sure you wish to delete this match?');">
                        <input type="text" name="delete" value="yes" hidden>
                        <input type="number" name="match_id" value="<?php echo htmlspecialchars($match['id']); ?>" hidden>
                        <input type="number" name="lost_item_id" value="<?php echo htmlspecialchars($match['lost_item_id']); ?>" hidden>
                        <input type="number" name="found_item_id" value="<?php echo htmlspecialchars($match['found_item_id']); ?>" hidden>

                        <button type="submit" class="btn btn-outline-danger d-inline mx-auto my-2">
                            Delete Match
                        </button>
                    </form>
    
                    <!-- confirm match -->
                    <form method='POST' class="d-inline" onsubmit="return confirm('Are you sure you wish to confirm this match?');">
                        <input type="text" name="confirm" value="yes" hidden>
                        <input type="number" name="match_id" value="<?php echo htmlspecialchars($match['id']); ?>" hidden>
                        <input type="number" name="lost_item_id" value="<?php echo htmlspecialchars($match['lost_item_id']); ?>" hidden>
                        <input type="number" name="found_item_id" value="<?php echo htmlspecialchars($match['found_item_id']); ?>" hidden>

                        <button type="submit" class="btn btn-outline-success d-inline mx-auto my-2">
                            Confirm Match
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </main>

    <?php
    require "footer.php";
    require "bootstrap_bottom.php";
    ?>
</body>
</html>