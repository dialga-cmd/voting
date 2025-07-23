<?php
session_start();
require_once "config.php";

// Redirect non-admins
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VoteEasy - Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        .card-hover:hover { transform: translateY(-5px); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); }
        .tab-active { border-bottom: 2px solid #4f46e5; color: #4f46e5; }
        .modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); display: none; align-items: center; justify-content: center; z-index: 1000; }
        .modal-content { background: white; padding: 20px; border-radius: 8px; max-width: 400px; width: 100%; }
    </style>
</head>
<<<<<<< HEAD
<body class="bg-gray-100 font-sans">

<!-- Header -->
<header class="gradient-bg text-white shadow-lg">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold"><i class="fas fa-tools mr-2"></i>Admin Dashboard</h1>
        <button onclick="logoutAdmin()" class="bg-white text-purple-600 px-4 py-2 rounded-full font-medium hover:bg-purple-100 transition">Logout</button>
    </div>
</header>

<!-- Tabs -->
<nav class="bg-white shadow-md">
    <div class="container mx-auto px-4">
        <ul class="flex space-x-6">
            <li><button class="tab-btn tab-active py-4" data-tab="dashboard">Dashboard</button></li>
            <li><button class="tab-btn py-4" data-tab="polls">Polls</button></li>
            <li><button class="tab-btn py-4" data-tab="participants">Participants</button></li>
            <li><button class="tab-btn py-4" data-tab="council">Student Council</button></li>
            <li><button class="tab-btn py-4" data-tab="results">Results</button></li>
        </ul>
    </div>
</nav>

<!-- Content -->
<main class="container mx-auto px-4 py-8">
    <!-- Dashboard -->
    <section id="dashboard" class="tab-section">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow card-hover text-center">
                <h3 class="text-xl font-bold">Active Polls</h3>
                <p id="active-polls" class="text-3xl font-bold text-purple-600">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow card-hover text-center">
                <h3 class="text-xl font-bold">Total Participants</h3>
                <p id="total-participants" class="text-3xl font-bold text-purple-600">0</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow card-hover text-center">
                <h3 class="text-xl font-bold">Student Council</h3>
                <p id="council-members" class="text-3xl font-bold text-purple-600">0</p>
            </div>
        </div>
    </section>

    <!-- Polls -->
    <section id="polls" class="tab-section hidden">
        <div class="flex justify-between mb-4">
            <h2 class="text-2xl font-bold">Manage Polls</h2>
            <button onclick="openModal('poll-modal')" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700"><i class="fas fa-plus"></i> Create Poll</button>
        </div>
        <div id="poll-list" class="bg-white rounded-lg shadow p-4">Loading polls...</div>
    </section>

    <!-- Participants -->
    <section id="participants" class="tab-section hidden">
        <div class="flex justify-between mb-4">
            <h2 class="text-2xl font-bold">Manage Participants</h2>
            <button onclick="openModal('participant-modal')" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700"><i class="fas fa-user-plus"></i> Add Participant</button>
        </div>
        <div id="participant-list" class="bg-white rounded-lg shadow p-4">Loading participants...</div>
    </section>

    <!-- Student Council -->
    <section id="council" class="tab-section hidden">
        <div class="flex justify-between mb-4">
            <h2 class="text-2xl font-bold">Manage Student Council</h2>
            <button onclick="openModal('council-modal')" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700"><i class="fas fa-user-tie"></i> Add Member</button>
        </div>
        <div id="council-list" class="bg-white rounded-lg shadow p-4">Loading members...</div>
    </section>

    <!-- Results -->
    <section id="results" class="tab-section hidden">
        <h2 class="text-2xl font-bold mb-4">Poll Results</h2>
        <div id="result-list" class="bg-white rounded-lg shadow p-4">Loading results...</div>
    </section>
</main>

