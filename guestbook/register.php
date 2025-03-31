<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions
session_start();
$config = require_once 'config.php';
$db = mysqli_connect(
    $config['host'],
    $config['user'],
    $config['pass'],
    $config['name']
);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// TODO 2: ROUTING
if (!empty($_SESSION['auth'])) {
    header('Location: /admin.php');
    die;
}

// TODO 3: CODE by REQUEST METHODS (ACTIONS) GET, POST, etc. (handle data from request): 1) validate 2) working with data source 3) transforming data

// 1. Create empty $infoMessage
$infoMessage = '';

// 2. handle form data
if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 3. Check that user has already existed
    $stmt = mysqli_prepare($db, "SELECT id FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $infoMessage = "Такий користувач вже існує! <a href='/login.php'>Спробуйте увійти</a>";
    } else {

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $insertStmt = mysqli_prepare($db, "INSERT INTO users (email, password, created_at) VALUES (?, ?, NOW())");
        mysqli_stmt_bind_param($insertStmt, "ss", $email, $hashed);

        if (mysqli_stmt_execute($insertStmt)) {
            header('Location: /login.php');
            die;
        } else {
            $infoMessage = "Помилка при створенні користувача!";
        }

        mysqli_stmt_close($insertStmt);
    }

    mysqli_stmt_close($stmt);
} elseif (!empty($_POST)) {
    $infoMessage = 'Заповніть всі поля!';
}

// TODO 4: RENDER: 1) view (html) 2) data (from php)

?>


<!DOCTYPE html>
<html>

<?php require_once 'sectionHead.php' ?>

<body>

<div class="container">

    <?php require_once 'sectionNavbar.php' ?>

    <br>

    <div class="card card-primary">
        <div class="card-header bg-success text-light">
            Register form
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="email" name="email"/>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input class="form-control" type="password" name="password"/>
                </div>
                <br>
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" name="formRegister"/>
                </div>
            </form>

            <!-- TODO: render php data   -->
            <?php
                if ($infoMessage) {
                    echo '<hr/>';
                    echo "<span style='color:red'>$infoMessage</span>";
                }
            ?>

        </div>

    </div>
</div>

</body>
</html>