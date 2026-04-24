<?php
require "db_connect.php";
require "bootstrap_top.php";

$reg_succ = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users(name, email, password) VALUES(?, ?, ?)");
    $stmt->execute([$user, $email, $pass]);

    $reg_succ = true;
}
?>

<style>
    #form-register {
        max-width: 30vw;
    }
</style>

<main class="container">
    <div class="my-5"></div>

    <div class="text-center">
        <a class="border border-5 border-primary px-3 rounded-3 fw-bolder bg-primary text-light fs-1 text-decoration-none" href="index.php">
            LaF
        </a>
        <p class="fs-3 mt-5 fw-bold">Register</p>
    </div>

    <div class="my-5"></div>

    <form id="form-register" method="POST" class="mx-auto">
      <div class="mb-3">
        <label for="inputName" class="form-label">Your Name</label>
        <input type="text" class="form-control" id="inputName" name="name">
      </div>
      <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Email address</label>
        <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" name="email">
      </div>
      <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Password</label>
        <input type="password" class="form-control" id="exampleInputPassword1" name=""password>
      </div>

      <div class="text-center">  
          <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </form>

    <div class="my-5"></div>
    
    <?php if($reg_succ): ?>
        Registration Succesful! Now you can 
        <a 
        href="login.php"
        class="btn btn-secondary mx-1 fw-bold"
        >
            Login
        </a>
    <?php endif; ?>
</main>

<?php
require "footer.php";
require "bootstrap_bottom.php";
?>