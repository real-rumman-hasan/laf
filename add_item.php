<?php
// session_start();
require "db_connect.php";
require "nav_bar.php";

// echo $role;

if (!$role || $role === 'admin') {
    header("Location: restricted.php");
    exit;
}

$category = ["Electronics", "Documents", "Personal Accessories"];
$item_category = [
    $category[0] => [ "Smartphone", "Laptop", "Earphones", "Power Bank", "USB Flash Drive"],
    
    $category[1] => [ "Student ID Card", "National ID Card", "Passport", "Driving License", "Admit Card"],
    
    $category[2] => [ "Wallet", "Wristwatch", "Backpack", "Keys", "Glasses"]
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

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $type = $_POST['type'];
    $location = $_POST['location'];

    // echo "$title $description $category $type $location";

    $stmt = $conn->prepare("INSERT INTO items(title, description, category, type, location, date, status, user_id) VALUES(?, ?, ?, ?, ?, ?, 'open', ?)");

    $current_date = date('Y-m-d H:i:s');

    $stmt->execute([$title, $description, $category, $type, $location, $current_date, $_SESSION['user_id']]);

    header("Location: my_items.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a Post about an Item</title>
</head>
<body>
    
    <main class="container">
        <h1 class="fs-1 fw-light text-center my-4">Make a Post About a Lost or Found Item</h1>

        <form method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control form-control-lg" id="title" placeholder="Add a title..." name="title">
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" rows="3" placeholder="Add a description..." name="description"></textarea>
            </div>

            <select class="form-select mb-3" aria-label="Select an item category" name="category">
                <option selected hidden>Select an Item Category...</option>

                <?php for($i = 0; $i < 3; $i++): ?>
                    <optgroup label=<?php echo htmlspecialchars($category[$i]); ?>>

                    <?php foreach($item_category[$category[$i]] as $item): ?>
                        <option value="<?php echo htmlspecialchars($item); ?>">
                            <?php echo htmlspecialchars($item); ?>
                        </option>
                    <?php endforeach; ?>

                    </optgroup>
                <?php endfor; ?>   
            </select>

            <select class="form-select mb-3" aria-label="Select an item type" name="type">
                <option selected hidden>Select item type...</option>
                <option value="lost">Lost</option>
                <option value="found">Found</option>
            </select>   

            <select class="form-select mb-3" aria-label="Select a location" name="location">
                <option selected hidden>Select a location...</option>
                
                <?php foreach($dhaka_areas as $area): ?>
                    <option value="<?php echo htmlspecialchars($area); ?>">
                        <?php echo htmlspecialchars($area); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-primary d-block mx-auto">Make Post</button>
        </form>
    </main>

    <?php
    require "footer.php";
    require "bootstrap_bottom.php";
    ?>
</body>
</html>