<?php
require_once __DIR__ . '/functions.php';
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Inventaris') ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="container bar">
        <a class="brand" href="index.php">📦 Inventaris</a>
        <nav>
            <a href="index.php">Produk</a>
            <a href="create.php">Tambah Produk</a>
            <a href="log.php">Log Aktivitas</a>
            <a href="export.php">Export CSV</a>
        </nav>
    </div>
</header>
<main class="container">
<?php if ($flash): ?>
    <div class="alert <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
