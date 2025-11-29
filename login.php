<?php
session_start();
include 'includes/config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $sql = "SELECT username, password, FirstName, Surname 
                FROM User 
                WHERE username = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {

                if ($row['password'] === $password) {

                    $_SESSION['username'] = $row['username'];
                    $_SESSION['fullName'] = $row['FirstName'] . ' ' . $row['Surname'];

                    header('Location: index.php');
                    exit;
                } else {
                    $error = 'Invalid username or password.';
                }
            } else {
                $error = 'Invalid username or password.';
            }

            $stmt->close();
        } else {
            $error = 'Database error. Please try again later.';
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
        background: #1A1C28; /* same dark navy as account page */
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
        padding: 20px 0;
    }

    .login-panel {
        background: rgba(255, 255, 255, 0.06);
        padding: 50px 60px;
        width: 100%;
        max-width: 420px;
        border-radius: 10px;
        box-shadow: 0px 0px 20px rgba(0,0,0,0.25);
    }

    .login-panel h1 {
        margin: 0 0 25px;
        font-size: 32px;
        font-weight: bold;
        color: #E4E8F2;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group input {
        width: 100%;
        padding: 15px;
        border-radius: 6px;
        border: none;
        background-color: #242738;
        color: #fff;
        font-size: 15px;
    }

    .form-group input::placeholder {
        color: #A8B0C5;
    }

    .btn-submit {
        width: 100%;
        padding: 13px;
        margin-top: 10px;
        background-color: #5568A3;
        border: none;
        border-radius: 6px;
        color: white;
        font-size: 17px;
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
        padding: 10px 12px;
        border-radius: 6px;
        margin-bottom: 18px;
        font-size: 14px;
        text-align: center;
    }

    .extra-text {
        margin-top: 35px;
        font-size: 15px;
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
        <h1>Sign In</h1>

        <?php if ($error !== ''): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <input
                    type="text"
                    name="username"
                    placeholder="Username"
                    required
                >
            </div>

            <div class="form-group">
                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    required
                >
            </div>

            <button type="submit" class="btn-submit">Sign In</button>

            <div class="extra-text">
                New here? <a href="account.php">Create an account.</a>
            </div>
        </form>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
