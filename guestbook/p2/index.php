<?php
if (isset($_GET['search'])) {
    $search = urlencode($_GET['search']);


    $apiKey = "AIzaSyBaePtsK5mlpKsQL66tiA5Hv90GuoJVxSc";
    $cx = "748b378cfaef9465d";
    $url = "https://www.googleapis.com/customsearch/v1?key=$apiKey&cx=$cx&q=$search";


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $results = json_decode($response, true);
    $items = $results['items'] ?? [];

}

if (isset($_GET['url'])) {
    $url = $_GET['url'];
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location: $url");
        exit();
    } else {
        echo "<p style='color: red;'>Invalid URL</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Browser</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            width: 300px;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-right: 10px;
        }

        input[type="submit"] {
            padding: 10px 15px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        h2 {
            color: #333;
        }

        ul {
            list-style-type: none;
            padding: 0;
            max-width: 600px;
        }
        li {
            background: #fff;
            margin: 10px 0;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<h2>My Browser</h2>

<form method="GET" action="">
    <label for="search">Search:</label>
    <input type="text" id="search" name="search" value=""><br><br>
    <input type="submit" value="Submit">
</form>

<form method="GET" action="">
    <label for="url">Enter URL:</label>
    <input type="text" id="url" name="url" placeholder="Enter a valid URL"><br><br>
    <input type="submit" value="Go to URL">
</form>

<?php if (!empty($items)): ?>
    <h3>Results:</h3>
    <ul>
        <?php foreach ($items as $item): ?>
            <li>
                <a href="<?= htmlspecialchars($item['link']) ?>" target="_blank">
                    <?= htmlspecialchars($item['title']) ?>
                </a>
                <p><?= htmlspecialchars($item['snippet'] ?? '') ?></p>

                <?php if (isset($item['pagemap']['cse_image'][0]['src'])): ?>
                    <img src="<?= htmlspecialchars($item['pagemap']['cse_image'][0]['src']) ?>" alt="Image" style="width:100px; height:auto;">
                <?php endif; ?>

                <p>Site: <?= htmlspecialchars($item['displayLink'] ?? 'No data available') ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>No results found.</p>
<?php endif; ?>

</body>
</html>
