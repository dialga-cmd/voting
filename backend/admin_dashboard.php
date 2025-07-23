<?php
require_once __DIR__ . '/config.php';

// Get real-time stats
$totalPolls = $conn->query("SELECT COUNT(*) FROM polls")->fetchColumn();
$totalParticipants = $conn->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$totalCouncil = $conn->query("SELECT COUNT(*) FROM student_council")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VoteEasy</title>
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

<!-- Background Pattern -->
<div class="fixed inset-0 z-0">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-indigo-100/50"></div>
    <div class="absolute inset-0" style="background-image: radial-gradient(circle at 25% 25%, rgba(99, 102, 241, 0.1) 0%, transparent 50%), radial-gradient(circle at 75% 75%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);"></div>
</div>

<!-- Sidebar -->
<aside class="fixed left-0 top-0 h-screen w-72 sidebar-gradient shadow-2xl z-50 transform transition-transform duration-300">
    <!-- Header -->
    <div class="p-6 border-b border-slate-700/50">
        <div class="flex items-center space-x-3">
            <div class="relative">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-shield text-white text-xl"></i>
                </div>
                <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full pulse-ring"></div>
            </div>
            <div>
                <h1 class="text-xl font-bold text-white">Admin Panel</h1>
                <p class="text-slate-400 text-sm">VoteEasy Dashboard</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 p-4 space-y-2">
        <a href="#dashboard" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-all duration-200 group">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-tachometer-alt text-white"></i>
            </div>
            <span class="font-medium">Dashboard</span>
        </a>
        
        <a href="#polls" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-all duration-200 group">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-poll text-white"></i>
            </div>
            <span class="font-medium">Manage Polls</span>
        </a>
        
        <a href="#participants" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-all duration-200 group">
            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-users text-white"></i>
            </div>
            <span class="font-medium">Participants</span>
        </a>
        
        <a href="#council" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-all duration-200 group">
            <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-user-tie text-white"></i>
            </div>
            <span class="font-medium">Student Council</span>
        </a>
        
        <a href="#results" class="nav-link flex items-center space-x-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-white/10 transition-all duration-200 group">
            <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-bar text-white"></i>
            </div>
            <span class="font-medium">Results</span>
        </a>
    </nav>
    
    <!-- Logout Button -->
    <div class="p-4">
        <a href="../index.html" class="flex items-center justify-center space-x-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-3 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
            <i class="fas fa-sign-out-alt"></i>
            <span class="font-medium">Logout</span>
        </a>
    </div>
</aside>

<!-- Main Content -->
<main class="ml-72 p-8 relative z-10">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome back, Admin!</h1>
                <p class="text-gray-600">Here's what's happening with your voting system today.</p>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-500">Last updated</div>
                <div class="text-lg font-semibold text-gray-800" id="currentTime"></div>
            </div>
        </div>
    </div>

    <!-- Dashboard Overview -->
    <section id="dashboard" class="mb-12 animate-fade-in">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="stat-card rounded-2xl p-8 card-hover animate-slide-up shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-poll text-white text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-800"><?php echo $totalPolls; ?></div>
                        <div class="text-purple-600 font-semibold">Active Polls</div>
                    </div>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-trending-up text-green-500 mr-2"></i>
                    <span>Total polls created</span>
                </div>
            </div>
            
            <div class="stat-card rounded-2xl p-8 card-hover animate-slide-up shadow-xl" style="animation-delay: 0.1s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-users text-white text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-800"><?php echo $totalParticipants; ?></div>
                        <div class="text-green-600 font-semibold">Participants</div>
                    </div>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-user-plus text-blue-500 mr-2"></i>
                    <span>Registered users</span>
                </div>
            </div>
            
            <div class="stat-card rounded-2xl p-8 card-hover animate-slide-up shadow-xl" style="animation-delay: 0.2s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-user-tie text-white text-2xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-800"><?php echo $totalCouncil; ?></div>
                        <div class="text-cyan-600 font-semibold">Council Members</div>
                    </div>
                </div>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-crown text-yellow-500 mr-2"></i>
                    <span>Leadership team</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Polls Section -->
    <section id="polls" class="mb-12 animate-fade-in">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-poll mr-3 text-purple-600"></i>
                Manage Polls
            </h2>
        </div>
        
        <!-- Create Poll Card -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-xl mb-8 card-hover">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-plus-circle mr-2 text-purple-600"></i>
                Create New Poll
            </h3>
            <form id="createPollForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="relative">
                    <input type="text" name="title" placeholder="Poll Title" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" required>
                    <i class="fas fa-edit absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="relative">
                    <input type="date" name="start_date" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" required>
                </div>
                <div class="relative">
                    <input type="date" name="end_date" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200" required>
                </div>
                <button type="submit" class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg font-medium flex items-center justify-center">
                    <i class="fas fa-rocket mr-2"></i>
                    Create Poll
                </button>
            </form>
        </div>
        
        <!-- Polls Table -->
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-list mr-2 text-purple-600"></i>
                    Active Polls
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pollsBody" class="divide-y divide-gray-100">
                        <!-- Poll rows via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Participants Section -->
    <section id="participants" class="mb-12 animate-fade-in">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-users mr-3 text-green-600"></i>
                Manage Participants
            </h2>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-xl">
            <div class="text-center py-12">
                <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-600 text-lg">Participants management UI will be loaded here</p>
                <p class="text-gray-500 text-sm mt-2">Dynamic content loading...</p>
            </div>
        </div>
    </section>

    <!-- Council Section -->
    <section id="council" class="mb-12 animate-fade-in">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-user-tie mr-3 text-cyan-600"></i>
                Student Council
            </h2>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-xl">
            <button onclick="addCouncilDialog()" class="bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white px-6 py-3 rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg font-medium flex items-center mb-6">
                <i class="fas fa-user-plus mr-2"></i>
                Add Council Member
            </button>
            <div id="councilTable">
                <div class="text-center py-8">
                    <i class="fas fa-spinner fa-spin text-2xl text-gray-400 mb-2"></i>
                    <p class="text-gray-500">Loading council members...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Results Section -->
    <section id="results" class="animate-fade-in">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-chart-bar mr-3 text-orange-600"></i>
                Poll Results
            </h2>
        </div>
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-8 shadow-xl">
            <div id="resultsTable">
                <div class="text-center py-12">
                    <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                    <p class="text-gray-600 text-lg">Poll results and analytics will appear here</p>
                    <p class="text-gray-500 text-sm mt-2">Loading comprehensive data...</p>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