<!-- Modals -->
<div id="poll-modal" class="modal">
    <div class="modal-content">
        <h3 class="text-lg font-bold mb-4">Create Poll</h3>
        <input type="text" id="poll-title" placeholder="Poll Title" class="w-full mb-3 px-3 py-2 border rounded">
        <input type="date" id="poll-start" class="w-full mb-3 px-3 py-2 border rounded">
        <input type="date" id="poll-end" class="w-full mb-3 px-3 py-2 border rounded">
        <button onclick="createPoll()" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Create</button>
        <button onclick="closeModal('poll-modal')" class="ml-2 text-gray-500 hover:text-black">Cancel</button>
    </div>
</div>

<div id="participant-modal" class="modal">
    <div class="modal-content">
        <h3 class="text-lg font-bold mb-4">Add Participant</h3>
        <input type="text" id="participant-name" placeholder="Name" class="w-full mb-3 px-3 py-2 border rounded">
        <input type="email" id="participant-email" placeholder="Email" class="w-full mb-3 px-3 py-2 border rounded">
        <input type="number" id="participant-poll" placeholder="Poll ID" class="w-full mb-3 px-3 py-2 border rounded">
        <button onclick="addParticipant()" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Add</button>
        <button onclick="closeModal('participant-modal')" class="ml-2 text-gray-500 hover:text-black">Cancel</button>
    </div>
</div>

<div id="council-modal" class="modal">
    <div class="modal-content">
        <h3 class="text-lg font-bold mb-4">Add Council Member</h3>
        <input type="text" id="council-name" placeholder="Name" class="w-full mb-3 px-3 py-2 border rounded">
        <input type="text" id="council-position" placeholder="Position" class="w-full mb-3 px-3 py-2 border rounded">
        <button onclick="addCouncilMember()" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Add</button>
        <button onclick="closeModal('council-modal')" class="ml-2 text-gray-500 hover:text-black">Cancel</button>
    </div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('tab-active'));
    document.querySelector(`[data-tab="${tab}"]`).classList.add('tab-active');
    document.querySelectorAll('.tab-section').forEach(sec => sec.classList.add('hidden'));
    document.getElementById(tab).classList.remove('hidden');
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => switchTab(btn.getAttribute('data-tab')));
});

// Load dashboard data
function loadDashboard() {
    fetch('poll.php?action=count')
        .then(res => res.json())
        .then(data => document.getElementById('active-polls').textContent = data.count || 0);
    fetch('participants.php?action=count')
        .then(res => res.json())
        .then(data => document.getElementById('total-participants').textContent = data.count || 0);
    fetch('council.php?action=count')
        .then(res => res.json())
        .then(data => document.getElementById('council-members').textContent = data.count || 0);
}

// Modal controls
function openModal(id) { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

// Poll operations
function createPoll() {
    const title = document.getElementById('poll-title').value;
    const start = document.getElementById('poll-start').value;
    const end = document.getElementById('poll-end').value;
    fetch('poll.php?action=create', {
        method: 'POST',
        body: new URLSearchParams({ title, start_date: start, end_date: end })
    }).then(res => res.json()).then(data => {
        alert(data.status);
        closeModal('poll-modal');
        loadPolls();
        loadDashboard();
    });
}

// Participant operations
function addParticipant() {
    const name = document.getElementById('participant-name').value;
    const email = document.getElementById('participant-email').value;
    const poll_id = document.getElementById('participant-poll').value;
    fetch('participants.php?action=add', {
        method: 'POST',
        body: new URLSearchParams({ name, email, poll_id })
    }).then(res => res.json()).then(data => {
        alert(data.status);
        closeModal('participant-modal');
        loadParticipants();
        loadDashboard();
    });
}

// Council operations
function addCouncilMember() {
    const name = document.getElementById('council-name').value;
    const position = document.getElementById('council-position').value;
    fetch('council.php?action=add', {
        method: 'POST',
        body: new URLSearchParams({ name, position })
    }).then(res => res.json()).then(data => {
        alert(data.status);
        closeModal('council-modal');
        loadCouncil();
        loadDashboard();
    });
}

// Logout
function logoutAdmin() {
    fetch('logout.php').then(() => window.location.href = '../index.html');
}

// Initial load
window.onload = () => {
    loadDashboard();
    switchTab('dashboard');
};
</script>
=======
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
>>>>>>> 3f6de91878b343bb0bd5db57afe54037675d653e
</body>
</html>
