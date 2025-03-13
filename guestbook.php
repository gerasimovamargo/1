<?php
// TODO 1: PREPARING ENVIRONMENT: 1) session 2) functions
session_start();

function saveComment($name, $email, $text) {
    $comment = [
        'name' => htmlspecialchars($name),
        'email' => htmlspecialchars($email),
        'text' => htmlspecialchars($text),
        'time' => date('Y-m-d H:i:s')
    ];
    file_put_contents("comments.csv", json_encode($comment) . "\n", FILE_APPEND);
}

function renderComments($perPage = 5) {
    if (!file_exists("comments.csv") || !is_readable("comments.csv")) {
        echo "<p>No comments yet.</p>";
        return;
    }

    $file = fopen("comments.csv", "r");
    $comments = [];

    while (($line = fgets($file)) !== false) {
        $comment = json_decode($line, true);
        if ($comment) {
            $comments[] = $comment;
        }
    }
    fclose($file);

    $totalComments = count($comments);
    $totalPages = ceil($totalComments / $perPage);
    $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $currentPage = max(1, min($currentPage, $totalPages));
    $start = ($currentPage - 1) * $perPage;
    $pageComments = array_slice($comments, $start, $perPage);

    $comments = array_reverse($comments);
    foreach ($pageComments as $comment) {
        echo "<div class='card my-2 p-3'>";
        echo "<strong>" . htmlspecialchars($comment['name']) . "</strong> (" . htmlspecialchars($comment['email']) . ")<br>";
        echo "<small>" . htmlspecialchars($comment['time']) . "</small>";
        echo "<p>" . nl2br(htmlspecialchars($comment['text'])) . "</p>";
        echo "</div>";
    }

    echo "<nav><ul class='pagination'>";
    for ($i = 1; $i <= $totalPages; $i++) {
        echo "<li class='page-item " . ($i == $currentPage ? "active" : "") . "'>
                <a class='page-link' href='?page=$i'>$i</a>
              </li>";
    }
    echo "</ul></nav>";
}

// TODO 2: ROUTING

// TODO 3: CODE by REQUEST METHODS (ACTIONS) GET, POST, etc.
function handlePostRequest() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $text = trim($_POST['text']);

        $errors = validateForm($name, $email, $text);

        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        } else {
            saveComment($name, $email, $text);
            header("Location: guestbook.php");
            exit();
        }
    }
}

function validateForm($name, $email, $text) {
    $errors = [];

    if (empty($name) || strlen($name) < 2) {
        $errors[] = "Name must have at least 2 characters.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($text) || strlen($text) > 500) {
        $errors[] = "Comment must be between 1 and 500 characters.";
    }

    return $errors;
}

handlePostRequest();

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
        <div class="card-header bg-primary text-light">
            GuestBook form
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Your Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="text" class="form-label">Your Comment</label>
                            <textarea class="form-control" id="text" name="text" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Comment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <br>

    <div class="card card-primary">
        <div class="card-header bg-body-secondary text-dark">
            Comments
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-6">
                    <?php renderComments(5); ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