// Update current time
function updateTime() {
    const now = new Date();
    document.getElementById('currentTime').textContent = now.toLocaleTimeString();
}
updateTime();
setInterval(updateTime, 1000);

// Enhanced poll loading with better UI
function loadPolls() {
    const pollsBody = document.getElementById('pollsBody');
    pollsBody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center"><i class="fas fa-spinner fa-spin text-xl text-gray-400 mr-2"></i><span class="text-gray-500">Loading polls...</span></td></tr>';
    
    fetch('poll.php?action=list')
        .then(res => res.json())
        .then(data => {
            pollsBody.innerHTML = '';
            if (data.length === 0) {
                pollsBody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-gray-500"><i class="fas fa-inbox text-2xl mb-2 block"></i>No polls found. Create your first poll above!</td></tr>';
                return;
            }
            
            data.forEach((poll, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50 transition-colors duration-200';
                row.style.animationDelay = `${index * 0.1}s`;
                row.innerHTML = `
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-600 rounded-full font-semibold text-sm">${poll.id}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-semibold text-gray-800">${poll.title}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2 text-sm text-gray-600">
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                            <span>${poll.start_date} → ${poll.end_date}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="deletePoll(${poll.id})" class="inline-flex items-center px-3 py-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors duration-200 text-sm font-medium">
                            <i class="fas fa-trash-alt mr-1"></i>
                            Delete
                        </button>
                    </td>
                `;
                pollsBody.appendChild(row);
            });
        })
        .catch(error => {
            pollsBody.innerHTML = '<tr><td colspan="4" class="px-6 py-8 text-center text-red-500"><i class="fas fa-exclamation-triangle mr-2"></i>Error loading polls</td></tr>';
        });
}

// Load polls on page load
loadPolls();

// Enhanced form submission with better feedback
document.getElementById('createPollForm').addEventListener('submit', function(e){
    e.preventDefault();
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
    submitBtn.disabled = true;
    
    fetch('poll.php?action=create', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(data => {
        // Reset form
        this.reset();
        
        // Show success animation
        submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Created!';
        submitBtn.className = submitBtn.className.replace('from-purple-500 to-purple-600', 'from-green-500 to-green-600');
        
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.className = submitBtn.className.replace('from-green-500 to-green-600', 'from-purple-500 to-purple-600');
            submitBtn.disabled = false;
        }, 2000);
        
        // Reload polls
        loadPolls();
    })
    .catch(error => {
        submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Error';
        submitBtn.className = submitBtn.className.replace('from-purple-500 to-purple-600', 'from-red-500 to-red-600');
        
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.className = submitBtn.className.replace('from-red-500 to-red-600', 'from-purple-500 to-purple-600');
            submitBtn.disabled = false;
        }, 2000);
    });
});

// Enhanced delete function with better confirmation
function deletePoll(id) {
    // Create custom confirmation modal
    const confirmDelete = confirm('⚠️ Are you sure you want to delete this poll?\n\nThis action cannot be undone.');
    if (!confirmDelete) return;
    
    fetch('poll.php?action=delete', {
        method: 'POST',
        body: new URLSearchParams({id})
    })
    .then(response => response.json())
    .then(data => {
        // Reload polls with success indication
        loadPolls();
    })
    .catch(error => {
        alert('❌ Error deleting poll. Please try again.');
    });
}

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Add council dialog placeholder function
function addCouncilDialog() {
    alert('Council member addition dialog would open here.\nThis connects to your existing backend logic.');
}

// Add loading states and animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(el => {
        observer.observe(el);
    });
});
</script>

</body>
</html>