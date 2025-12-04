<?php
session_start();
include 'includes/config.php';

$books = [];
$error = '';

// Load all books from the database including already reserved ones
$sql = "SELECT B.isbn, B.title, B.author, B.Edition, B.Year, B.reserved, C.categoryName
        FROM Books B
        JOIN Category C ON B.categoryId = C.categoryId
        ORDER BY B.title";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $books[] = $row; // puts each book data in a row in $book
    }

    $stmt->close();
} else {
    $error = 'Database error while loading books.';
}

/* Pagination */
$booksPerPage = 5;
$totalBooks = count($books);


if (isset($_GET['page'])) { /** checks if page is in URL and a number */
    $currentPage = (int) $_GET['page'];
} else {
    $currentPage = 1;
}

if ($currentPage < 1) {
    $currentPage = 1;
}

if ($totalBooks > 0) {
    $totalPages = ceil($totalBooks / $booksPerPage);
} else {
    $totalPages = 1;
}


if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

// Figure out where to start in the array
$offset = ($currentPage - 1) * $booksPerPage;

// Get only the books for this page
$pagedBooks = array_slice($books, $offset, $booksPerPage);

//  change url for active page
function buildPageUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return $_SERVER['PHP_SELF'] . '?' . http_build_query($params);
}

include 'includes/header.php';
include 'includes/nav.php';
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

    .search-container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        flex-grow: 1;
        padding: 20px 0;
    }

    .search-panel {
        background: rgba(255, 255, 255, 0.06);
        padding: 30px 34px;

        max-width: 900px;
        width: 100%;

        border-radius: 10px;
        box-shadow: 0px 0px 20px rgba(0,0,0,0.25);

        font-size: 20px;
    }

    .search-panel h1 {
        margin: 0 0 18px;
        font-size: 39px;
        font-weight: bold;
        color: #E4E8F2;
    }

    .error-message {
        background-color: #C05C5C;
        color: #fff;
        padding: 10px 14px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 18px;
        text-align: center;
    }

    .results-section {
        margin-top: 10px;
    }

    .results-title {
        font-size: 25px;
        margin-bottom: 12px;
        color: #E4E8F2;
    }

    .results-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 18px;
    }

    .results-table th,
    .results-table td {
        padding: 14px 10px;
        text-align: left;
    }

    .results-table th {
        font-size: 20px;
        border-bottom: 1px solid #2c3145;
        color: #cbd0e0;
    }

    .results-table tr:nth-child(even) {
        background-color: rgba(255, 255, 255, 0.02);
    }

    .results-table tr:nth-child(odd) {
        background-color: rgba(255, 255, 255, 0.01);
    }

    .no-results {
        margin-top: 10px;
        font-size: 18px;
        color: #9FA6BC;
    }

    .btn-reserve {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 6px;
        border: none;
        background-color: #5568A3;
        color: #fff;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        cursor: pointer;
        transition: 0.2s;
        white-space: nowrap;
    }

    .btn-reserve:hover {
        background-color: #7384C8;
    }

    /* Disabled button style */
    .btn-disabled {
        background-color: #555;
        color: #aaa;
        cursor: not-allowed !important;
    }

    .btn-disabled:hover {
        background-color: #555;
    }

    .reserve-cell {
        text-align: right;
    }

    /* Pagination styles */
    .pagination {
        margin-top: 15px;
        font-size: 16px;
        text-align: center;
    }

    .pagination a {
        color: #E4E8F2;
        text-decoration: none;
        margin: 0 6px;
        font-weight: 500;
    }

    .pagination a:hover {
        text-decoration: underline;
    }

    .pagination .current-page {
        font-weight: bold;
        margin: 0 6px;
    }
</style>

<div class="search-container">
    <div class="search-panel">
        <h1>All Books</h1>

        <?php if ($error !== ''): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="results-section">
            <div class="results-title">Browse All Books</div>

            <?php if ($totalBooks > 0): ?>
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Year</th>
                            <th>Category</th>
                            <th class="reserve-cell">Reserve</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagedBooks as $book): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td><?php echo htmlspecialchars($book['Year']); ?></td>
                                <td><?php echo htmlspecialchars($book['categoryName']); ?></td>

                                <td class="reserve-cell">
                                    <?php if ($book['reserved'] == 1): ?> <!-- if booked already reserved-->
                                        <span class="btn-reserve btn-disabled">
                                            Unavailable
                                        </span>
                                    <?php else: ?>
                                        <a
                                            class="btn-reserve"
                                            href="reserve.php?isbn=<?php echo urlencode($book['isbn']); ?>" 
                                        >
                                            Reserve
                                        </a>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php if ($totalPages > 1): ?> <!-- if more than one page show pagination control-->
                    <div class="pagination">
                        <?php if ($currentPage > 1): ?>
                            <a href="<?php echo htmlspecialchars(buildPageUrl($currentPage - 1)); ?>">
                                Previous
                            </a>
                        <?php endif; ?>

                        <span class="current-page">
                            Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?>
                        </span>

                        <?php if ($currentPage < $totalPages): ?>
                            <a href="<?php echo htmlspecialchars(buildPageUrl($currentPage + 1)); ?>">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="no-results">No books found in the database.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>
