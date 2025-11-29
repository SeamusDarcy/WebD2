
<nav class="main-nav">
    <ul class="nav-list">
        <li><a href="search.php">Search Books</a></li>
        <li><a href="books.php">Browse Books</a></li>
    </ul>
</nav>

<style>
    .main-nav {
        position: absolute;
        top: 18px;       
        right: 40px;     
        z-index: 100;    
    }

    .nav-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        gap: 22px;
    }

    .nav-list li {
        margin: 0;
    }

    .nav-list a {
        color: #cbd0e0;
        text-decoration: none;
        font-size: 14px;
        padding: 6px 10px;
        border-radius: 6px;
        transition: 0.2s;
    }

    .nav-list a:hover {
        background: #5568A3;
        color: #ffffff;
        text-decoration: none;
    }
</style>
