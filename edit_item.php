<?php
// session_start();
require "db_connect.php";
require "nav_bar.php";

if (!$role || $role === 'admin') {
    header("Location: restricted.php");
    exit;
}

// retrieve from item.php?id=3
// we can also send two superglobals: item.php?id=3&type=found
$item_id = null;

if (isset($_GET['id'])) {
    $item_id = intval($_GET['id']);
} else {
    die("Invalid request");
}

// retrieve item from database
function get_one_item($conn, $item_id) {
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->execute([$item_id]);

    $item = $stmt->fetch(PDO::FETCH_ASSOC);

    return $item;
}

$item = get_one_item($conn, $item_id);

// save changes

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $type = $_POST['type'];
    $location = $_POST['location'];

    $stmt = $conn->prepare("
    UPDATE items 
    SET title = ?, description = ?, category = ?, type = ?, location = ?
    WHERE id = ?
");

    $stmt->execute([$title, $description, $category, $type, $location, $item_id]);

    header("Location: my_items.php");
}

//data
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item Information</title>
</head>
<body>
    <main class="container">
        <h1 class="fs-1 fw-light text-center my-4">Edit Item Information</h1>

        <form method="POST" onsubmit="return confirm('Are you sure you want to save these changes?');">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control form-control-lg" id="title" placeholder="Add a title..." name="title" value="<?php echo htmlspecialchars($item['title']); ?>">
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" rows="3" placeholder="Add a description..." name="description"><?php echo htmlspecialchars($item['description']); ?>
                </textarea>
            </div>

            <select class="form-select mb-3" aria-label="Select an item category" name="category">
                <?php for($i = 0; $i < 3; $i++): ?>
                    <optgroup label=<?php echo htmlspecialchars($category[$i]); ?>>

                    <?php foreach($item_category[$category[$i]] as $sub_item): ?>
                        <option 
                        value="<?php echo htmlspecialchars($sub_item); ?>"
                        <?php if($item['category'] === $sub_item) echo 'selected'; ?>
                        >
                            <?php echo htmlspecialchars($sub_item); ?>
                        </option>
                    <?php endforeach; ?>

                    </optgroup>
                <?php endfor; ?>   
            </select>
            
            <?php $type = strtolower(trim($item['type'])); ?>
            <select class="form-select mb-3" aria-label="Select an item type" name="type">
                <option value="lost" <?php if($type === 'lost') echo 'selected'; ?>>
                    Lost
                </option>

                <option value="found" <?php if($type === 'found') echo 'selected'; ?>>
                    Found
                </option>
            </select>   

            <select class="form-select mb-3" aria-label="Select a location" name="location">                
                <?php foreach($dhaka_areas as $area): ?>
                    <option 
                    value="<?php echo htmlspecialchars($area); ?>" 
                    <?php if($item['location'] === $area) echo 'selected'; ?>
                    >
                        <?php echo htmlspecialchars($area); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-primary d-block mx-auto">
                Save Changes
            </button>
        </form>
    </main>

    <?php
    require "footer.php";
    require "bootstrap_bottom.php";
    ?>
</body>
</html>