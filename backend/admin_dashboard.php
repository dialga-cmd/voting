<?php
require_once __DIR__ . '/config.php';

// Initial poll statistics for page load
$totalPolls = $conn->query("SELECT COUNT(*) FROM polls")->fetchColumn();
$totalParticipants = $conn->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$totalCouncil = $conn->query("SELECT COUNT(*) FROM student_council")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - VoteEasy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#6366f1',
                        secondary: '#8b5cf6',
                        accent: '#06b6d4',
                        success: '#10b981',
                        warning: '#f59e0b',
                        danger: '#ef4444',
                        dark: '#1f2937',
                        light: '#f8fafc'
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.3s ease-out',
                        'bounce-in': 'bounceIn 0.6s ease-out',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes bounceIn {
            0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
            40%, 43% { transform: translate3d(0, -30px, 0); }
            70% { transform: translate3d(0, -15px, 0); }
            90% { transform: translate3d(0, -4px, 0); }
        }
        
        .glass-effect {
            backdrop-filter: blur(20px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        .sidebar-gradient {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }
        
        .nav-link {
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }
        
        .nav-link:hover::before {
            left: 100%;
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
        }
        
        .pulse-ring {
            animation: pulse-ring 2s infinite;
        }
        
        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }
            100% {
                transform: scale(2.4);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-cyan-50 min-h-screen font-sans">

<!-- Sidebar & navigation omitted for brevity, you can use your own markup -->

<main class="px-8 md:ml-72 py-10 relative z-10 max-w-7xl mx-auto">
    <!-- Dashboard stats -->
    <section class="mb-10 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="stat-card rounded-2xl p-8">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-lg text-purple-600">Active Polls</span>
                <i class="fas fa-poll text-2xl text-purple-400"></i>
            </div>
            <div class="text-4xl font-bold text-gray-900" id="totalPollsCount"><?php echo $totalPolls; ?></div>
        </div>
        <div class="stat-card rounded-2xl p-8">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-lg text-green-600">Participants</span>
                <i class="fas fa-users text-2xl text-green-400"></i>
            </div>
            <div class="text-4xl font-bold text-gray-900" id="totalParticipantsCount"><?php echo $totalParticipants; ?></div>
        </div>
        <div class="stat-card rounded-2xl p-8">
            <div class="flex items-center justify-between mb-2">
                <span class="font-semibold text-lg text-cyan-600">Council Members</span>
                <i class="fas fa-user-tie text-2xl text-cyan-400"></i>
            </div>
            <div class="text-4xl font-bold text-gray-900" id="totalCouncilCount"><?php echo $totalCouncil; ?></div>
        </div>
    </section>

    <!-- Polls Management -->
    <section id="polls" class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-700">Manage Polls</h2>
        </div>
        <div class="bg-white rounded-xl shadow p-6 mb-4">
            <form id="createPollForm" class="flex flex-col md:flex-row gap-4">
                <input type="text" name="title" placeholder="Poll Title" class="flex-1 px-4 py-2 rounded border" required>
                <input type="date" name="start_date" class="rounded border px-4 py-2" required>
                <input type="date" name="end_date" class="rounded border px-4 py-2" required>
                <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-xl font-semibold">Create</button>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow p-0 overflow-x-auto">
            <table class="w-full" id="pollsTable">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left">ID</th>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">Dates</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="pollsBody">
                    <!-- AJAX Loaded -->
                </tbody>
            </table>
        </div>
    </section>

    <!-- Participants (by poll) -->
    <section id="participants" class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-700">Participants</h2>
        </div>
        <div class="bg-white rounded-xl shadow p-6 mb-2">
            <form id="addParticipantForm" class="flex flex-col md:flex-row gap-4">
                <select name="poll_id" required id="pollSelect" class="rounded border px-4 py-2 min-w-[180px]"></select>
                <input type="text" name="name" required placeholder="Full Name" class="flex-1 px-4 py-2 rounded border">
                <input type="email" name="email" required placeholder="Email" class="flex-1 px-4 py-2 rounded border">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-xl font-semibold">Add</button>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow p-4" id="participantsList"></div>
    </section>

    <!-- Council Members -->
    <section id="council" class="mb-10">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-700">Council Members</h2>
            <button id="showCouncilFormBtn" class="bg-cyan-600 hover:bg-cyan-700 text-white px-5 py-2 rounded-xl font-semibold">+ Add Member</button>
        </div>
        <div id="councilForm" class="hidden bg-white rounded-xl shadow p-6 mb-2">
            <form id="addCouncilForm" class="flex flex-col md:flex-row gap-4">
                <input type="text" name="name" required placeholder="Full Name" class="flex-1 px-4 py-2 rounded border">
                <input type="text" name="position" required placeholder="Position" class="flex-1 px-4 py-2 rounded border">
                <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded-xl font-semibold">Add</button>
                <button type="button" id="closeCouncilFormBtn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded">Cancel</button>
            </form>
        </div>
        <div class="bg-white rounded-xl shadow p-4" id="councilTable"></div>
    </section>
</main>


<!-- Scripts -->
<script>
// --- Utility: Update stat cards ---
function updateStatCounts() {
    fetch('backend/poll.php?action=count')
        .then(res => res.json())
        .then(data => document.getElementById('totalPollsCount').textContent = data.total);

    fetch('backend/participants.php?action=count')
        .then(res => res.json())
        .then(data => document.getElementById('totalParticipantsCount').textContent = data.total);

    fetch('backend/council.php?action=count')
        .then(res => res.json())
        .then(data => document.getElementById('totalCouncilCount').textContent = data.total);
}


// --- Polls Section ---
function loadPolls() {
    const tbody = document.getElementById('pollsBody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center py-8"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>';
    fetch('backend/poll.php?action=list')
      .then(res => res.json())
      .then(data => {
          if (!data.length) {
              tbody.innerHTML = '<tr><td colspan="4" class="text-center text-gray-500 py-8">No polls found</td></tr>';
              return;
          }
          tbody.innerHTML = data.map(poll => `
              <tr>
                  <td class="px-6 py-4">${poll.id}</td>
                  <td class="px-6 py-4">${poll.title}</td>
                  <td class="px-6 py-4">${poll.start_date} - ${poll.end_date}</td>
                  <td class="px-6 py-4">
                      <button onclick="deletePoll(${poll.id})" class="bg-red-100 text-red-600 px-3 py-1 rounded hover:bg-red-200 text-sm">
                          <i class="fas fa-trash"></i> Delete
                      </button>
                  </td>
              </tr>
          `).join('');
      });
}

function deletePoll(id) {
    if(confirm('Delete this poll and all its candidates?')) {
        fetch('backend/poll.php?action=delete', {
            method: 'POST',
            body: new URLSearchParams({ id })
        })
        .then(res => res.json())
        .then(data => {
            loadPolls();
            updateStatCounts();
            loadPollDropdown();
        });
    }
}

document.getElementById('createPollForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('backend/poll.php?action=create', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        this.reset();
        loadPolls();
        updateStatCounts();
        loadPollDropdown();
    });
});


