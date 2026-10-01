<?php
require_once __DIR__ . '/includes/functions.php';
$page = 'games';
$games = read_games();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Game Listings - Video Game Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php require __DIR__ . '/includes/navigation.php'; ?>
<main class="container my-4">
    <h1 class="h3">Game Listings</h1>
    <p class="text-secondary">Browse the games in our store.</p>

    <?php if (isset($_GET['saved']) && $_GET['saved'] === '1'): ?>
        <div class="alert alert-success" role="status">Game added successfully.</div>
    <?php endif; ?>

    <?php if (!$games): ?>
        <div class="alert alert-info">No games have been added yet. Add a game to get started.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <caption class="visually-hidden">All saved games and their prices.</caption>
                <thead class="table-dark">
                    <tr>
                        <th scope="col">Image</th>
                        <th scope="col">Inventory Code</th>
                        <th scope="col">Video Game Name</th>
                        <th scope="col">Console</th>
                        <th scope="col">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($games as $game): ?>
                        <tr>
                            <td>
                                <img class="game-image rounded" src="uploads/<?= escape(rawurlencode(basename($game[4]))) ?>"
                                     alt="<?= escape($game[1]) ?> cover" width="75" height="100">
                            </td>
                            <td><?= escape($game[0]) ?></td>
                            <td><?= escape($game[1]) ?></td>
                            <td><?= escape($game[2]) ?></td>
                            <td class="text-nowrap">$<?= escape(number_format((float) $game[3], 2)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <a href="index.php" class="btn btn-primary">Add Another Game</a>
</main>
</body>
</html>
