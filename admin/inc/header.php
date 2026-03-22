<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle : 'Admin Panel' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        body {
            background: #0b1120;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex;
            position: relative;
        }
        /* sidebar */
        .sidebar {
            width: 260px;
            background: #111827;
            border-right: 1px solid #1e293b;
            display: flex;
            flex-direction: column;
            padding: 24px 0;
            transition: transform 0.3s ease;
            z-index: 1000;
        }
        .sidebar .logo {
            padding: 0 20px 24px;
            font-size: 1.5rem;
            font-weight: bold;
            color: #3b82f6;
            border-bottom: 1px solid #1e293b;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sidebar .close-btn {
            display: none;
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .sidebar .close-btn:hover { color: #fff; }
        .sidebar .nav { flex: 1; }
        .sidebar .nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar .nav a:hover,
        .sidebar .nav a.active {
            background: #1e293b;
            color: #fff;
            border-left-color: #3b82f6;
        }
        .sidebar .nav a i { width: 20px; font-size: 1.2rem; }
        .sidebar .logout {
            margin: 20px 20px 0;
            padding: 12px;
            background: #1e293b;
            text-align: center;
            border-radius: 8px;
        }
        .sidebar .logout a {
            color: #ef4444;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        /* main content */
        .main {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            background: #0f172a;
        }
        .container { max-width: 1400px; margin: 0 auto; }
        /* menu toggle (mobile) */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            background: #1e293b;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            z-index: 1001;
        }
        /* overlay for mobile */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        /* mobile styles */
        @media (max-width: 768px) {
            body { display: block; }
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100%;
                transform: translateX(-100%);
                box-shadow: 2px 0 10px rgba(0,0,0,0.3);
            }
            .sidebar .close-btn { display: inline-block; }
            .main {
                padding: 80px 20px 20px;
                width: 100%;
            }
            .menu-toggle { display: block; }
            .overlay.active { display: block; }
            .sidebar.open { transform: translateX(0); }
        }
        /* common styles */
        h1 { font-size: 2rem; margin-bottom: 24px; font-weight: 600; }
        .card {
            background: #1e293b;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.5);
            margin-bottom: 24px;
            border: 1px solid #334155;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 500;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn:hover { background: #2563eb; }
        .btn-secondary { background: #475569; }
        .btn-secondary:hover { background: #64748b; }
        .btn-danger { background: #ef4444; }
        .btn-danger:hover { background: #dc2626; }
        .form-group { margin-bottom: 20px; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #cbd5e1;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            color: #f1f5f9;
            font-size: 1rem;
        }
        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
        }
        textarea.form-control { min-height: 120px; resize: vertical; }
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left;
            padding: 12px;
            background: #0f172a;
            color: #94a3b8;
            font-weight: 500;
            border-bottom: 1px solid #334155;
        }
        td { padding: 16px 12px; border-bottom: 1px solid #334155; }
        .actions a { margin-right: 12px; color: #3b82f6; text-decoration: none; }
        .actions a.delete { color: #ef4444; }
        img.thumb {
            max-width: 80px;
            border-radius: 6px;
            border: 1px solid #334155;
        }
        .current-img {
            max-width: 200px;
            border-radius: 8px;
            margin: 10px 0;
            border: 2px solid #334155;
        }
        .message {
            background: #1e3a5f;
            color: #93c5fd;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3b82f6;
        }
        .error {
            background: #4c1d28;
            color: #fecaca;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #ef4444;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #94a3b8;
            text-decoration: none;
        }
        .back-link:hover { color: #fff; }
    </style>
</head>
<body>
    <!-- Mobile menu toggle button -->
    <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>
    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo">
            <i class="fas fa-cube"></i> Admin
            <button class="close-btn" id="closeSidebar"><i class="fas fa-times"></i></button>
        </div>
        <div class="nav">
            <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="about.php" class="<?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> About</a>
            <a href="blogs.php" class="<?= strpos($_SERVER['PHP_SELF'], 'blogs') !== false ? 'active' : '' ?>"><i class="fas fa-blog"></i> Blogs</a>
            <a href="projects.php" class="<?= strpos($_SERVER['PHP_SELF'], 'projects') !== false ? 'active' : '' ?>"><i class="fas fa-code-branch"></i> Projects</a>
        </div>
        <div class="logout">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main content -->
    <div class="main">
        <div class="container">

<script>
    // Mobile sidebar toggle
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuToggle = document.getElementById('menuToggle');
    const closeBtn = document.getElementById('closeSidebar');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // prevent scrolling
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    menuToggle.addEventListener('click', openSidebar);
    closeBtn.addEventListener('click', closeSidebar);
    overlay.addEventListener('click', closeSidebar);

    // Optional: close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });
</script>
