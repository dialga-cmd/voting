<?php
require_once __DIR__ . '/config.php';
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { 
      background: linear-gradient(135deg, #e0eaff 0%, #e5e9fa 100%); margin: 0; font-family: 'Segoe UI', Arial, sans-serif; min-height: 100vh;
    }
    .sidebar-gradient {
      background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
      position: fixed; top: 0; left: 0; width: 260px; min-height: 100vh; z-index: 40;
      color: #fff; padding: 30px 18px 18px 20px; display: flex; flex-direction: column;
    }
    .sidebar-header { font-size: 1.4em; font-weight: 700; margin-bottom: 8px; }
    .sidebar-desc { color: #c3c5ce; font-size: 0.98em; margin-bottom: 28px; }
    .nav-link {
      display: block; padding: 13px 18px; border-radius: 11px; color: #e6e8ee;
      font-size: 1.12em; margin-bottom: 7px; text-decoration: none; position: relative;
      transition: background 0.2s, color 0.2s;
    }
    .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.14); color: #fff; }
    .logout-btn {
      background: linear-gradient(90deg,#eb4e4e 0%,#c52234 100%); color: #fff;
      padding:12px 0; border:none; width: 100%; border-radius: 13px; font-weight: 600;
      font-size: 1em; margin-top: auto; cursor: pointer;
    }
    main { margin-left: 280px; padding: 40px 46px 40px 36px; min-height: 100vh; }
    h1, h2 { margin-top: 0; }
    .stat-row { display:flex;gap:32px;flex-wrap:wrap; }
    .stat-card {
      background: linear-gradient(135deg, rgba(255,255,255,0.93) 0%, rgba(255,255,255,0.78) 100%);
      backdrop-filter: blur(10px); border-radius: 20px; border: 1px solid #dde3ee;
      padding: 26px 22px; margin-bottom: 30px; box-shadow: 0 14px 32px -10px #bbc6e3;
      display: flex; align-items: center; justify-content: space-between;
      transition: box-shadow 0.25s, transform 0.25s;
    }
    .stat-card:hover { box-shadow: 0 18px 44px -10px #657ac8; transform: translateY(-3px);}
    .panel {
      background: rgba(255,255,255,0.96); border-radius: 18px; box-shadow: 0 8px 24px -8px #ced5e9;
      margin-bottom: 46px; padding: 32px 25px 24px 23px;
    }
    .panel-header { font-weight: bold; color: #444; margin-bottom: 22px; font-size: 1.28rem; }
    /* Table & Form */
    table { width:100%; border-collapse:collapse; }
    th, td { padding: 14px 15px; text-align: left; }
    th { background: #f4f5fa; }
    tr:nth-child(even) td { background: #f8fafd; }
    input, select, button {
      font-size: 1em; border: 1px solid #d6dae5; border-radius: 8px;
      padding: 10px 13px; background: #fff; outline: none;
    }
    .action-btn {
      color: #fff; background: linear-gradient(90deg,#6366f1 0%,#8b5cf6 100%);
      border: none; padding: 8px 20px; border-radius: 8px; font-weight: 600;
      transition: background 0.2s;
    }
    .action-btn:hover { background: #6d28d9; }
    .delete-btn {
      background: #fae1e1; color: #e34d4d; border: none; padding: 7px 17px;
      border-radius: 8px; font-size: 1em; font-weight: 500;
    }
    .delete-btn:hover { background: #e95050; color: #fff; }
    .hidden { display: none !important; }
    /* Animations */
    @keyframes fadeIn { from { opacity: 0;} to{ opacity: 1;} }
    .animate-fade-in { animation: fadeIn 0.6s; }
    .glass-effect { backdrop-filter: blur(20px); background: rgba(255,255,255,0.09);}
  </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar-gradient">
  <div class="sidebar-header"><i class="fas fa-user-shield"></i> Admin Panel</div>
  <div class="sidebar-desc">VoteEasy Dashboard</div>
  <a href="#dashboard" class="nav-link active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="#polls" class="nav-link"><i class="fas fa-poll"></i> Manage Polls</a>
  <a href="#participants" class="nav-link"><i class="fas fa-users"></i> Participants</a>
  <form action="../index.html" method="get" style="margin-top:32px">
    <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
  </form>
</aside>

<main>
  <!-- Dashboard Stats -->
  <section id="dashboard" style="margin-bottom:50px;">
    <h1 style="font-size:2em;font-weight:bold;">Welcome back, Admin!</h1>
    <p class="text-gray-600" style="margin-bottom:18px;">See the live stats for your voting system.</p>
    <div class="stat-row">
      <div class="stat-card card-hover" style="flex:1;">
        <div>
          <div style="font-size:1.7em;font-weight:700;" id="totalPollsCount"><?php echo $totalPolls; ?></div>
          <div class="text-gray-600">Active Polls</div>
        </div>
        <i class="fas fa-poll" style="font-size:2.1em;color:#6366f1;"></i>
      </div>
      <div class="stat-card card-hover" style="flex:1;">
        <div>
          <div style="font-size:1.7em;font-weight:700;" id="totalParticipantsCount"><?php echo $totalParticipants; ?></div>
          <div class="text-gray-600">Participants</div>
        </div>
        <i class="fas fa-users" style="font-size:2.1em;color:#10b981;"></i>
      </div>
      <div class="stat-card card-hover" style="flex:1;">
        <div>
          <div style="font-size:1.7em;font-weight:700;" id="totalCouncilCount"><?php echo $totalCouncil; ?></div>
          <div class="text-gray-600">Council Members</div>
        </div>
        <i class="fas fa-user-tie" style="font-size:2.1em;color:#06b6d4;"></i>
      </div>
    </div>
  </section>

  <!-- Polls Management Panel -->
  <section id="polls" class="panel animate-fade-in">
    <div class="panel-header mb-4"><i class="fas fa-poll"></i> Manage Polls</div>
    <form id="createPollForm" style="display:flex;gap:13px;flex-wrap:wrap;align-items:center;margin-bottom:18px;">
      <input type="text" name="title" placeholder="Poll Title" required>
      <input type="date" name="start_date" required>
      <input type="date" name="end_date" required>
      <button type="submit" class="action-btn">Create Poll</button>
    </form>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Title</th><th>Duration</th><th>Actions</th>
          </tr>
        </thead>
        <tbody id="pollsBody"></tbody>
      </table>
    </div>
  </section>

  <!-- Participants Panel -->
  <section id="participants" class="panel animate-fade-in">
    <div class="panel-header mb-4"><i class="fas fa-users"></i> Manage Participants</div>
    <form id="addParticipantForm" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-bottom:14px;">
      <select name="poll_id" required id="pollSelect"></select>
      <input type="text" name="name" required placeholder="Full Name">
      <input type="email" name="email" required placeholder="Email">
      <button type="submit" class="action-btn">Add Participant</button>
    </form>
    <div id="participantsList"></div>
  </section>
</main>

<script>
// --- Dashboard stat update
function updateStatCounts() {
  fetch('poll.php?action=count')
    .then(res => res.json())
    .then(data => document.getElementById('totalPollsCount').textContent = data.total);
  fetch('participants.php?action=count')
    .then(res => res.json())
    .then(data => document.getElementById('totalParticipantsCount').textContent = data.total);
  fetch('council.php?action=count')
    .then(res => res.json())
    .then(data => document.getElementById('totalCouncilCount').textContent = data.total);
}

// --- Polls ---
function loadPolls() {
  let tbody = document.getElementById('pollsBody');
  tbody.innerHTML = '<tr><td colspan="4" class="py-4 text-center">Loading…</td></tr>';
  fetch('poll.php?action=list')
    .then(res => res.json())
    .then(data => {
      if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="4" class="py-4 text-center text-gray-500">No polls.</td></tr>';
        return;
      }
      tbody.innerHTML = data.map(poll => `
        <tr>
          <td>${poll.id}</td>
          <td>${poll.title}</td>
          <td>${poll.start_date} – ${poll.end_date}</td>
          <td>
            <button onclick="deletePoll(${poll.id})" class="delete-btn"><i class="fas fa-trash-alt"></i> Delete</button>
          </td>
        </tr>
      `).join('');
    });
}
function deletePoll(id) {
  if(!confirm('Delete this poll and all its candidates?')) return;
  fetch('poll.php?action=delete', {
    method: 'POST',
    body: new URLSearchParams({ id })
  })
  .then(res => res.json())
  .then(() => {
    loadPolls(); updateStatCounts(); loadPollDropdown();
  });
}
document.getElementById('createPollForm').addEventListener('submit', function(e) {
  e.preventDefault();
  let formData = new FormData(this);
  fetch('poll.php?action=create', { method:'POST', body:formData })
    .then(res=>res.json()).then(()=>{
      this.reset(); loadPolls(); updateStatCounts(); loadPollDropdown();
    });
});

// --- Participants ---
let currentPollId = "";
function loadPollDropdown() {
  fetch('poll.php?action=list')
    .then(res=>res.json())
    .then(data => {
      let sel = document.getElementById('pollSelect');
      sel.innerHTML = "";
      if (!data.length) { sel.innerHTML = '<option value="">No polls</option>'; sel.disabled=true; currentPollId=""; loadParticipants(); return; }
      data.forEach(poll => sel.innerHTML += `<option value="${poll.id}">${poll.title} (${poll.start_date} – ${poll.end_date})</option>`);
      sel.disabled=false;
      if (!currentPollId) currentPollId = sel.value;
      sel.value = currentPollId;
      loadParticipants();
    });
}
function loadParticipants() {
  if(!currentPollId) { document.getElementById('participantsList').innerHTML = '<p class="text-gray-500">Select a poll…</p>'; return; }
  let box = document.getElementById('participantsList');
  box.innerHTML = '<p>Loading…</p>';
  fetch('participants.php?action=list&poll_id='+encodeURIComponent(currentPollId))
    .then(res=>res.json())
    .then(data => {
      if (!data.length) { box.innerHTML = '<p class="text-gray-500">No participants.</p>'; return;}
      let html = data.map(part=>`
        <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #f2f2f2;padding:8px 0;">
          <div>
            <span class="font-semibold">${part.name}</span> <span class="text-sm text-gray-500 ml-1">${part.email}</span>
          </div>
          <button onclick="deleteParticipant(${part.id})" class="delete-btn"><i class="fas fa-trash-alt"></i> Remove</button>
        </div>
      `).join(''); box.innerHTML = html;
    });
}
document.getElementById('pollSelect').addEventListener('change', function() {
  currentPollId = this.value; loadParticipants();
});
document.getElementById('addParticipantForm').addEventListener('submit', function(e) {
  e.preventDefault();
  if (!currentPollId) { alert("Please select a poll"); return; }
  let formData = new FormData(this);
  formData.set('poll_id', currentPollId);
  fetch('participants.php?action=add', { method:'POST', body: formData })
    .then(res=>res.json())
    .then(data=>{
      if (data.status==='success') {
        this.reset(); document.getElementById('pollSelect').value=currentPollId;
        loadParticipants(); updateStatCounts();
      } else alert("Error: "+data.message);
    });
});
function deleteParticipant(id) {
  if(!confirm("Remove participant and their votes?")) return;
  fetch('participants.php?action=remove', {
    method: 'POST',
    body: new URLSearchParams({ id })
  }).then(res=>res.json()).then(()=>{loadParticipants();updateStatCounts();});
}

document.getElementById('showCouncilFormBtn').onclick = function(){ document.getElementById('councilForm').classList.remove('hidden'); };
document.getElementById('closeCouncilFormBtn').onclick = function(){ document.getElementById('councilForm').classList.add('hidden'); };
document.getElementById('addCouncilForm').addEventListener('submit', function(e){
  e.preventDefault();
  let formData = new FormData(this);
  fetch('council.php?action=add',{method: 'POST', body:formData})
    .then(res=>res.json())
    .then(data=>{
      if (data.status==='success') {
        this.reset();
        document.getElementById('councilForm').classList.add('hidden');
        loadCouncilMembers(); updateStatCounts();
      } else alert("Error: "+data.message);
    });
});
function deleteCouncil(id) {
  if(!confirm("Remove this council member?")) return;
  fetch('council.php?action=delete', {
    method:'POST', body: new URLSearchParams({ id })
  }).then(res=>res.json()).then(()=>{loadCouncilMembers();updateStatCounts();});
}

// --- On page load ---
document.addEventListener('DOMContentLoaded', function(){
  loadPolls();
  loadPollDropdown();
  loadCouncilMembers();
  updateStatCounts();
});
</script>
</body>
</html>