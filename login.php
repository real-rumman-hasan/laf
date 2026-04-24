<?php
session_start();

require "db_connect.php";
require "bootstrap_top.php";

$log_succ = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$email]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data && password_verify($pass, $data['password'])) {
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['user_name'] = $data['name'];
        $_SESSION['role'] = $data['role'];

        $log_succ = true;

        header("Location: index.php");
    } else {
        $log_succ = false;
    }
}
?>

<style>
    #form-login {
        max-width: 30vw;
    }
</style>

<main class="container">
    <div class="my-5"></div>
    
    <div class="text-center">
        <a class="border border-5 border-primary px-3 rounded-3 fw-bolder bg-primary text-light fs-1 text-decoration-none" href="index.php">
            LaF
        </a>
        <p class="fs-3 mt-5 fw-bold">Login</p>
    </div>

    <div class="my-5"></div>

    <form id="form-login" method="POST" class="mx-auto">
      <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Email address</label>
        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="email">
      </div>
      <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Password</label>
        <input type="password" class="form-control" id="exampleInputPassword1" name=""password>
      </div>

      <div class="text-center">  
          <button type="submit" class="btn btn-primary">Login</button>
      </div>
    </form>

    <div class="my-5"></div>

    <?php if($log_succ === false): ?>
        <div class="text-danger">Invalid login. Please try again</div>
    <?php endif; ?>
</main>

<?php
require "footer.php";
require "bootstrap_bottom.php";
?>