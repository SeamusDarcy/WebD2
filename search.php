<?php
session_start();
include 'includes/config.php';

$searchTerm = trim($_GET['searchTerm'] ?? '');
$categoryId = trim($_GET['categoryId'] ?? '');

$categories = [];
$books = [];
$error = '';

// Load categories for dropdown
$catSql = "SELECT categoryId, categoryName FROM Category ORDER BY categoryName";
$catResult = $conn->query($catSql);
if ($catResult) {
    while ($row = $catResult->fetch_assoc()) {
        $categories[] = $row;
    }
} else {
    $error = 'Could not load categories.';
}

// Run search if user has entered something
if ($searchTerm !== '' || $categoryId !== '') {
    $sql = "SELECT B.isbn, B.title, B.author, B.Edition, B.Year, C.categoryName
            FROM Books B
            JOIN Category C ON B.categoryId = C.categoryId";

    $conditions = [];
    $params = [];
    $types = '';

    if ($searchTerm !== '') {
        $conditions[] = "(B.title LIKE ? OR B.author LIKE ?)";
        $like = '%' . $searchTerm . '%';
        $types .= 'ss';
        $params[] = $like;
        $params[] = $like;
    }

    if ($categoryId !== '') {
        $conditions[] = "B.categoryId = ?";
        $types .= 'i';
        $params[] = (int)$categoryId;
    }

    if (!empty($conditions)) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $books[] = $row;
        }

        $stmt->close();
    } else {
        $error = 'Database error while searching.';
    }
}

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
        width: 100%;
        max-width: 520px;
        border-radius: 10px;
        box-shadow: 0px 0px 20px rgba(0,0,0,0.25);
    }

    .search-panel h1 {
        margin: 0 0 18px;
        font-size: 28px;
        font-weight: bold;
        color: #E4E8F2;
    }

    .form-row {
        margin-bottom: 14px;
    }

    .form-row label {
        display: block;
        margin-bottom: 6px;
        font-size: 14px;
        color: #cbd0e0;
    }

    .form-row input[type="text"],
    .form-row select {
        width: 100%;
        padding: 11px;
        border-radius: 6px;
        border: none;
        background-color: #242738;
        color: #fff;
        font-size: 14px;
    }

    .form-row input::placeholder {
        color: #A8B0C5;
    }

    .btn-submit {
        width: 100%;
        padding: 11px;
        margin-top: 8px;
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

    .results-section {
        margin-top: 22px;
    }

    .results-title {
        font-size: 18px;
        margin-bottom: 8px;
        color: #E4E8F2;
    }

    .results-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .results-table th,
    .results-table td {
        padding: 8px 6px;
        text-align: left;
    }

    .results-table th {
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
        font-size: 14px;
        color: #9FA6BC;
    }
</style>

<div class="search-container">
    <div class="search-panel">
        <h1>Search Books</h1>

        <?php if ($error !== ''): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="get" action="">
            <div class="form-row">
                <label for="searchTerm">Title or Author</label>
                <input
                    type="text"
                    id="searchTerm"
                    name="searchTerm"
                    placeholder="Enter book title or author"
                    value="<?php echo htmlspecialchars($searchTerm); ?>"
                >
            </div>

            <div class="form-row">
                <label for="categoryId">Category</label>
                <select id="categoryId" name="categoryId">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['categoryId']); ?>"
                            <?php if ($categoryId !== '' && (int)$categoryId === (int)$cat['categoryId']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cat['categoryName']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-submit">Search</button>
        </form>

        <?php if ($searchTerm !== '' || $categoryId !== ''): ?>
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['title']); ?></td>
                                    <td><?php echo htmlspecialchars($book['author']); ?></td>
                                    <td><?php echo htmlspecialchars($book['Year']); ?></td>
                                    <td><?php echo htmlspecialchars($book['categoryName']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-results">
                        No books found matching your search.
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php
include 'includes/footer.php';
?>
