<?php
// ini_set('display_errors', 1); 
// ini_set('display_startup_errors', 1); 
// error_reporting(E_ALL);
require "db_connect.php";
require "nav_bar.php";
// require "test.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and found System</title>
    <style>
        #hero {
            min-height: 90vh;
            min-width: 100vw;
            background: #020024;
background: linear-gradient(271deg, rgba(2, 0, 36, 1) 0%, rgba(9, 121, 119, 1) 100%);
        }

        #image {
            min-height: 90vh;
            background-image: url(images/background-hero.jpg);
            background-size: cover;
            background-position: -134px 13px;
        }

        #greeting {
            min-height: 90vh;
        }
    </style>
</head>
<body>
    <main>
        <div id="hero" class="container shadow-lg p-3 rounded">
            <div class="row">
                <div id="greeting" class="col fw-bold text-light fs-1 d-flex align-items-center justify-content-center text-center">
                    Welcome to Lost and Found System
                </div>
                <div id="image" class="col rounded-circle shadow-lg">
                
                </div>
            </div>
        </div>
    </main>

    <?php
        require "footer.php";
        require "bootstrap_bottom.php";
    ?>
</body>
</html>
