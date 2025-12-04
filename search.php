<?php
session_start();
include 'includes/config.php';

$searchTerm   = trim($_GET['searchTerm'] ?? '');
$categoryId   = trim($_GET['categoryId'] ?? '');
$formSubmitted = isset($_GET['searchTerm']) || isset($_GET['categoryId']);

$categories = [];
$books      = [];
$error      = '';

// Load categories for dropdown
$catSql    = "SELECT categoryId, categoryName FROM Category ORDER BY categoryName";
$catResult = $conn->query($catSql);
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) { // gets one row at a time
        $categories[] = $row;
    }
} else {
    $error = 'Could not load categories.';
}

// When search is submitted
if ($formSubmitted) {
    $sql = "SELECT B.isbn, B.title, B.author, B.Edition, B.Year, B.Reserved, C.categoryName
            FROM Books B
            JOIN Category C ON B.categoryId = C.categoryId";

    $conditions = [];
    $params     = [];
    $types      = '';

    if ($searchTerm !== '') {
        $conditions[] = "(B.title LIKE ? OR B.author LIKE ?)"; // sets the query to use Searched terms
        $like   = '%' . $searchTerm . '%';
        $types .= 'ss';
        $params[] = $like;
        $params[] = $like;
    }

    if ($categoryId !== '') {
        $conditions[] = "B.categoryId = ?"; //uses user seacrch term for the query, 
        $types       .= 'i';
        $params[]     = (int)$categoryId;
    }

    if (!empty($conditions)) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions); // is no categorey entered searchs all cats
    }

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params); // use ...$param to combine both searchs into one
        }
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }

        $stmt->close();
    } else {
        $error = 'Database error during search.';
    }
}

/* Simple Pagination (same style as Browse Books) */
$booksPerPage = 5;
$totalBooks   = count($books);

// Get current page from URL
if (isset($_GET['page'])) {
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


$offset = ($currentPage - 1) * $booksPerPage;

// Get only the books for this page
$pagedBooks = array_slice($books, $offset, $booksPerPage);


function buildPageUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return $_SERVER['PHP_SELF'] . '?' . http_build_query($params);
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<style>
    * { box-sizing: border-box; }
    body { margin:0; font-family:Arial, Helvetica, sans-serif; background:#1A1C28; color:#fff; }

    .search-container {
        display:flex; justify-content:center; align-items:flex-start;
        flex-grow:1; padding:20px 0;
    }

    .search-panel {
        background:rgba(255,255,255,0.06);
        padding:30px 34px;
        max-width:900px; width:100%;
        border-radius:10px;
        box-shadow:0px 0px 20px rgba(0,0,0,0.25);
        font-size:20px;
    }

    .search-panel h1 {
        margin:0 0 18px;
        font-size:39px;
        font-weight:bold;
        color:#E4E8F2;
    }

    .form-row {
        margin-bottom:14px;
    }

    .form-row label {
        display:block; margin-bottom:6px;
        font-size:20px; color:#cbd0e0;
    }

    .form-row input, .form-row select {
        width:100%; padding:18px;
        border-radius:6px; border:none;
        background:#242738; color:#fff;
        font-size:20px;
    }

    .btn-submit {
        width:100%; padding:18px; margin-top:8px;
        background:#5568A3; border:none;
        border-radius:6px; color:white;
        font-size:22px; font-weight:bold;
        cursor:pointer; transition:.2s;
    }
    .btn-submit:hover { background:#7384C8; }

    .error-message {
        background:#C05C5C; color:#fff;
        padding:10px 14px; border-radius:6px;
        margin-bottom:20px; font-size:18px;
        text-align:center;
    }

    .results-section { margin-top:22px; }
    .results-title { font-size:25px; margin-bottom:12px; color:#E4E8F2; }

    .results-table {
        width:100%; border-collapse:collapse;
        font-size:18px;
    }

    .results-table th, .results-table td {
        padding:14px 10px; text-align:left;
    }

    .results-table th {
        font-size:20px; border-bottom:1px solid #2c3145;
        color:#cbd0e0;
    }

    .results-table tr:nth-child(even) { background:rgba(255,255,255,0.02); }
    .results-table tr:nth-child(odd) { background:rgba(255,255,255,0.01); }

    .no-results { margin-top:10px; font-size:18px; color:#9FA6BC; }

    /* Reserve button */
    .btn-reserve {
        display:inline-block; padding:8px 14px;
        border-radius:6px; border:none;
        background:#5568A3; color:#fff;
        font-size:16px; font-weight:bold;
        text-decoration:none; cursor:pointer;
        transition:.2s; white-space:nowrap;
    }
    .btn-reserve:hover { background:#7384C8; }

    .btn-disabled {
        background:#555; color:#aaa;
        cursor:not-allowed !important;
    }
    .btn-disabled:hover { background:#555; }

    .reserve-cell { text-align:right; }

   
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
        <h1>Search Books</h1>

        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="get" action="">
            <div class="form-row">
                <label for="searchTerm">Title or Author</label>
                <input type="text" id="searchTerm" name="searchTerm"
                       placeholder="Enter book title or author"
                       value="<?= htmlspecialchars($searchTerm) ?>">
            </div>

            <div class="form-row">
                <label for="categoryId">Category</label>
                <select id="categoryId" name="categoryId">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['categoryId'] ?>"
                            <?= ($categoryId !== '' && (int)$categoryId === (int)$cat['categoryId']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['categoryName']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-submit">Search</button>
        </form>

        <?php if ($formSubmitted): ?>
            <div class="results-section">
                <div class="results-title">Results</div>

                <?php if (count($books) > 0): ?>
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
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td><?= htmlspecialchars($book['Year']) ?></td>
                                    <td><?= htmlspecialchars($book['categoryName']) ?></td>

                                    <td class="reserve-cell">
                                        <?php if ($book['Reserved'] == 1): ?>
                                            <span class="btn-reserve btn-disabled">Unavailable</span>
                                        <?php else: ?>
                                            <a class="btn-reserve"
                                               href="reserve.php?isbn=<?= urlencode($book['isbn']) ?>">
                                                Reserve
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="<?= htmlspecialchars(buildPageUrl($currentPage - 1)) ?>">
                                    Previous
                                </a>
                            <?php endif; ?>

                            <span class="current-page">
                                Page <?= $currentPage ?> of <?= $totalPages ?>
                            </span>

                            <?php if ($currentPage < $totalPages): ?>
                                <a href="<?= htmlspecialchars(buildPageUrl($currentPage + 1)) ?>">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="no-results">No books found matching your search.</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
