<?php
session_start();

require "bootstrap_top.php";

$role = $_SESSION['role'] ?? null;

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function is_user() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

// $_SESSION['user_name'] = "Rumman";
// $_SESSION['user_id'] = 11;
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light border border-3">
  <div class="container-fluid">
    <a class="navbar-brand border border-5 border-primary px-3 rounded-3 fw-bolder bg-primary text-light" href="index.php">
        LaF
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Home</a>
        </li>

        <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] === 'user'): ?>
            <li class="nav-item">
              <a class="nav-link" href="add_item.php">Make Post</a>
            </li>
    
            <li class="nav-item">
              <a class="nav-link" href="my_items.php">View Posts</a>
            </li>
    
            <li class="nav-item">
              <a class="nav-link" href="my_matches.php">View Matches</a>
            </li>
        <?php endif; ?>

        <?php if(isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin'): ?>
            <li class="nav-item">
            <a class="nav-link" href="manage_user.php">Manage Users</a>
            </li>

            <li class="nav-item">
            <a class="nav-link" href="manage_matches.php">Manage Matches</a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link" href="search.php">
                <?php if($_SESSION['role'] === 'admin'): ?>
                    Manage Items
                <?php else: ?>
                    Search Items
                <?php endif; ?>
            </a>
        </li>
      </ul>

        <!-- php code to hide login and register buttons on login -->
        <?php if(isset($_SESSION['user_id'])): ?>
            <div class="btn btn-primary mx-1 fw-bold">
                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
            </div>
            <a 
            href="logout.php"
            class="btn btn-secondary mx-1 fw-bold"
            >
                Logout
            </a>
        <?php else: ?>
            <a 
            href="login.php"
            class="btn btn-secondary mx-1 fw-bold"
            >
                Login
            </a>
            <a 
            href="register.php"
            class="btn btn-primary mx-1 fw-bold"
            >
                Register
            </a>
        <?php endif; ?>

    </div>
  </div>
</nav>