<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>BookReserve</title>
    <link rel="icon" type="image/png" href="imgs/logo.png">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #1A1C28; /* Dark navy */
            color: #fff;
        }

        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ---------------- LOGO BAR ---------------- */

        .logo-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 40px;
        }

        .logo-text {
            font-size: 30px;
            font-weight: bold;
            color: #9CA7D1; /* accent color */
        }

        .logo-img {
            height: 38px;
            width: auto;
            object-fit: contain;
            margin-top: -4px;  /* align vertically */
            vertical-align: middle;
        }

        /* ----------- PAGE CONTENT WRAPPER ---------- */
        .content-container {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px 0;
        }
    </style>
</head>
<body>

<div class="page-wrapper">

    <!-- LOGO BAR -->
    <div class="logo-bar">
        <div class="logo-text">BookReserve</div>
        <img src="imgs/logo.png" alt="Logo" class="logo-img">
    </div>

    <!-- Page content starts here -->
    <div class="content-container">
