<?php
require 'config.php';
session_start();
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-gray-800 text-white p-4">
        <h1 class="text-xl">Admin Dashboard</h1>
    </header>
    <main class="p-6">
        <h2 class="text-2xl font-bold mb-4">Welcome, Admin</h2>
        <div id="stats" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8"></div>
        <a href="polls.html" class="bg-purple-600 text-white px-4 py-2 rounded">Manage Polls</a>
        <a href="participants.html" class="bg-green-600 text-white px-4 py-2 rounded">Manage Participants</a>
        <a href="council.html" class="bg-blue-600 text-white px-4 py-2 rounded">Student Council</a>
        <a href="results.html" class="bg-yellow-600 text-white px-4 py-2 rounded">Results</a>
    </main>
</body>
</html>
