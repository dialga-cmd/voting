<?php
// Simple admin check (optional, can be enhanced with session login)
session_start();
if (!isset($_SESSION['admin'])) {
    // Uncomment below if you want a login check
    // header("Location: login.php");
    // exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Voting System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; }
        .sidebar {
            background: #1e293b;
            color: #fff;
            width: 250px;
            min-height: 100vh;
            padding: 20px;
            position: fixed;
        }
        .sidebar h2 { font-size: 1.5rem; margin-bottom: 20px; text-align: center; }
        .sidebar a {
            display: block; padding: 10px 15px; margin-bottom: 5px;
            border-radius: 5px; transition: background 0.2s;
        }
        .sidebar a:hover, .sidebar a.active { background: #334155; }
        .content { margin-left: 270px; padding: 20px; }
        .section { display: none; }
        .section.active { display: block; }
        .btn {
            padding: 8px 14px; background: #4f46e5;
            color: white; border: none; border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover { background: #4338ca; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        table th { background: #f3f4f6; }
        input, select {
            padding: 8px; border: 1px solid #ccc;
            border-radius: 4px; width: 100%; margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2><i class="fas fa-cogs"></i> Admin</h2>
        <a href="#" data-section="dashboard" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="#" data-section="polls"><i class="fas fa-poll"></i> Manage Polls</a>
        <a href="#" data-section="participants"><i class="fas fa-users"></i> Participants</a>
        <a href="#" data-section="council"><i class="fas fa-user-tie"></i> Student Council</a>
        <a href="#" data-section="results"><i class="fas fa-chart-bar"></i> Results</a>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Dashboard -->
        <div id="dashboard" class="section active">
            <h1 class="text-2xl font-bold mb-4">Welcome to Admin Dashboard</h1>
            <p class="text-gray-700">Use the sidebar to manage polls, participants, student council, and view results.</p>
        </div>

        <!-- Manage Polls -->
        <div id="polls" class="section">
            <h1 class="text-xl font-bold mb-4">Manage Polls</h1>
            <form id="poll-form" class="mb-6">
                <input type="hidden" id="poll-id">
                <label>Title</label>
                <input type="text" id="poll-title" required>
                <label>Start Date</label>
                <input type="date" id="poll-start" required>
                <label>End Date</label>
                <input type="date" id="poll-end" required>
                <label>Candidates (comma separated)</label>
                <input type="text" id="poll-candidates" required>
                <button type="submit" class="btn mt-2">Save Poll</button>
            </form>
            <table>
                <thead>
                    <tr><th>Title</th><th>Start</th><th>End</th><th>Candidates</th><th>Action</th></tr>
                </thead>
                <tbody id="poll-list"></tbody>
            </table>
        </div>

        <!-- Participants -->
        <div id="participants" class="section">
            <h1 class="text-xl font-bold mb-4">Participants</h1>
            <form id="participant-form" class="mb-6">
                <label>Poll ID</label>
                <input type="number" id="participant-poll" required>
                <label>Name</label>
                <input type="text" id="participant-name" required>
                <label>Email</label>
                <input type="email" id="participant-email" required>
                <button type="submit" class="btn mt-2">Add Participant</button>
            </form>
            <table>
                <thead>
                    <tr><th>Name</th><th>Email</th><th>Action</th></tr>
                </thead>
                <tbody id="participant-list"></tbody>
            </table>
        </div>

        <!-- Student Council -->
        <div id="council" class="section">
            <h1 class="text-xl font-bold mb-4">Student Council</h1>
            <form id="council-form" class="mb-6">
                <label>Name</label>
                <input type="text" id="council-name" required>
                <label>Role</label>
                <input type="text" id="council-role" required>
                <button type="submit" class="btn mt-2">Add Member</button>
            </form>
            <table>
                <thead>
                    <tr><th>Name</th><th>Role</th><th>Action</th></tr>
                </thead>
                <tbody id="council-list"></tbody>
            </table>
        </div>

        <!-- Results -->
        <div id="results" class="section">
            <h1 class="text-xl font-bold mb-4">Poll Results</h1>
            <label>Poll ID</label>
            <input type="number" id="result-poll-id" placeholder="Enter Poll ID">
            <button id="fetch-results" class="btn mt-2">Fetch Results</button>
            <table>
                <thead>
                    <tr><th>Candidate</th><th>Votes</th></tr>
                </thead>
                <tbody id="result-list"></tbody>
            </table>
        </div>
    </div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    // Sidebar navigation
    document.querySelectorAll(".sidebar a").forEach(link => {
        link.addEventListener("click", e => {
            e.preventDefault();
            document.querySelectorAll(".sidebar a").forEach(a => a.classList.remove("active"));
            document.querySelectorAll(".section").forEach(sec => sec.classList.remove("active"));
            link.classList.add("active");
            document.getElementById(link.dataset.section).classList.add("active");

            if (link.dataset.section === "polls") loadPolls();
            if (link.dataset.section === "participants") loadParticipants();
            if (link.dataset.section === "council") loadCouncil();
        });
    });

    /* ---------------- Polls ---------------- */
    function loadPolls() {
        fetch("poll.php?action=list")
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById("poll-list");
                list.innerHTML = "";
                data.forEach(p => {
                    list.innerHTML += `<tr>
                        <td>${p.title}</td><td>${p.start_date}</td><td>${p.end_date}</td>
                        <td>${p.candidates}</td>
                        <td>
                            <button class="btn" onclick="editPoll(${p.id},'${p.title}','${p.start_date}','${p.end_date}','${p.candidates}')">Edit</button>
                            <button class="btn bg-red-600" onclick="deletePoll(${p.id})">Delete</button>
                        </td></tr>`;
                });
            });
    }

    document.getElementById("poll-form").addEventListener("submit", e => {
        e.preventDefault();
        const id = document.getElementById("poll-id").value;
        const formData = new FormData();
        formData.append("title", document.getElementById("poll-title").value);
        formData.append("start_date", document.getElementById("poll-start").value);
        formData.append("end_date", document.getElementById("poll-end").value);
        formData.append("candidates", document.getElementById("poll-candidates").value);
        const action = id ? "update" : "create";
        if (id) formData.append("id", id);

        fetch(`poll.php?action=${action}`, { method: "POST", body: formData })
            .then(res => res.json())
            .then(() => { loadPolls(); e.target.reset(); });
    });

    window.editPoll = function (id, title, start, end, candidates) {
        document.getElementById("poll-id").value = id;
        document.getElementById("poll-title").value = title;
        document.getElementById("poll-start").value = start;
        document.getElementById("poll-end").value = end;
        document.getElementById("poll-candidates").value = candidates;
    };

    window.deletePoll = function (id) {
        const formData = new FormData();
        formData.append("id", id);
        fetch("poll.php?action=delete", { method: "POST", body: formData })
            .then(() => loadPolls());
    };

    /* ---------------- Participants ---------------- */
    function loadParticipants() {
        const pollId = document.getElementById("participant-poll").value;
        if (!pollId) return;
        fetch(`participants.php?action=list&poll_id=${pollId}`)
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById("participant-list");
                list.innerHTML = "";
                data.forEach(p => {
                    list.innerHTML += `<tr>
                        <td>${p.name}</td><td>${p.email}</td>
                        <td><button class="btn bg-red-600" onclick="removeParticipant(${p.id}, ${pollId})">Remove</button></td></tr>`;
                });
            });
    }

    document.getElementById("participant-form").addEventListener("submit", e => {
        e.preventDefault();
        const formData = new FormData();
        formData.append("poll_id", document.getElementById("participant-poll").value);
        formData.append("name", document.getElementById("participant-name").value);
        formData.append("email", document.getElementById("participant-email").value);

        fetch("participants.php?action=add", { method: "POST", body: formData })
            .then(() => { loadParticipants(); e.target.reset(); });
    });

    window.removeParticipant = function (id, pollId) {
        const formData = new FormData();
        formData.append("id", id);
        fetch("participants.php?action=remove", { method: "POST", body: formData })
            .then(() => loadParticipants());
    };

    document.getElementById("participant-poll").addEventListener("change", loadParticipants);

    /* ---------------- Council ---------------- */
    function loadCouncil() {
        fetch("council.php?action=list")
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById("council-list");
                list.innerHTML = "";
                data.forEach(m => {
                    list.innerHTML += `<tr>
                        <td>${m.name}</td><td>${m.role}</td>
                        <td><button class="btn bg-red-600" onclick="deleteCouncil(${m.id})">Delete</button></td></tr>`;
                });
            });
    }

    document.getElementById("council-form").addEventListener("submit", e => {
        e.preventDefault();
        const formData = new FormData();
        formData.append("name", document.getElementById("council-name").value);
        formData.append("role", document.getElementById("council-role").value);
        fetch("council.php?action=add", { method: "POST", body: formData })
            .then(() => { loadCouncil(); e.target.reset(); });
    });

    window.deleteCouncil = function (id) {
        const formData = new FormData();
        formData.append("id", id);
        fetch("council.php?action=delete", { method: "POST", body: formData })
            .then(() => loadCouncil());
    };

    /* ---------------- Results ---------------- */
    document.getElementById("fetch-results").addEventListener("click", () => {
        const pollId = document.getElementById("result-poll-id").value;
        fetch(`result.php?poll_id=${pollId}`)
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById("result-list");
                list.innerHTML = "";
                data.forEach(r => {
                    list.innerHTML += `<tr><td>${r.candidate}</td><td>${r.votes}</td></tr>`;
                });
            });
    });
});
</script>
</body>
</html>
