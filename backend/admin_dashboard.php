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
    <script>
document.addEventListener("DOMContentLoaded", function () {
    // Fetch dashboard statistics
    fetch('admin_stats.php')
        .then(res => res.json())
        .then(data => {
            document.getElementById('poll-count').innerText = data.polls;
            document.getElementById('participant-count').innerText = data.participants;
            document.getElementById('council-count').innerText = data.council;
        })
        .catch(err => console.error('Stats error:', err));

    // Load polls list
    function loadPolls() {
        fetch('poll.php?action=list')
            .then(res => res.json())
            .then(polls => {
                const table = document.getElementById('polls-table');
                table.innerHTML = '';
                polls.forEach(poll => {
                    table.innerHTML += `
                        <tr class="border-b">
                            <td class="p-2">${poll.title}</td>
                            <td class="p-2">${poll.start_date}</td>
                            <td class="p-2">${poll.end_date}</td>
                            <td class="p-2">
                                <button onclick="deletePoll(${poll.id})" class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                    `;
                });
            });
    }
    loadPolls();

    // Add poll
    document.getElementById('add-poll-form')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('poll.php?action=create', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                loadPolls();
            });
    });

    // Delete poll
    window.deletePoll = function (id) {
        if (confirm('Are you sure you want to delete this poll?')) {
            const formData = new FormData();
            formData.append('id', id);
            fetch('poll.php?action=delete', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    loadPolls();
                });
        }
    };

    // Load council members
    function loadCouncil() {
        fetch('council.php?action=list')
            .then(res => res.json())
            .then(members => {
                const councilTable = document.getElementById('council-table');
                councilTable.innerHTML = '';
                members.forEach(member => {
                    councilTable.innerHTML += `
                        <tr class="border-b">
                            <td class="p-2">${member.name}</td>
                            <td class="p-2">${member.position}</td>
                            <td class="p-2">
                                <button onclick="deleteCouncil(${member.id})" class="text-red-500 hover:text-red-700">Delete</button>
                            </td>
                        </tr>
                    `;
                });
            });
    }
    loadCouncil();

    // Add council member
    document.getElementById('add-council-form')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('council.php?action=add', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                alert(data.message);
                loadCouncil();
            });
    });

    // Delete council
    window.deleteCouncil = function (id) {
        if (confirm('Are you sure you want to remove this council member?')) {
            const formData = new FormData();
            formData.append('id', id);
            fetch('council.php?action=delete', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    loadCouncil();
                });
        }
    };
});
</script>
    <footer class="bg-gray-800 text-white p-4 mt-6">
        <p>&copy; 2023 Voting System. All rights reserved.</p>
    </footer>
</body>
</html>
