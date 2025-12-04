<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>BookReserve</title>

    <!-- icon -->
    <link rel="icon" type="image/png" href="imgs/logo.png">

    <style>
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

        
        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 40px;
            background: #14161F;
            border-bottom: 1px solid #2c3145;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-text {
            font-size: 30px;
            font-weight: bold;
            color: #9CA7D1;
        }

        .logo-img {
            height: 38px;
            width: auto;
            object-fit: contain;
            margin-top: -4px;
        }
    </style>
</head>

<body>

<div class="page-wrapper">

    <div class="header-bar">
        <div class="header-left">
            <span class="logo-text">BookReserve</span>
            <img src="imgs/logo.png" alt="Logo" class="logo-img">
        </div>

        <!-- Set up variable to hide nav bar on certian pages if needed (e.g. login) -->
        <?php if (empty($hideNav)): ?>  
            <div class="header-right">
                <?php include __DIR__ . '/nav.php'; ?>
            </div>
        <?php endif; ?>
    </div>
