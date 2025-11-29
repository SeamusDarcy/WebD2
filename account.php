<?php
session_start();
include 'includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username    = trim($_POST['username'] ?? '');
    $password    = trim($_POST['password'] ?? '');
    $confirm     = trim($_POST['confirm_password'] ?? '');
    $firstName   = trim($_POST['firstName'] ?? '');
    $surname     = trim($_POST['surname'] ?? '');
    $addressLine = trim($_POST['addressLine'] ?? '');
    $addressLine2= trim($_POST['addressLine2'] ?? '');
    $city        = trim($_POST['city'] ?? '');
    $email       = trim($_POST['email'] ?? '');
    $mobile      = trim($_POST['mobile'] ?? '');

    if (
        $username === '' || $password === '' || $confirm === '' ||
        $firstName === '' || $surname === '' ||
        $addressLine === '' || $addressLine2 === '' || $city === '' ||
        $email === '' || $mobile === ''
    ) {
        $error = 'Please fill in all fields.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) !== 6) {
        $error = 'Password must be exactly 6 characters long.';
    } elseif (!ctype_digit($mobile) || strlen($mobile) !== 10) {
        $error = 'Mobile number must be numeric and exactly 10 digits.';
    } else {

        $checkSql = "SELECT username FROM User WHERE username = ?";
        $checkStmt = $conn->prepare($checkSql);

        if ($checkStmt) {
            $checkStmt->bind_param("s", $username);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                $error = 'That username is already taken.';
            } else {

                $insertSql = "INSERT INTO User 
                    (username, password, FirstName, Surname, AddressLine, AddressLine2, City, email, mobile)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $insertStmt = $conn->prepare($insertSql);

                if ($insertStmt) {
                    $insertStmt->bind_param(
                        "sssssssss",
                        $username,
                        $password,
                        $firstName,
                        $surname,
                        $addressLine,
                        $addressLine2,
                        $city,
                        $email,
                        $mobile
                    );

                    if ($insertStmt->execute()) {
                        header('Location: login.php');
                        exit;
                    } else {
                        $error = 'Failed to create account.';
                    }

                    $insertStmt->close();
                } else {
                    $error = 'Database error.';
                }
            }

            $checkStmt->close();
        } else {
            $error = 'Database error.';
        }
    }
}

$hideNav = true;
include 'includes/header.php';
?>

<style>
    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
        background: #1A1C28;
        color: #fff;
    }

    .page-wrapper {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-grow: 1;
        padding: 8px 0; 
    }

    .login-panel {
        background: rgba(255, 255, 255, 0.06);
        padding: 33px 38px;
        width: 100%;
        max-width: 360px;
        border-radius: 10px;
        box-shadow: 0px 0px 20px rgba(0,0,0,0.25);
    }

    .login-panel h1 {
        margin: 0 0 16px;
        font-size: 27px;
        font-weight: bold;
        color: #E4E8F2;
    }

    .form-group {
        margin-bottom: 11px;
    }

    .form-group input {
        width: 100%;
        padding: 11px;
        border-radius: 6px;
        border: none;
        background-color: #242738;
        color: #fff;
        font-size: 14px;
    }

    .form-group input::placeholder {
        color: #A8B0C5;
    }

    .btn-submit {
        width: 100%;
        padding: 11px;
        margin-top: 10px;
        background-color: #5568A3;
        border: none;
        border-radius: 6px;
        color: white;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-submit:hover {
        background-color: #7384C8;
    }

    .error-message {
        background-color: #C05C5C;
        color: #fff;
        padding: 7px 10px;
        border-radius: 6px;
        margin-bottom: 14px;
        font-size: 13px;
        text-align: center;
    }

    .extra-text {
        margin-top: 18px;
        font-size: 14px;
        color: #9FA6BC;
    }

    .extra-text a {
        color: #ffffff;
        text-decoration: none;
        font-weight: 500;
    }

    .extra-text a:hover {
        text-decoration: underline;
    }
</style>

<div class="login-container">
    <div class="login-panel">
        <h1>Create Account</h1>

        <?php if ($error !== ''): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">

            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Password (6 characters)" required minlength="6" maxlength="6">
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" placeholder="Confirm Password" required minlength="6" maxlength="6">
            </div>

            <div class="form-group">
                <input type="text" name="firstName" placeholder="First Name" required>
            </div>

            <div class="form-group">
                <input type="text" name="surname" placeholder="Surname" required>
            </div>

            <div class="form-group">
                <input type="text" name="addressLine" placeholder="Address Line 1" required>
            </div>

            <div class="form-group">
                <input type="text" name="addressLine2" placeholder="Address Line 2" required>
            </div>

            <div class="form-group">
                <input type="text" name="city" placeholder="City" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="form-group">
                <input type="text" name="mobile" placeholder="Mobile (10 digits)" required maxlength="10">
            </div>

            <button type="submit" class="btn-submit">Create Account</button>

            <div class="extra-text">
                Already have an account? <a href="login.php">Sign in.</a>
            </div>

        </form>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