// --- Participants Section ---
let currentPollId = "";

function loadPollDropdown() {
    fetch('backend/poll.php?action=list')
      .then(res => res.json())
      .then(data => {
          const sel = document.getElementById('pollSelect');
          sel.innerHTML = "";
          if (!data.length) {
              sel.innerHTML = '<option value="">No polls found</option>';
              sel.disabled = true;
              currentPollId = "";
              loadParticipants();
              return;
          }
          data.forEach(poll => {
              sel.innerHTML += `<option value="${poll.id}">${poll.title} (${poll.start_date} – ${poll.end_date})</option>`;
          });
          sel.disabled = false;
          if (!currentPollId) currentPollId = sel.value;
          sel.value = currentPollId;
          loadParticipants();
      });
}

// Load participants of currently selected poll
function loadParticipants() {
    if(!currentPollId) {
        document.getElementById('participantsList').innerHTML = '<p class="text-gray-500">Select a poll to view participants.</p>';
        return;
    }
    const box = document.getElementById('participantsList');
    box.innerHTML = '<p><i class="fas fa-spinner fa-spin"></i> Loading participants…</p>';
    fetch('backend/participants.php?action=list&poll_id=' + encodeURIComponent(currentPollId))
        .then(res => res.json())
        .then(data => {
            if (!data.length) {
                box.innerHTML = '<p class="text-gray-500">No participants for this poll.</p>';
                return;
            }
            let html = '<div class="divide-y">';
            data.forEach(part => {
                html += `
                  <div class="flex justify-between py-3 items-center">
                    <div>
                      <span class="font-semibold">${part.name}</span> <span class="text-sm text-gray-400 ml-1">${part.email}</span>
                    </div>
                    <button onclick="deleteParticipant(${part.id})" class="bg-red-50 text-red-600 px-3 py-1 rounded hover:bg-red-100 text-sm">
                      <i class="fas fa-trash"></i> Remove
                    </button>
                  </div>
                `;
            });
            html += '</div>';
            box.innerHTML = html;
        });
}

