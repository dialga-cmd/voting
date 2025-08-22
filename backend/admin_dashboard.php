<?php
require_once __DIR__ . '/config.php';

// Get initial counts for dashboard
try {
    $totalPolls = $conn->query("SELECT COUNT(*) FROM polls")->fetchColumn();
    $totalParticipants = $conn->query("SELECT COUNT(*) FROM participants")->fetchColumn();
    $totalCouncil = $conn->query("SELECT COUNT(*) FROM student_council")->fetchColumn();
    $totalVotes = $conn->query("SELECT COUNT(*) FROM votes")->fetchColumn();
} catch (Exception $e) {
    $totalPolls = 0;
    $totalParticipants = 0;
    $totalCouncil = 0;
    $totalVotes = 0;
}
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
      transition: background 0.2s, color 0.2s; cursor: pointer;
    }
    .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.14); color: #fff; }
    .logout-btn {
      background: linear-gradient(90deg,#eb4e4e 0%,#c52234 100%); color: #fff;
      padding:12px 0; border:none; width: 100%; border-radius: 13px; font-weight: 600;
      font-size: 1em; margin-top: auto; cursor: pointer;
    }
    .logout-btn:hover { opacity: 0.9; }
    main { margin-left: 280px; padding: 40px 46px 40px 36px; min-height: 100vh; }
    h1, h2 { margin-top: 0; }
    .stat-row { display:flex;gap:32px;flex-wrap:wrap; }
    .stat-card {
      background: linear-gradient(135deg, rgba(255,255,255,0.93) 0%, rgba(255,255,255,0.78) 100%);
      backdrop-filter: blur(10px); border-radius: 20px; border: 1px solid #dde3ee;
      padding: 26px 22px; margin-bottom: 30px; box-shadow: 0 14px 32px -10px #bbc6e3;
      display: flex; align-items: center; justify-content: space-between;
      transition: box-shadow 0.25s, transform 0.25s; flex: 1; min-width: 200px;
    }
    .stat-card:hover { box-shadow: 0 18px 44px -10px #657ac8; transform: translateY(-3px);}
    .panel {
      background: rgba(255,255,255,0.96); border-radius: 18px; box-shadow: 0 8px 24px -8px #ced5e9;
      margin-bottom: 46px; padding: 32px 25px 24px 23px;
    }
    .panel-header { font-weight: bold; color: #444; margin-bottom: 22px; font-size: 1.28rem; }
    /* Table & Form */
    table { width:100%; border-collapse:collapse; }
    th, td { padding: 14px 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
    th { background: #f4f5fa; font-weight: 600; }
    tr:nth-child(even) td { background: #f8fafd; }
    input, select, button, textarea {
      font-size: 1em; border: 1px solid #d6dae5; border-radius: 8px;
      padding: 10px 13px; background: #fff; outline: none;
    }
    textarea { resize: vertical; min-height: 80px; }
    .form-row { display: flex; gap: 15px; flex-wrap: wrap; align-items: center; margin-bottom: 18px; }
    .form-row input, .form-row select { flex: 1; min-width: 200px; }
    .action-btn {
      color: #fff; background: linear-gradient(90deg,#6366f1 0%,#8b5cf6 100%);
      border: none; padding: 10px 24px; border-radius: 8px; font-weight: 600;
      transition: background 0.2s; cursor: pointer;
    }
    .action-btn:hover { background: #6d28d9; }
    .delete-btn {
      background: #fae1e1; color: #e34d4d; border: none; padding: 7px 17px;
      border-radius: 8px; font-size: 1em; font-weight: 500; cursor: pointer;
    }
    .delete-btn:hover { background: #e95050; color: #fff; }
    .edit-btn {
      background: #e0f2fe; color: #0277bd; border: none; padding: 7px 17px;
      border-radius: 8px; font-size: 1em; font-weight: 500; cursor: pointer; margin-right: 5px;
    }
    .edit-btn:hover { background: #0277bd; color: #fff; }
    .hidden { display: none !important; }
    .loading { opacity: 0.5; }
    .error-message { color: #e34d4d; background: #fae1e1; padding: 10px; border-radius: 8px; margin: 10px 0; }
    .success-message { color: #059669; background: #d1fae5; padding: 10px; border-radius: 8px; margin: 10px 0; }
    .info-message { color: #1e40af; background: #dbeafe; padding: 10px; border-radius: 8px; margin: 10px 0; }
    /* Animations */
    @keyframes fadeIn { from { opacity: 0;} to{ opacity: 1;} }
    .animate-fade-in { animation: fadeIn 0.6s; }
    .section { display: none; }
    .section.active { display: block; }
    
    /* Responsive */
    @media (max-width: 768px) {
      .sidebar-gradient { width: 100%; height: auto; position: relative; }
      main { margin-left: 0; padding: 20px; }
      .stat-row { flex-direction: column; }
      .form-row { flex-direction: column; }
      .form-row input, .form-row select { min-width: 100%; }
    }
    
    /* Charts and Analytics */
    .chart-container { background: #fff; padding: 20px; border-radius: 12px; margin: 20px 0; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .metric-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 12px; margin: 10px 0; }
    .activity-item { display: flex; align-items: center; padding: 12px; border-bottom: 1px solid #f0f0f0; }
    .activity-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 12px; }
    .activity-icon.poll { background: #e3f2fd; color: #1976d2; }
    .activity-icon.user { background: #e8f5e8; color: #388e3c; }
    .activity-icon.vote { background: #fff3e0; color: #f57c00; }
  </style>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar-gradient">
  <div class="sidebar-header"><i class="fas fa-user-shield"></i> Admin Panel</div>
  <div class="sidebar-desc">VoteEasy Dashboard</div>
  <a href="#" class="nav-link active" data-section="dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
  <a href="#" class="nav-link" data-section="polls"><i class="fas fa-poll"></i> Manage Polls</a>
  <a href="#" class="nav-link" data-section="participants"><i class="fas fa-users"></i> Participants</a>
  <a href="#" class="nav-link" data-section="council"><i class="fas fa-user-tie"></i> Council</a>
  <a href="#" class="nav-link" data-section="results"><i class="fas fa-chart-bar"></i> Results</a>
  <a href="#" class="nav-link" data-section="analytics"><i class="fas fa-analytics"></i> Analytics</a>
  <form action="../index.html" method="get" style="margin-top:32px">
    <button class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
  </form>
</aside>

<main>
  <!-- Dashboard Stats -->
  <section id="dashboard" class="section active">
    <h1 style="font-size:2em;font-weight:bold;">Welcome back, Admin!</h1>
    <p class="text-gray-600" style="margin-bottom:18px;">See the live stats for your voting system.</p>
    <div class="stat-row">
      <div class="stat-card">
        <div>
          <div style="font-size:1.7em;font-weight:700;" id="totalPollsCount"><?php echo $totalPolls; ?></div>
          <div class="text-gray-600">Active Polls</div>
        </div>
        <i class="fas fa-poll" style="font-size:2.1em;color:#6366f1;"></i>
      </div>
      <div class="stat-card">
        <div>
          <div style="font-size:1.7em;font-weight:700;" id="totalParticipantsCount"><?php echo $totalParticipants; ?></div>
          <div class="text-gray-600">Participants</div>
        </div>
        <i class="fas fa-users" style="font-size:2.1em;color:#10b981;"></i>
      </div>
      <div class="stat-card">
        <div>
          <div style="font-size:1.7em;font-weight:700;" id="totalCouncilCount"><?php echo $totalCouncil; ?></div>
          <div class="text-gray-600">Council Members</div>
        </div>
        <i class="fas fa-user-tie" style="font-size:2.1em;color:#06b6d4;"></i>
      </div>
      <div class="stat-card">
        <div>
          <div style="font-size:1.7em;font-weight:700;" id="totalVotesCount"><?php echo $totalVotes; ?></div>
          <div class="text-gray-600">Total Votes</div>
        </div>
        <i class="fas fa-vote-yea" style="font-size:2.1em;color:#f59e0b;"></i>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="panel">
      <div class="panel-header"><i class="fas fa-clock"></i> Recent Activity</div>
      <div id="recent-activity">
        <div class="activity-item">
          <div class="activity-icon poll"><i class="fas fa-poll"></i></div>
          <div>
            <div style="font-weight: 600;">System initialized</div>
            <div style="font-size: 0.9em; color: #666;">Welcome to VoteEasy Admin Dashboard</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="panel">
      <div class="panel-header"><i class="fas fa-bolt"></i> Quick Actions</div>
      <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <button class="action-btn" onclick="showSection('polls')">
          <i class="fas fa-plus"></i> Create New Poll
        </button>
        <button class="action-btn" onclick="showSection('participants')">
          <i class="fas fa-user-plus"></i> Add Participants
        </button>
        <button class="action-btn" onclick="showSection('results')">
          <i class="fas fa-chart-line"></i> View Results
        </button>
        <button class="action-btn" onclick="exportData()">
          <i class="fas fa-download"></i> Export Data
        </button>
      </div>
    </div>
  </section>

  <!-- Polls Management Section -->
  <section id="polls" class="section">
    <div class="panel animate-fade-in">
      <div class="panel-header"><i class="fas fa-poll"></i> Manage Polls</div>
      <div id="polls-messages"></div>
      <form id="createPollForm">
        <div class="form-row">
          <input type="text" name="title" placeholder="Poll Title" required>
          <input type="date" name="start_date" required>
          <input type="date" name="end_date" required>
          <button type="submit" class="action-btn">Create Poll</button>
        </div>
      </form>
      <div style="overflow-x:auto;">
        <table>
          <thead>
            <tr>
              <th>ID</th><th>Title</th><th>Duration</th><th>Participants</th><th>Votes</th><th>Actions</th>
            </tr>
          </thead>
          <tbody id="pollsBody">
            <tr><td colspan="6" style="text-align: center; padding: 20px;">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- Participants Section -->
  <section id="participants" class="section">
    <div class="panel animate-fade-in">
      <div class="panel-header"><i class="fas fa-users"></i> Manage Participants</div>
      <div id="participants-messages"></div>
      <form id="addParticipantForm">
        <div class="form-row">
          <select name="poll_id" required id="pollSelect">
            <option value="">Select Poll...</option>
          </select>
          <input type="text" name="name" required placeholder="Full Name">
          <input type="email" name="email" placeholder="Email (optional)">
          <button type="submit" class="action-btn">Add Participant</button>
        </div>
      </form>
      <div id="participantsList">
        <p style="text-align: center; color: #666; padding: 20px;">Select a poll to view participants...</p>
      </div>
    </div>
  </section>

  <!-- Council Section -->
  <section id="council" class="section">
    <div class="panel animate-fade-in">
      <div class="panel-header"><i class="fas fa-user-tie"></i> Student Council Management</div>
      <div id="council-messages"></div>
      <form id="addCouncilForm">
        <div class="form-row">
          <input type="text" name="name" required placeholder="Council Member Name">
          <input type="text" name="position" required placeholder="Position (e.g., President, Secretary)">
          <button type="submit" class="action-btn">Add Member</button>
        </div>
      </form>
      <div style="overflow-x:auto;">
        <table>
          <thead>
            <tr>
              <th>ID</th><th>Name</th><th>Position</th><th>Actions</th>
            </tr>
          </thead>
          <tbody id="councilBody">
            <tr><td colspan="4" style="text-align: center; padding: 20px;">Loading...</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- Results Section -->
  <section id="results" class="section">
    <div class="panel animate-fade-in">
      <div class="panel-header"><i class="fas fa-chart-bar"></i> Voting Results</div>
      <div id="results-messages"></div>
      
      <div class="form-row">
        <select id="resultsPollSelect" onchange="loadPollResults()">
          <option value="">Select Poll to View Results...</option>
        </select>
        <button onclick="refreshResults()" class="action-btn">
          <i class="fas fa-sync-alt"></i> Refresh
        </button>
      </div>

      <div id="results-content">
        <div style="text-align: center; color: #666; padding: 40px;">
          <i class="fas fa-chart-pie" style="font-size: 3em; margin-bottom: 20px; opacity: 0.3;"></i>
          <p>Select a poll to view detailed results</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Analytics Section -->
  <section id="analytics" class="section">
    <div class="panel animate-fade-in">
      <div class="panel-header"><i class="fas fa-analytics"></i> System Analytics</div>
      
      <div class="stat-row">
        <div class="metric-card">
          <h3 style="margin: 0 0 10px 0;">Participation Rate</h3>
          <div style="font-size: 2em; font-weight: bold;" id="participation-rate">--%</div>
          <div style="opacity: 0.8;">Average across all polls</div>
        </div>
        <div class="metric-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
          <h3 style="margin: 0 0 10px 0;">Active Polls</h3>
          <div style="font-size: 2em; font-weight: bold;" id="active-polls-count">0</div>
          <div style="opacity: 0.8;">Currently running</div>
        </div>
      </div>

      <div class="chart-container">
        <h3>Voting Activity Over Time</h3>
        <div id="activity-chart" style="height: 300px; display: flex; align-items: center; justify-content: center; color: #666;">
          <div style="text-align: center;">
            <i class="fas fa-chart-line" style="font-size: 3em; opacity: 0.3; margin-bottom: 20px;"></i>
            <p>Chart will be displayed here</p>
            <small>Integration with Chart.js coming soon</small>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>

<script>
// Global variables
let currentPollId = "";

// Initialize
document.addEventListener('DOMContentLoaded', function() {
  setupNavigation();
  updateStatCounts();
  loadPolls();
  loadPollDropdown();
  loadCouncilMembers();
  loadRecentActivity();
});

// Navigation setup
function setupNavigation() {
  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const sectionId = this.getAttribute('data-section');
      showSection(sectionId);
    });
  });
}

function showSection(sectionId) {
  // Update active nav
  document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
  document.querySelector(`[data-section="${sectionId}"]`).classList.add('active');
  
  // Show section
  document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
  document.getElementById(sectionId).classList.add('active');
  
  // Load section data
  if (sectionId === 'polls') loadPolls();
  if (sectionId === 'participants') loadPollDropdown();
  if (sectionId === 'council') loadCouncilMembers();
  if (sectionId === 'results') loadResultsPolls();
  if (sectionId === 'analytics') loadAnalytics();
}

// Utility functions
function showMessage(containerId, message, type = 'error') {
  const container = document.getElementById(containerId);
  container.innerHTML = `<div class="${type}-message">${message}</div>`;
  setTimeout(() => container.innerHTML = '', 5000);
}

async function apiCall(url, options = {}) {
  try {
    const response = await fetch(url, options);
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return await response.json();
  } catch (error) {
    console.error('API call failed:', error);
    throw error;
  }
}

// Dashboard stats
async function updateStatCounts() {
  try {
    const [polls, participants, council] = await Promise.all([
      apiCall('poll.php?action=count'),
      apiCall('participants.php?action=count'),
      apiCall('council.php?action=count')
    ]);
    
    document.getElementById('totalPollsCount').textContent = polls.total || 0;
    document.getElementById('totalParticipantsCount').textContent = participants.total || 0;
    document.getElementById('totalCouncilCount').textContent = council.total || 0;
    
    // Update votes count
    try {
      const votes = await apiCall('votes.php?action=count');
      document.getElementById('totalVotesCount').textContent = votes.total || 0;
    } catch (e) {
      // If votes endpoint doesn't exist, calculate from database
      document.getElementById('totalVotesCount').textContent = '0';
    }
  } catch (error) {
    console.error('Failed to update stats:', error);
  }
}

// Recent activity
async function loadRecentActivity() {
  const container = document.getElementById('recent-activity');
  try {
    // This would typically load from a recent_activity API endpoint
    // For now, we'll show static content
    container.innerHTML = `
      <div class="activity-item">
        <div class="activity-icon poll"><i class="fas fa-poll"></i></div>
        <div>
          <div style="font-weight: 600;">System ready</div>
          <div style="font-size: 0.9em; color: #666;">All systems operational</div>
        </div>
      </div>
    `;
  } catch (error) {
    container.innerHTML = '<p style="color: #666; text-align: center;">No recent activity</p>';
  }
}

// Polls Management
async function loadPolls() {
  const tbody = document.getElementById('pollsBody');
  tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Loading…</td></tr>';
  
  try {
    const polls = await apiCall('poll.php?action=list');
    
    if (!polls.length) {
      tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: #666;">No polls found. Create your first poll above!</td></tr>';
      return;
    }
    
    // For each poll, get participant and vote counts
    const pollsWithCounts = await Promise.all(polls.map(async poll => {
      try {
        const participants = await apiCall(`participants.php?action=list&poll_id=${poll.id}`);
        const participantCount = participants.length;
        // You could also get vote count here if you have a votes API
        return { ...poll, participantCount, voteCount: 0 };
      } catch (e) {
        return { ...poll, participantCount: 0, voteCount: 0 };
      }
    }));
    
    tbody.innerHTML = pollsWithCounts.map(poll => `
      <tr>
        <td>${poll.id}</td>
        <td><strong>${poll.title}</strong></td>
        <td>${poll.start_date} – ${poll.end_date}</td>
        <td>${poll.participantCount}</td>
        <td>${poll.voteCount}</td>
        <td>
          <button onclick="editPoll(${poll.id})" class="edit-btn" title="Edit Poll">
            <i class="fas fa-edit"></i>
          </button>
          <button onclick="deletePoll(${poll.id})" class="delete-btn" title="Delete Poll">
            <i class="fas fa-trash-alt"></i>
          </button>
        </td>
      </tr>
    `).join('');
  } catch (error) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px; color: #e34d4d;">Failed to load polls</td></tr>';
    showMessage('polls-messages', 'Failed to load polls: ' + error.message);
  }
}

async function deletePoll(id) {
  if (!confirm('Delete this poll and all its candidates? This action cannot be undone!')) return;
  
  try {
    const result = await apiCall('poll.php?action=delete', {
      method: 'POST',
      body: new URLSearchParams({ id })
    });
    
    if (result.status === 'success') {
      showMessage('polls-messages', 'Poll deleted successfully', 'success');
      loadPolls();
      updateStatCounts();
      loadPollDropdown();
      loadResultsPolls();
    } else {
      showMessage('polls-messages', result.message);
    }
  } catch (error) {
    showMessage('polls-messages', 'Failed to delete poll: ' + error.message);
  }
}

function editPoll(id) {
  // Future enhancement: open edit modal
  alert('Edit functionality coming soon!');
}

document.getElementById('createPollForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  
  try {
    const result = await apiCall('poll.php?action=create', {
      method: 'POST',
      body: formData
    });
    
    if (result.status === 'success') {
      showMessage('polls-messages', 'Poll created successfully', 'success');
      this.reset();
      loadPolls();
      updateStatCounts();
      loadPollDropdown();
      loadResultsPolls();
    } else {
      showMessage('polls-messages', result.message);
    }
  } catch (error) {
    showMessage('polls-messages', 'Failed to create poll: ' + error.message);
  }
});

// Participants Management
async function loadPollDropdown() {
  try {
    const polls = await apiCall('poll.php?action=list');
    const sel = document.getElementById('pollSelect');
    const resultsSel = document.getElementById('resultsPollSelect');
    
    sel.innerHTML = '<option value="">Select Poll...</option>';
    if (resultsSel) resultsSel.innerHTML = '<option value="">Select Poll to View Results...</option>';
    
    if (!polls.length) {
      sel.innerHTML = '<option value="">No polls available</option>';
      sel.disabled = true;
      if (resultsSel) resultsSel.disabled = true;
      currentPollId = "";
      loadParticipants();
      return;
    }
    
    polls.forEach(poll => {
      const option = `<option value="${poll.id}">${poll.title} (${poll.start_date} – ${poll.end_date})</option>`;
      sel.innerHTML += option;
      if (resultsSel) resultsSel.innerHTML += option;
    });
    
    sel.disabled = false;
    if (resultsSel) resultsSel.disabled = false;
    
    if (!currentPollId && polls.length > 0) {
      currentPollId = polls[0].id;
      sel.value = currentPollId;
    } else {
      sel.value = currentPollId;
    }
    loadParticipants();
  } catch (error) {
    showMessage('participants-messages', 'Failed to load polls: ' + error.message);
  }
}

async function loadParticipants() {
  const box = document.getElementById('participantsList');
  
  if (!currentPollId) {
    box.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">Select a poll to view participants...</p>';
    return;
  }
  
  box.innerHTML = '<p style="text-align: center; padding: 20px;">Loading participants...</p>';
  
  try {
    const participants = await apiCall(`participants.php?action=list&poll_id=${currentPollId}`);
    
    if (!participants.length) {
      box.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">No participants in this poll yet. Add some above!</p>';
      return;
    }
    
    const html = `
      <div style="overflow-x: auto;">
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            ${participants.map(part => `
              <tr>
                <td><strong>${part.name}</strong></td>
                <td>${part.email || '<em>No email</em>'}</td>
                <td>
                  <button onclick="editParticipant(${part.id})" class="edit-btn" title="Edit Participant">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button onclick="deleteParticipant(${part.id})" class="delete-btn" title="Remove Participant">
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      </div>
    `;
    
    box.innerHTML = html;
  } catch (error) {
    box.innerHTML = '<p style="text-align: center; color: #e34d4d; padding: 20px;">Failed to load participants</p>';
    showMessage('participants-messages', 'Failed to load participants: ' + error.message);
  }
}

function editParticipant(id) {
  // Future enhancement: open edit modal
  alert('Edit functionality coming soon!');
}

document.getElementById('pollSelect').addEventListener('change', function() {
  currentPollId = this.value;
  loadParticipants();
});

document.getElementById('addParticipantForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  
  if (!currentPollId) {
    showMessage('participants-messages', 'Please select a poll first');
    return;
  }
  
  const formData = new FormData(this);
  formData.set('poll_id', currentPollId);
  
  try {
    const result = await apiCall('participants.php?action=add', {
      method: 'POST',
      body: formData
    });
    
    if (result.status === 'success') {
      showMessage('participants-messages', 'Participant added successfully', 'success');
      this.reset();
      document.getElementById('pollSelect').value = currentPollId;
      loadParticipants();
      updateStatCounts();
    } else {
      showMessage('participants-messages', result.message);
    }
  } catch (error) {
    showMessage('participants-messages', 'Failed to add participant: ' + error.message);
  }
});

async function deleteParticipant(id) {
  if (!confirm("Remove participant and their votes? This action cannot be undone!")) return;
  
  try {
    const result = await apiCall('participants.php?action=remove', {
      method: 'POST',
      body: new URLSearchParams({ id })
    });
    
    if (result.status === 'success') {
      showMessage('participants-messages', 'Participant removed successfully', 'success');
      loadParticipants();
      updateStatCounts();
    } else {
      showMessage('participants-messages', result.message);
    }
  } catch (error) {
    showMessage('participants-messages', 'Failed to remove participant: ' + error.message);
  }
}

// Council Management
async function loadCouncilMembers() {
  const tbody = document.getElementById('councilBody');
  tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px;">Loading…</td></tr>';
  
  try {
    const council = await apiCall('council.php?action=list');
    
    if (!council.length) {
      tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px; color: #666;">No council members found. Add some above!</td></tr>';
      return;
    }
    
    tbody.innerHTML = council.map(member => `
      <tr>
        <td>${member.id}</td>
        <td><strong>${member.name}</strong></td>
        <td>${member.position}</td>
        <td>
          <button onclick="editCouncilMember(${member.id})" class="edit-btn" title="Edit Member">
            <i class="fas fa-edit"></i>
          </button>
          <button onclick="deleteCouncilMember(${member.id})" class="delete-btn" title="Delete Member">
            <i class="fas fa-trash-alt"></i>
          </button>
        </td>
      </tr>
    `).join('');
  } catch (error) {
    tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px; color: #e34d4d;">Failed to load council members</td></tr>';
    showMessage('council-messages', 'Failed to load council members: ' + error.message);
  }
}

function editCouncilMember(id) {
  // Future enhancement: open edit modal
  alert('Edit functionality coming soon!');
}

document.getElementById('addCouncilForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData(this);
  
  try {
    const result = await apiCall('council.php?action=add', {
      method: 'POST',
      body: formData
    });
    
    if (result.status === 'success') {
      showMessage('council-messages', 'Council member added successfully', 'success');
      this.reset();
      loadCouncilMembers();
      updateStatCounts();
    } else {
      showMessage('council-messages', result.message);
    }
  } catch (error) {
    showMessage('council-messages', 'Failed to add council member: ' + error.message);
  }
});

async function deleteCouncilMember(id) {
  if (!confirm("Remove this council member? This action cannot be undone!")) return;
  
  try {
    const result = await apiCall('council.php?action=delete', {
      method: 'POST',
      body: new URLSearchParams({ id })
    });
    
    if (result.status === 'success') {
      showMessage('council-messages', 'Council member deleted successfully', 'success');
      loadCouncilMembers();
      updateStatCounts();
    } else {
      showMessage('council-messages', result.message);
    }
  } catch (error) {
    showMessage('council-messages', 'Failed to delete council member: ' + error.message);
  }
}

// Results Management
async function loadResultsPolls() {
  try {
    const polls = await apiCall('poll.php?action=list');
    const sel = document.getElementById('resultsPollSelect');
    
    sel.innerHTML = '<option value="">Select Poll to View Results...</option>';
    
    if (!polls.length) {
      sel.innerHTML = '<option value="">No polls available</option>';
      sel.disabled = true;
      return;
    }
    
    polls.forEach(poll => {
      sel.innerHTML += `<option value="${poll.id}">${poll.title} (${poll.start_date} – ${poll.end_date})</option>`;
    });
    
    sel.disabled = false;
  } catch (error) {
    showMessage('results-messages', 'Failed to load polls: ' + error.message);
  }
}

async function loadPollResults() {
  const pollId = document.getElementById('resultsPollSelect').value;
  const container = document.getElementById('results-content');
  
  if (!pollId) {
    container.innerHTML = `
      <div style="text-align: center; color: #666; padding: 40px;">
        <i class="fas fa-chart-pie" style="font-size: 3em; margin-bottom: 20px; opacity: 0.3;"></i>
        <p>Select a poll to view detailed results</p>
      </div>
    `;
    return;
  }
  
  container.innerHTML = '<div style="text-align: center; padding: 40px;">Loading results...</div>';
  
  try {
    // Load participants for this poll
    const participants = await apiCall(`participants.php?action=list&poll_id=${pollId}`);
    
    if (!participants.length) {
      container.innerHTML = `
        <div style="text-align: center; color: #666; padding: 40px;">
          <i class="fas fa-users-slash" style="font-size: 3em; margin-bottom: 20px; opacity: 0.3;"></i>
          <p>No participants in this poll yet</p>
        </div>
      `;
      return;
    }
    
    // For now, show participants (in a real system, you'd show vote counts)
    const resultsHtml = `
      <div class="chart-container">
        <h3>Poll Results</h3>
        <div style="overflow-x: auto;">
          <table>
            <thead>
              <tr>
                <th>Candidate</th>
                <th>Votes</th>
                <th>Percentage</th>
                <th>Progress</th>
              </tr>
            </thead>
            <tbody>
              ${participants.map((participant, index) => {
                // Simulated vote count (in real system, get from votes table)
                const voteCount = Math.floor(Math.random() * 50);
                const totalVotes = participants.length * 25; // Simulated total
                const percentage = totalVotes > 0 ? Math.round((voteCount / totalVotes) * 100) : 0;
                
                return `
                  <tr>
                    <td><strong>${participant.name}</strong></td>
                    <td>${voteCount}</td>
                    <td>${percentage}%</td>
                    <td>
                      <div style="background: #f0f0f0; border-radius: 10px; height: 20px; width: 200px; position: relative;">
                        <div style="background: linear-gradient(90deg, #6366f1, #8b5cf6); height: 100%; width: ${percentage}%; border-radius: 10px; transition: width 0.3s ease;"></div>
                      </div>
                    </td>
                  </tr>
                `;
              }).join('')}
            </tbody>
          </table>
        </div>
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
          <small style="color: #666;">
            <i class="fas fa-info-circle"></i>
            Note: This is demo data. In a production system, these would be real vote counts from your database.
          </small>
        </div>
      </div>
    `;
    
    container.innerHTML = resultsHtml;
  } catch (error) {
    container.innerHTML = `
      <div style="text-align: center; color: #e34d4d; padding: 40px;">
        <i class="fas fa-exclamation-triangle" style="font-size: 3em; margin-bottom: 20px;"></i>
        <p>Failed to load results: ${error.message}</p>
      </div>
    `;
  }
}

function refreshResults() {
  loadPollResults();
}

// Analytics
async function loadAnalytics() {
  try {
    // Load participation rate
    const polls = await apiCall('poll.php?action=list');
    const participants = await apiCall('participants.php?action=count');
    
    // Simulated participation rate calculation
    const participationRate = polls.length > 0 ? Math.round((participants.total / (polls.length * 10)) * 100) : 0;
    document.getElementById('participation-rate').textContent = Math.min(participationRate, 100) + '%';
    
    // Active polls count
    document.getElementById('active-polls-count').textContent = polls.length;
    
  } catch (error) {
    console.error('Failed to load analytics:', error);
    document.getElementById('participation-rate').textContent = 'N/A';
    document.getElementById('active-polls-count').textContent = '0';
  }
}

// Export functionality
async function exportData() {
  try {
    const [polls, participants, council] = await Promise.all([
      apiCall('poll.php?action=list'),
      apiCall('participants.php?action=list'),
      apiCall('council.php?action=list')
    ]);
    
    const exportData = {
      polls,
      participants,
      council,
      exportDate: new Date().toISOString(),
      systemInfo: {
        totalPolls: polls.length,
        totalParticipants: participants.length,
        totalCouncil: council.length
      }
    };
    
    const dataStr = JSON.stringify(exportData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    
    const link = document.createElement('a');
    link.href = url;
    link.download = `voteeasy-export-${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    alert('Data exported successfully!');
  } catch (error) {
    alert('Failed to export data: ' + error.message);
  }
}

// Auto-refresh stats every 30 seconds
setInterval(updateStatCounts, 30000);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
  if (e.ctrlKey || e.metaKey) {
    switch(e.key) {
      case '1':
        e.preventDefault();
        showSection('dashboard');
        break;
      case '2':
        e.preventDefault();
        showSection('polls');
        break;
      case '3':
        e.preventDefault();
        showSection('participants');
        break;
      case '4':
        e.preventDefault();
        showSection('council');
        break;
      case 'r':
        e.preventDefault();
        location.reload();
        break;
    }
  }
});

// Show keyboard shortcuts help
function showKeyboardShortcuts() {
  alert(`Keyboard Shortcuts:
  
Ctrl/Cmd + 1: Dashboard
Ctrl/Cmd + 2: Polls
Ctrl/Cmd + 3: Participants
Ctrl/Cmd + 4: Council
Ctrl/Cmd + R: Refresh Page

Click anywhere to close this help.`);
}

// Add help button functionality (you can add this to the sidebar if needed)
console.log('VoteEasy Admin Dashboard loaded successfully!');
console.log('Use Ctrl/Cmd + 1-4 for quick navigation');
</script>

<!-- Help Modal (Optional) -->
<div id="help-modal" class="hidden" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center;">
  <div style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;">
    <h3 style="margin-top: 0;">Admin Dashboard Help</h3>
    <div style="margin: 20px 0;">
      <h4>Quick Navigation:</h4>
      <ul style="margin: 10px 0; padding-left: 20px;">
        <li>Ctrl/Cmd + 1: Dashboard Overview</li>
        <li>Ctrl/Cmd + 2: Manage Polls</li>
        <li>Ctrl/Cmd + 3: Manage Participants</li>
        <li>Ctrl/Cmd + 4: Council Management</li>
      </ul>
    </div>
    <div style="margin: 20px 0;">
      <h4>Features:</h4>
      <ul style="margin: 10px 0; padding-left: 20px;">
        <li>Create and manage voting polls</li>
        <li>Add participants to polls</li>
        <li>View real-time voting results</li>
        <li>Export system data</li>
        <li>Monitor system analytics</li>
      </ul>
    </div>
    <button onclick="document.getElementById('help-modal').classList.add('hidden')" class="action-btn">
      Got it!
    </button>
  </div>
</div>

</body>
</html>