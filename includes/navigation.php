<nav class="navbar navbar-dark bg-dark" aria-label="Main navigation">
    <div class="container">
        <a class="navbar-brand" href="games.php">Video Game Store</a>
        <div class="navbar-nav flex-row gap-3">
            <a class="nav-link <?= $page === 'add' ? 'active' : '' ?>" href="index.php" <?= $page === 'add' ? 'aria-current="page"' : '' ?>>Add Game</a>
            <a class="nav-link <?= $page === 'games' ? 'active' : '' ?>" href="games.php" <?= $page === 'games' ? 'aria-current="page"' : '' ?>>View Games</a>
        </div>
    </div>
</nav>
