<?php

require_once __DIR__ . '/functions/user.php';

session_start();

$errorMessages = [];

$email = '';

if (isset($_POST['submit-button'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    // remember the login information which will return Boolean(true/false) value
    $isRememberMe = isset($_POST['remember-me']);

    // Validate email
    if (empty($email)) {
        $errorMessages['email'] = 'メールアドレスを入力してください';
    }

    // Validate password and enforce minimum length of 8 characters
    if (empty($password) || strlen($password) < 8) {
        $errorMessages['password'] = 'パスワードは8文字以上で入力してください';
    }

    // If no errors, proceed with login validation
    if (empty($errorMessages)) {
        $user = login($email, $password);

        if (!is_null($user)) {
            $_SESSION['id'] = $user['id'];

            // Remember me functionality using cookies
            if ($isRememberMe) {
                setcookie('id', $user['id'], time() + 180, '/');
            }

            // Redirect to my-page.php
            header('Location: ./my-page.php');
            exit();
        }

        // Error message for invalid login credentials
        $errorMessages['result'] = '一致するユーザーが見つかりませんでした';
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>ログイン</h1>

        <?php if (isset($errorMessages['result'])): ?>
            <p class="error" style="color: red;"><?php echo $errorMessages['result']; ?></p>
        <?php endif; ?>

        <form action="./login.php" method="post">
            <div>
                <label for="email">メールアドレス</label>
                <input type="email" name="email" id="email" value="<?php echo ($email); ?>" required>
                <?php if (isset($errorMessages['email'])): ?>
                    <p class="error"><?php echo $errorMessages['email']; ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="password">パスワード</label>
                <input type="password" name="password" id="password">
                <?php if (isset($errorMessages['password'])): ?>
                    <p class="error"><?php echo $errorMessages['password']; ?></p>
                <?php endif; ?>
            </div>

            <div class="remember-me-container">
                <input type="checkbox" name="remember-me" id="remember-me">
                <label for="remember-me">ログイン状態を保存する</label>
            </div>

            <div>
                <input type="submit" value="ログイン" name="submit-button">
            </div>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
