<?php

// Include user functions
require_once __DIR__ . '/functions/user.php';

session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $dobYear = $_POST['dob-year'];
    $dobMonth = $_POST['dob-month'];
    $dobDay = $_POST['dob-day'];
    $countryCode = $_POST['country-code'];
    $phone = $_POST['phone'];
    $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
    $address = $_POST['address'];

    // Validate form fields
    if (empty($name)) {
        $errors[] = 'お名前を入力してください';
    }
    if (empty($email)) {
        $errors[] = 'メールアドレスを入力してください';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '正しいメールアドレスを入力してください';
    }
    if (empty($password)) {
        $errors[] = 'パスワードを入力してください';
    } elseif (strlen($password) < 8) {
        $errors[] = 'パスワードは8文字以上で入力してください';
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

    // Check for duplicate email
    $users = getUsers();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $errors[] = 'このメールアドレスは既に登録されています';
            break;
        }
    }

    // If no errors, save the user data
    if (empty($errors)) {
        $newUser = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'dob' => $dob,
            'phone' => $fullPhoneNumber,
            'gender' => $gender,
            'address' => $address,
        ];

        // Save user
        $user = saveUser($newUser);

        // Save user ID to session
        $_SESSION['id'] = $user['id'];

        // Redirect to my-page.php
        header('Location: ./my-page.php');
        exit();
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>会員登録</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <?php include 'header.php'; ?>

        <div class="container">
            <h2>会員登録</h2>

            <?php if (!empty($errors)): ?>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div>
                    <label for="name">お名前:</label>
                    <input type="text" name="name" id="name" value="<?php echo ($name ?? ''); ?>">
                </div>

                <div>
                    <label for="email">メールアドレス:</label>
                    <input type="email" name="email" id="email" value="<?php echo ($email ?? ''); ?>">
                </div>

                <div>
                    <label for="password">パスワード:</label>
                    <input type="password" name="password" id="password">
                </div>

                <div>
                    <label for="dob-year">生年月日:</label>

                    <!-- Year -->
                    <select name="dob-year" id="dob-year">
                        <option value="">年</option>
                        <?php
                        $currentYear = date('Y');
                        for ($year = $currentYear; $year >= 1900; $year--) {
                            $selected = (isset($dobYear) && $dobYear == $year) ? 'selected' : '';
                            echo "<option value='$year' $selected>$year</option>";
                        }
                        ?>
                    </select>

                    <!-- Month -->
                    <select name="dob-month" id="dob-month">
                        <option value="">月</option>
                        <?php
                        for ($month = 1; $month <= 12; $month++) {
                            $selected = (isset($dobMonth) && $dobMonth == $month) ? 'selected' : '';
                            echo "<option value='$month' $selected>$month</option>";
                        }
                        ?>
                    </select>

                    <!-- Day -->
                    <select name="dob-day" id="dob-day">
                        <option value="">日</option>
                        <?php
                        for ($day = 1; $day <= 31; $day++) {
                            $selected = (isset($dobDay) && $dobDay == $day) ? 'selected' : '';
                            echo "<option value='$day' $selected>$day</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label for="gender">性別:</label>
                    <div>
                        <label>
                            <input type="radio" name="gender" value="男性" <?php echo (isset($gender) && $gender == '男性') ? 'checked' : ''; ?>>
                            男性
                        </label>
                        <label>
                            <input type="radio" name="gender" value="女性" <?php echo (isset($gender) && $gender == '女性') ? 'checked' : ''; ?>>
                            女性
                        </label>
                        <label>
                            <input type="radio" name="gender" value="other" <?php echo (isset($gender) && $gender == 'other') ? 'checked' : ''; ?>>
                            その他
                        </label>
                    </div>
                </div>

                <div>
                    <label for="phone">電話番号:</label>

                    <select name="country-code" id="country-code">
                        <option value="">国コード</option>
                        <option value="+1" <?php echo (isset($countryCode) && $countryCode == '+1') ? 'selected' : ''; ?>>🇺🇸 +1 (USA)</option>
                        <option value="+81" <?php echo (isset($countryCode) && $countryCode == '+81') ? 'selected' : ''; ?>>🇯🇵 +81 (Japan)</option>
                        <option value="+44" <?php echo (isset($countryCode) && $countryCode == '+44') ? 'selected' : ''; ?>>🇬🇧 +44 (UK)</option>
                        <option value="+61" <?php echo (isset($countryCode) && $countryCode == '+61') ? 'selected' : ''; ?>>🇦🇺 +61 (Australia)</option>
                    </select>

                    <input type="tel" name="phone" id="phone" value="<?php echo ($phone ?? ''); ?>">
                </div>

                <div>
                    <label for="address">住所:</label>
                    <textarea name="address" id="address"><?php echo ($address ?? ''); ?></textarea>
                </div>

                <div>
                    <input type="submit" value="登録" name="submit-button">
                </div>
            </form>
        </div>

        <?php include 'footer.php'; ?>
    </body>
</html>
