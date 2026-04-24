<?php
// session_start();
require "db_connect.php";
require "nav_bar.php";

$categories = ["Electronics", "Documents", "Personal Accessories"];
$item_category = [
    $categories[0] => [ "Smartphone", "Laptop", "Earphones", "Power Bank", "USB Flash Drive"],
    
    $categories[1] => [ "Student ID Card", "National ID Card", "Passport", "Driving License", "Admit Card"],
    
    $categories[2] => [ "Wallet", "Wristwatch", "Backpack", "Keys", "Glasses"]
];

$dhaka_areas = [
    "Dhanmondi",
    "Gulshan",
    "Banani",
    "Uttara",
    "Mirpur",
    "Mohammadpur",
    "Farmgate",
    "Tejgaon",
    "Motijheel",
    "Paltan",
    "Ramna",
    "Shahbagh",
    "New Market",
    "Lalbagh",
    "Azimpur",
    "Badda",
    "Rampura",
    "Khilgaon",
    "Malibagh",
    "Moghbazar",
    "Wari",
    "Jatrabari",
    "Demra",
    "Keraniganj",
    "Savar",
    "Tongi",
    "Airport",
    "Cantonment",
    "Basundhara",
    "Baridhara"
];

$data = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $category = $_GET['category'];
    $type = $_GET['type'];
    $location = $_GET['location'];

    $sql = "SELECT id, title, description, type FROM items";
    
    if ($category === 'all' && $type === 'all' && $location != 'all') {
        $sql = $sql . " WHERE location = '$location'";
    }
    if ($category != 'all' && $type === 'all' && $location === 'all') {
        $sql = $sql . " WHERE category = '$category'";
    }
    if ($category === 'all' && $type != 'all' && $location === 'all') {
        $sql = $sql . " WHERE type = '$type'";
    }
    if ($category === 'all' && $type != 'all' && $location != 'all') {
        $sql = $sql . " WHERE location = '$location' AND type = '$type'";
    }
    if ($category != 'all' && $type === 'all' && $location != 'all') {
        $sql = $sql . " WHERE category = '$category' AND location = '$location'";
    }
    if ($category != 'all' && $type != 'all' && $location === 'all') {
        $sql = $sql . " WHERE category = '$category' AND type = '$type'";
    }
    if ($category != 'all' && $type != 'all' && $location != 'all') {
        $sql = $sql . " WHERE category = '$category' AND location = '$location' AND type = '$type'";
    }

    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        if ($category === 'all' && $type === 'all' && $location === 'all') {
            $sql = $sql . " WHERE user_id != $user_id";
        }
        else {
            $sql = $sql . " AND user_id != $user_id";
        }
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

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
    <title>Search For Any Item</title>
</head>
<body>
    <main class="container">
        <h1 class="fs-1 fw-light mt-3 text-center" id="title">
            Search For Any Items Using Filters
        </h1>

        <hr>
        <form method="GET" class="mt-4 mb-3">
            <div class="row">
                <div class="col">

                    <select class="form-select mb-3" aria-label="Select an item category" name="category">
                        <option selected hidden value="all">Choose Category</option>
        
                        <?php for($i = 0; $i < 3; $i++): ?>
                            <optgroup label=<?php echo htmlspecialchars($categories[$i]); ?>>
        
                            <?php foreach($item_category[$categories[$i]] as $item): ?>
                                <option value="<?php echo htmlspecialchars($item); ?>">
                                    <?php echo htmlspecialchars($item); ?>
                                </option>
                            <?php endforeach; ?>
        
                            </optgroup>
                        <?php endfor; ?>   
                    </select>
                </div>
                <div class="col">

                    <select class="form-select mb-3" aria-label="Select an item type" name="type">
                        <option selected hidden value="all">Choose Type</option>
                        <option value="lost">Lost</option>
                        <option value="found">Found</option>
                    </select>   
                </div>
                <div class="col">
                    
                    <select class="form-select mb-3" aria-label="Select a location" name="location">
                        <option selected hidden value="all">Choose Location</option>
                        
                        <?php foreach($dhaka_areas as $area): ?>
                            <option value="<?php echo htmlspecialchars($area); ?>">
                                <?php echo htmlspecialchars($area); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col col-1">

                    <button type="submit" class="btn btn-success fw-bold">Search</button>
                </div>
            </div>

            <hr>
            
            <?php foreach($data as $item): ?>
                <div class="card text-center shadow">
                    <div class="card-body">

                        <h3 class="card-title fs-4 fw-bold">
                            <?php echo htmlspecialchars($item['title']); ?> 
                            <span 
                            class="badge rounded-pill 
                            <?php echo ($item['type'] === 'found' ? 'bg-success' : 'bg-danger') ?>"
                            >
                                <?php echo ucfirst(htmlspecialchars($item['type'])); ?>
                            </span>
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

                        <?php if(isset($_SESSION['user_id']) && $role === 'admin'): ?>

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
                        <?php endif; ?>
                    </div>
                </div>
                <div class="mb-3"></div>
            <?php endforeach; ?>
        </form>
    </main>

    <?php
    require "footer.php";
    require "bootstrap_bottom.php";
    ?>
</body>
</html>