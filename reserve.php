<?php
session_start();
include 'includes/config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$isbn = trim($_GET['isbn'] ?? $_POST['isbn'] ?? '');
$book = null;
$error = '';
$success = '';

$today = date('Y-m-d');
$defaultTo = date('Y-m-d', strtotime('+7 days'));  //default res time is 7 days 

// Handle reservation submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $reserveFrom = $_POST['reserve_from'] ?? '';
    $reserveTo = $_POST['reserve_to'] ?? '';

    // Validate date range
    if ($reserveFrom < $today) {
        $error = "The 'Reserve From' date cannot be in the past.";
    } elseif ($reserveTo < $reserveFrom) {
        $error = "'Reserve To' must be the same or after 'Reserve From'.";
    } else {

        // Insert data into reservedBooks
        $insertSql = "INSERT INTO reservedBooks (username, isbn, reservedFromDate, reservedToDate)
                      VALUES (?, ?, ?, ?)";

        $insertStmt = $conn->prepare($insertSql);

        if ($insertStmt) {
            $insertStmt->bind_param("ssss", $username, $isbn, $reserveFrom, $reserveTo);
            
            if ($insertStmt->execute()) {

                // Mark book as reserved in Books table
                $updateSql = "UPDATE Books SET Reserved = 1 WHERE isbn = ?";
                $updateStmt = $conn->prepare($updateSql);
                $updateStmt->bind_param("s", $isbn);
                $updateStmt->execute();
                $updateStmt->close();

                $success = "Book reserved successfully from $reserveFrom to $reserveTo.";

            } else {
                $error = "This book is already reserved or you have already reserved it.";
            }

            $insertStmt->close();
        } else {
            $error = "Database error while inserting reservation.";
        }
    }
}

// Load book info
if ($isbn !== '') {

    $sql = "SELECT B.isbn, B.title, B.author, B.Edition, B.Year, B.Reserved, C.categoryName
            FROM Books B
            JOIN Category C ON B.categoryId = C.categoryId
            WHERE B.isbn = ?";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $isbn);
        $stmt->execute();
        $book = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$book && !$error) {
            $error = "Book not found.";
        }
    } else {
        $error = "Database error while loading book details.";
    }
} else {
    $error = "No book specified.";
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<style>
    * { box-sizing: border-box; }
    body {
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
        background: #1A1C28;
        color: #fff;
    }
    .search-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 20px 0;
    }
    .search-panel {
        background: rgba(255,255,255,0.06);
        padding: 30px 34px;
        max-width: 900px;
        width: 100%;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.25);
        font-size: 20px;
    }
    .search-panel h1 {
        margin: 0 0 18px;
        font-size: 39px;
        font-weight: bold;
        color: #E4E8F2;
    }
    .error-message, .success-message {
        padding: 10px 14px;
        border-radius: 6px;
        font-size: 18px;
        margin-bottom: 20px;
        text-align: center;
    }
    .error-message { background-color: #C05C5C; }
    .success-message { background-color: #4C8F65; }
    .book-details p { margin: 6px 0; font-size: 18px; }
    .status-pill {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 16px;
        font-weight: bold;
    }
    .status-available { background:#3C8D5B; color:#E8FBEF; }
    .status-reserved { background:#8D3C3C; color:#FBE8E8; }
    .btn-submit {
        padding: 12px 22px;
        background:#5568A3;
        color:white;
        border:none;
        border-radius:6px;
        font-size:18px;
        font-weight:bold;
        cursor:pointer;
        transition:.2s;
    }
    .btn-submit:hover { background:#7384C8; }
    .back-link {
        display:inline-block;
        margin-top:16px;
        font-size:16px;
        color:#A8B0C5;
        text-decoration:none;
    }
</style>

<div class="search-container">
    <div class="search-panel">
        <h1>Reserve Book</h1>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($book): ?>
            <div class="book-details">
                <p><strong>Title:</strong> <?= htmlspecialchars($book['title']) ?></p>
                <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
                <p><strong>Year:</strong> <?= htmlspecialchars($book['Year']) ?></p>
                <p><strong>Category:</strong> <?= htmlspecialchars($book['categoryName']) ?></p>
                <p><strong>ISBN:</strong> <?= htmlspecialchars($book['isbn']) ?></p>
                <p><strong>Status:</strong>
                    <?php if ($book['Reserved']): ?>
                        <span class="status-pill status-reserved">Reserved</span>
                    <?php else: ?>
                        <span class="status-pill status-available">Available</span>
                    <?php endif; ?>
                </p>
            </div>

            <?php if (!$book['Reserved'] && !$success): ?>
                <form method="post">
                    <input type="hidden" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>">

                    <p>Select reservation dates:</p>

                    <p>
                        <label>Reserve From:</label><br>
                        <input type="date" name="reserve_from"
                               min="<?= $today ?>"
                               value="<?= $today ?>"
                               required>
                    </p>

                    <p>
                        <label>Reserve To:</label><br>
                        <input type="date" name="reserve_to"
                               min="<?= $today ?>"
                               value="<?= $defaultTo ?>"
                               required>
                    </p>

                    <button type="submit" class="btn-submit">Confirm Reservation</button>
                </form>
            <?php elseif ($book['Reserved']): ?>
                <p>This book is currently unavailable and cannot be reserved.</p>
            <?php endif; ?>
        <?php endif; ?>

        <a href="books.php" class="back-link">&larr; Back to all books</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