document.getElementById('pollSelect').addEventListener('change', function() {
    currentPollId = this.value;
    loadParticipants();
});

document.getElementById('addParticipantForm').addEventListener('submit', function(e) {
    e.preventDefault();
    if (!currentPollId) { alert("Please select a poll!"); return; }
    const formData = new FormData(this);
    formData.set('poll_id', currentPollId); // Set poll_id explicit
    fetch('backend/participants.php?action=add', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            this.reset();
            document.getElementById('pollSelect').value = currentPollId;
            loadParticipants();
            updateStatCounts();
        } else {
            alert("Failed to add participant: " + data.message);
        }
    });
});

function deleteParticipant(id) {
    if (!confirm('Remove this participant and all their votes?')) return;
    fetch('backend/participants.php?action=remove', {
        method: 'POST',
        body: new URLSearchParams({ id })
    })
    .then(res => res.json())
    .then(data => {
        loadParticipants();
        updateStatCounts();
    });
}

// --- Council Section ---
function loadCouncilMembers() {
    const box = document.getElementById('councilTable');
    box.innerHTML = `<div class="text-gray-500 py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Loading council members...</div>`;
    fetch('backend/council.php?action=list')
        .then(res => res.json())
        .then(data => {
            if (!data.length) {
                box.innerHTML = '<p class="text-gray-500">No council members yet.</p>';
                return;
            }
            let html = '<div class="divide-y">';
            data.forEach(mem => {
                html += `
                  <div class="flex justify-between py-3 items-center">
                    <div>
                      <span class="font-semibold">${mem.name}</span>
                      <span class="ml-2 text-sm text-gray-500">${mem.position}</span>
                    </div>
                    <button onclick="deleteCouncil(${mem.id})" class="bg-red-50 text-red-600 px-3 py-1 rounded hover:bg-red-100 text-sm">
                      <i class="fas fa-trash"></i> Remove
                    </button>
                  </div>
                `;
            });
            html += '</div>';
            box.innerHTML = html;
        });
}

document.getElementById('showCouncilFormBtn').onclick = function() {
    document.getElementById('councilForm').classList.remove('hidden');
};
document.getElementById('closeCouncilFormBtn').onclick = function() {
    document.getElementById('councilForm').classList.add('hidden');
};
document.getElementById('addCouncilForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('backend/council.php?action=add', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            this.reset();
            document.getElementById('councilForm').classList.add('hidden');
            loadCouncilMembers();
            updateStatCounts();
        } else {
            alert("Failed to add member: " + data.message);
        }
    });
});
function deleteCouncil(id) {
    if (!confirm('Remove this council member?')) return;
    fetch('backend/council.php?action=delete', {
        method: 'POST',
        body: new URLSearchParams({ id })
    })
    .then(res => res.json())
    .then(data => {
        loadCouncilMembers();
        updateStatCounts();
    });
}

// --- On page load ---
document.addEventListener('DOMContentLoaded', () => {
    loadPolls();
    loadPollDropdown();
    loadCouncilMembers();
    updateStatCounts();
});

</script>
</body>
</html>