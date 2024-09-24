<!DOCTYPE html>
<html>
<head>
    <title>ユーザー認証 FORM</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <header>
        <h1>ユーザー認証</h1>
        <nav>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="my-page.php">My Page</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>
</body>
</html>
