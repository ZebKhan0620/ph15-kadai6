<?php

require_once __DIR__ . '/functions/user.php';

session_start();

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

// Handle form submission for updating user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $dobYear = $_POST['dob-year'];
    $dobMonth = $_POST['dob-month'];
    $dobDay = $_POST['dob-day'];
    $countryCode = $_POST['country-code'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];

    // Handle password change
    $newPassword = $_POST['new-password'];
    $confirmPassword = $_POST['confirm-password'];

    // Combine date of birth into YYYY-MM-DD format
    $dob = sprintf('%04d-%02d-%02d', $dobYear, $dobMonth, $dobDay);

    // Combine country code and phone number
    $fullPhoneNumber = $countryCode . ' ' . $phone;

    // Password validation
    if (!empty($newPassword) || !empty($confirmPassword)) {
        if (strlen($newPassword) < 8) {
            $errors[] = 'パスワードは8文字以上で入力してください';
        }
        if ($newPassword !== $confirmPassword) {
            $errors[] = 'パスワードが一致しません';
        }
    }

    // Validate form fields
    if (empty($name)) {
        $errors[] = 'お名前を入力してください';
    }
    if (empty($email)) {
        $errors[] = 'メールアドレスを入力してください';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '正しいメールアドレスを入力してください';
    }
    // Validate the selected DOB
    if (empty($dobYear) || empty($dobMonth) || empty($dobDay)) {
        $errors[] = '生年月日を選択してください';
    } else {
        // Combine the year, month, and day into a date string (e.g., YYYY-MM-DD)
        $dob = sprintf('%04d-%02d-%02d', $dobYear, $dobMonth, $dobDay);
    }
    // Validate country code and phone number
    if (empty($countryCode)) {
        $errors[] = '国コードを選択してください';
    }
    if (empty($phone)) {
        $errors[] = '電話番号を入力してください';
    }

    // Combine country code and phone number for storage
    $fullPhoneNumber = $countryCode . ' ' . $phone;


    // Validate gender
    if (empty($gender)) {
        $errors[] = '性別を選択してください';
    }

    if (empty($address)) {
        $errors[] = '住所を入力してください';
    }

    // If no errors, update user data in CSV
    if (empty($errors)) {
        // Only update password if a new one was entered
        $passwordHash = !empty($newPassword) ? password_hash($newPassword, PASSWORD_BCRYPT) : $user['password'];

        updateUser($id, [
            'name' => $name,
            'email' => $email,
            'dob' => $dob,
            'phone' => $fullPhoneNumber,
            'gender' => $gender,
            'address' => $address,
            'password' => $passwordHash
        ]);

        // Redirect to the success page after updating
        header('Location: update-success.php');
        exit();
    }
}

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
        <h2>情報変更</h2>

        <?php if (!empty($errors)): ?>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="POST" action="./edit.php">
            <table>
                <tr>
                    <td>ID:</td>
                    <td><?php echo ($user['id']); ?></td>
                </tr>
                <tr>
                    <td>名前:</td>
                    <td><input type="text" name="name" value="<?php echo ($user['name']); ?>"></td>
                </tr>
                <tr>
                    <td>メールアドレス:</td>
                    <td><input type="email" name="email" value="<?php echo ($user['email']); ?>"></td>
                </tr>

                <!-- Select dropdown for Date of Birth -->
                <tr>
                    <td>生年月日:</td>
                    <td>
                        <!-- Year -->
                        <select name="dob-year" id="dob-year">
                            <option value="">年</option>
                            <?php
                            $dobParts = explode('-', $user['dob']);
                            $dobYear = $dobParts[0];
                            $dobMonth = $dobParts[1];
                            $dobDay = $dobParts[2];
                            $currentYear = date('Y');
                            for ($year = $currentYear; $year >= 1980; $year--) {
                                $selected = ($dobYear == $year) ? 'selected' : '';
                                echo "<option value='$year' $selected>$year</option>";
                            }
                            ?>
                        </select>

                        <!-- Month -->
                        <select name="dob-month" id="dob-month">
                            <option value="">月</option>
                            <?php
                            for ($month = 1; $month <= 12; $month++) {
                                $selected = ($dobMonth == $month) ? 'selected' : '';
                                echo "<option value='$month' $selected>$month</option>";
                            }
                            ?>
                        </select>

                        <!-- Day -->
                        <select name="dob-day" id="dob-day">
                            <option value="">日</option>
                            <?php
                            for ($day = 1; $day <= 31; $day++) {
                                $selected = ($dobDay == $day) ? 'selected' : '';
                                echo "<option value='$day' $selected>$day</option>";
                            }
                            ?>
                        </select>
                    </td>
                </tr>

                <!-- Select dropdown for Country Code and Phone Number -->
                <tr>
                    <td>電話番号:</td>
                    <td>
                        <!-- Country Code -->
                        <select name="country-code" id="country-code">
                            <option value="">国コード</option>
                            <?php
                            // Split phone into country code and number
                            $phoneParts = explode(' ', $user['phone']);
                            $countryCode = $phoneParts[0];
                            $phoneNumber = $phoneParts[1];

                            // Country codes options
                            $countryCodes = [
                                '+1' => '🇺🇸 +1 (USA)',
                                '+81' => '🇯🇵 +81 (Japan)',
                                '+44' => '🇬🇧 +44 (UK)',
                                '+61' => '🇦🇺 +61 (Australia)'
                            ];

                            foreach ($countryCodes as $code => $label) {
                                $selected = ($countryCode == $code) ? 'selected' : '';
                                echo "<option value='$code' $selected>$label</option>";
                            }
                            ?>
                        </select>

                        <!-- Phone Number -->
                        <input type="tel" name="phone" value="<?php echo ($phoneNumber); ?>">
                    </td>
                </tr>

                <tr>
                    <td>性別:</td>
                    <td>
                        <label><input type="radio" name="gender" value="男性" <?php echo ($user['gender'] == '男性') ? 'checked' : ''; ?>>男性</label>
                        <label><input type="radio" name="gender" value="女性" <?php echo ($user['gender'] == '女性') ? 'checked' : ''; ?>>女性</label>
                        <label><input type="radio" name="gender" value="その他" <?php echo ($user['gender'] == 'その他') ? 'checked' : ''; ?>>その他</label>
                    </td>
                </tr>

                <tr>
                    <td>住所:</td>
                    <td><textarea name="address"><?php echo ($user['address']); ?></textarea></td>
                </tr>

                <!-- Password Change Section -->
                <tr>
                    <td>新しいパスワード:</td>
                    <td><input type="password" name="new-password"></td>
                </tr>
                <tr>
                    <td>パスワード確認:</td>
                    <td><input type="password" name="confirm-password"></td>
                </tr>
            </table>

            <input type="submit" value="更新">
        </form>

        <div class="logout-container">
            <a href="./logout.php" class="logout-button">ログアウト</a>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
