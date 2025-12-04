<?php
session_start();
include 'includes/config.php';

// Require login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$error = '';
$success = '';

// if cancel post submiited 
if (isset($_POST['cancel_isbn'])) {

    $isbn = $_POST['cancel_isbn'];

    // Delete reservation entry
    $delSql = "DELETE FROM reservedBooks WHERE username = ? AND isbn = ?";
    $delStmt = $conn->prepare($delSql);

    if ($delStmt) {
        $delStmt->bind_param("ss", $username, $isbn);
        $delStmt->execute();

        if ($delStmt->affected_rows > 0) {

            // Update book availability
            $updateSql = "UPDATE Books SET Reserved = 0 WHERE isbn = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("s", $isbn);
            $updateStmt->execute();
            $updateStmt->close();

            $success = "Reservation cancelled successfully.";

        } else {
            $error = "Reservation could not be cancelled.";
        }

        $delStmt->close();

    } else {
        $error = "Database error while cancelling reservation.";
    }
}

// Load all reservations for this user
$reservations = [];

// joins ResBooks, CatID and Books
$sql = "SELECT RB.isbn, RB.reservedFromDate, RB.reservedToDate,
               B.title, B.author, B.Year, C.categoryName
        FROM reservedBooks RB
        JOIN Books B ON RB.isbn = B.isbn
        JOIN Category C ON B.categoryId = C.categoryId
        WHERE RB.username = ?
        ORDER BY RB.reservedFromDate DESC";

$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
    $stmt->close();
} else {
    $error = "Error loading your reservations.";
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<style>
    * { box-sizing: border-box; }
    body { margin:0; font-family:Arial, Helvetica, sans-serif; background:#1A1C28; color:#fff; }

    .page-container {
        display:flex; justify-content:center; align-items:flex-start;
        padding:20px 0;
    }

    .panel {
        background:rgba(255,255,255,0.06);
        padding:30px 34px;
        max-width:900px; width:100%;
        border-radius:10px;
        box-shadow:0 0 20px rgba(0,0,0,0.25);
        font-size:20px;
    }

    .panel h1 {
        margin:0 0 18px;
        font-size:39px;
        font-weight:bold;
        color:#E4E8F2;
    }

    .error-message,
    .success-message {
        padding:10px 14px;
        border-radius:6px;
        font-size:18px;
        margin-bottom:20px;
        text-align:center;
    }

    .error-message { background:#C05C5C; }
    .success-message { background:#4C8F65; }

    .results-table {
        width:100%;
        border-collapse:collapse;
        font-size:18px;
    }

    .results-table th, .results-table td {
        padding:14px 10px;
        text-align:left;
    }

    .results-table th {
        border-bottom:1px solid #2c3145;
        font-size:20px;
        color:#cbd0e0;
    }

    .results-table tr:nth-child(even) {
        background:rgba(255,255,255,0.02);
    }
    .results-table tr:nth-child(odd) {
        background:rgba(255,255,255,0.01);
    }

    .btn-cancel {
        padding:8px 14px;
        background:#C05C5C;
        border:none;
        border-radius:6px;
        color:white;
        font-size:16px;
        font-weight:bold;
        cursor:pointer;
        transition:.2s;
    }

    .btn-cancel:hover {
        background:#E06C6C;
    }

    .no-results {
        font-size:18px;
        margin-top:10px;
        color:#9FA6BC;
    }
</style>

<div class="page-container">
    <div class="panel">
        <h1>My Reservations</h1>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if (count($reservations) > 0): ?>
            <table class="results-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Cancel</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($reservations as $res): ?>
                        <tr>
                            <td><?= htmlspecialchars($res['title']) ?></td>
                            <td><?= htmlspecialchars($res['author']) ?></td>
                            <td><?= htmlspecialchars($res['categoryName']) ?></td>
                            <td><?= htmlspecialchars($res['reservedFromDate']) ?></td>
                            <td><?= htmlspecialchars($res['reservedToDate']) ?></td>

                            <td>
                                <form method="post" style="margin:0;">
                                    <input type="hidden" name="cancel_isbn"
                                           value="<?= htmlspecialchars($res['isbn']) ?>">
                                    <button class="btn-cancel" type="submit">
                                        Cancel
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>

        <?php else: ?>
            <div class="no-results">You have no active reservations.</div>
        <?php endif; ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
