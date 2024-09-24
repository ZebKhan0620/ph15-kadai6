<?php
session_start();

require_once __DIR__ . '/functions/user.php';

// Check if user is logged in
if (!isset($_SESSION['id']) && !isset($_COOKIE['id'])) {
    header('Location: ./login.php');
    exit();
}

// Retrieve user ID from session or cookie
$id = $_SESSION['id'] ?? $_COOKIE['id'];

$user = getUser($id);

// If user is not found, redirect to login
if (is_null($user)) {
    header('Location: ./login.php');
    exit();
}

// Initialize errors array for password change
$errors = [];

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マイページ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h2>マイページ</h2>

        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="my-page.php">
            <table>
                <tr>
                    <td>ID:</td>
                    <td><?php echo ($user['id']); ?></td>
                </tr>
                <tr>
                    <td>名前:</td>
                    <td><?php echo ($user['name']); ?></td>
                </tr>
                <tr>
                    <td>メールアドレス:</td>
                    <td><?php echo ($user['email']); ?></td>
                </tr>

                <tr>
                    <td>生年月日:</td>
                    <td>
                    <?php echo ($user['dob']); ?>
                    </td>
                </tr>

                <tr>
                    <td>電話番号:</td>
                    <td>
                    <?php echo ($user['phone']); ?>
                    </td>
                </tr>

                <tr>
                    <td>性別:</td>
                    <td>
                    <?php echo ($user['gender']) ?>
                    </td>
                </tr>

                <tr>
                    <td>住所:</td>
                    <td><?php echo ($user['address']); ?></td>
                </tr>
            </table>
        </form>

        <div class="success-container">
        <a href="./edit.php" class="success-button1">情報変更</a>
    </div>

        <div class="logout-container">
            <a href="./logout.php" class="logout-button">ログアウト</a>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
