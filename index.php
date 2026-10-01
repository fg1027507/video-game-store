<?php
require_once __DIR__ . '/includes/functions.php';
$page = 'add';

// These variables hold the form values and any error messages.
$code = '';
$name = '';
$console = '';
$price = '';
$errors = [];
$consoles = ['PC', 'Nintendo Switch', 'PlayStation 5', 'Xbox Series X'];

// Only process the form after the user submits it.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $console = trim($_POST['console'] ?? '');
    $price = trim($_POST['price'] ?? '');

    // Check each field before saving anything.
    if (!preg_match('/^GAME-[0-9]{4}$/', $code)) {
        $errors['code'] = 'Use GAME- followed by exactly four digits, such as GAME-1234.';
    }
    if ($name === '') {
        $errors['name'] = 'Enter the video game name.';
    }
    if (!in_array($console, $consoles)) {
        $errors['console'] = 'Choose one of the available consoles.';
    }
    if (!is_numeric($price) || $price < 0.01 || !is_finite((float) $price)) {
        $errors['price'] = 'Enter a price of at least $0.01.';
    }

    // PHP puts uploaded file information in the $_FILES array.
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors['image'] = 'Choose an image that uploads successfully.';
    } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        $errors['image'] = 'Choose an image under 2 MB.';
    } else {
        $temporaryImage = $_FILES['image']['tmp_name'];

        // Check the file contents, rather than trusting its name.
        $imageType = mime_content_type($temporaryImage);
        $imageTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (!isset($imageTypes[$imageType]) || @getimagesize($temporaryImage) === false) {
            $errors['image'] = 'Upload a valid JPG, PNG, or WebP image.';
        } else {
            $extension = $imageTypes[$imageType];
        }
    }

    // Both folders are included in the project and must be writable.
    $gamesFolder = __DIR__ . '/games';
    $uploadsFolder = __DIR__ . '/uploads';
    if (empty($errors) && (!is_writable($gamesFolder) || !is_writable($uploadsFolder))) {
        $errors['save'] = 'Check the write permissions for the games and uploads folders.';
    }

    if (empty($errors)) {
        // Use milliseconds for the image filename, as the assignment requires.
        $milliseconds = (int) (microtime(true) * 1000);
        $filename = $milliseconds . '.' . $extension;

        // If that filename already exists, try the next number.
        while (file_exists($uploadsFolder . '/' . $filename)) {
            $milliseconds++;
            $filename = $milliseconds . '.' . $extension;
        }

        $destination = $uploadsFolder . '/' . $filename;
        if (!move_uploaded_file($temporaryImage, $destination)) {
            $errors['save'] = 'The image could not be saved. Please try again.';
        } else {
            // Append one row without replacing the earlier games.
            $file = @fopen($gamesFolder . '/games.csv', 'a');
            if ($file === false) {
                unlink($destination);
                $errors['save'] = 'The CSV file could not be opened.';
            } else {
                $row = [$code, $name, $console, number_format((float) $price, 2, '.', ''), $filename];
                $saved = fputcsv($file, $row, ',', '"', '');
                fclose($file);

                if ($saved === false) {
                    unlink($destination);
                    $errors['save'] = 'The game could not be saved.';
                } else {
                    // Redirect so refreshing the listings will not add the game again.
                    header('Location: games.php?saved=1', true, 303);
                    exit;
                }
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add a Game - Video Game Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require __DIR__ . '/includes/navigation.php'; ?>
<main class="container my-4">
    <div class="card form-card mx-auto">
        <div class="card-body p-4">
            <h1 class="h3">Add a Game</h1>
            <p class="text-secondary">Enter the game details below. All fields are required.</p>

            <?php if ($errors): ?>
                <div class="alert alert-danger" role="alert">
                    Your game hasn't been saved. Correct the errors below and select your image again.
                    <?php if (isset($errors['save'])): ?>
                        <p class="mb-0"><?= escape($errors['save']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form action="index.php" method="post" enctype="multipart/form-data" novalidate>
                <div class="mb-3">
                    <label for="code" class="form-label">Inventory Code</label>
                    <input type="text" id="code" name="code" class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>"
                           value="<?= escape($code) ?>" placeholder="GAME-1234" pattern="GAME-[0-9]{4}" required <?= isset($errors['code']) ? 'aria-invalid="true"' : '' ?> aria-describedby="code-help code-error">
                    <div id="code-help" class="form-text">Use GAME- followed by four digits, such as GAME-1234.</div>
                    <div id="code-error" class="invalid-feedback"><?= escape($errors['code'] ?? '') ?></div>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Video Game Name</label>
                    <input type="text" id="name" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= escape($name) ?>" placeholder="Game name" required <?= isset($errors['name']) ? 'aria-invalid="true"' : '' ?> aria-describedby="name-error">
                    <div id="name-error" class="invalid-feedback"><?= escape($errors['name'] ?? '') ?></div>
                </div>

                <div class="mb-3">
                    <label for="console" class="form-label">Console</label>
                    <select id="console" name="console" class="form-select <?= isset($errors['console']) ? 'is-invalid' : '' ?>" required <?= isset($errors['console']) ? 'aria-invalid="true"' : '' ?> aria-describedby="console-error">
                        <option value="">Select a console</option>
                        <?php foreach ($consoles as $choice): ?>
                            <option value="<?= escape($choice) ?>" <?= $console === $choice ? 'selected' : '' ?>><?= escape($choice) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div id="console-error" class="invalid-feedback"><?= escape($errors['console'] ?? '') ?></div>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price (USD)</label>
                    <input type="number" id="price" name="price" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>"
                           value="<?= escape($price) ?>" placeholder="29.99" min="0.01" step="0.01" required <?= isset($errors['price']) ? 'aria-invalid="true"' : '' ?> aria-describedby="price-error">
                    <div id="price-error" class="invalid-feedback"><?= escape($errors['price'] ?? '') ?></div>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Game Image</label>
                    <input type="file" id="image" name="image" class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>"
                           accept="image/jpeg,image/png,image/webp" required <?= isset($errors['image']) ? 'aria-invalid="true"' : '' ?> aria-describedby="image-help image-error">
                    <div id="image-help" class="form-text">Choose a JPG, PNG, or WebP image under 2 MB.</div>
                    <div id="image-error" class="invalid-feedback"><?= escape($errors['image'] ?? '') ?></div>
                </div>

                <button type="submit" class="btn btn-primary">Add Game</button>
                <a href="games.php" class="btn btn-outline-secondary ms-2">View Games</a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
