<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if the user is not authenticated
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

require 'functions.php';

$page = $_GET['page'] ?? 'home';

if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'user';
}

$allowedPages = [
    'user' => ['home', 'rent', 'rent_payment'],
    'admin' => ['home', 'add', 'view', 'rent', 'rent_payment', 'report']
];

if (!in_array($page, $allowedPages[$_SESSION['role']])) {
    $page = 'home';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Rental System</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Include menu.php here -->
        <?php include 'menu.php'; ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <?php
                    $page = $_GET['page'] ?? 'home'; 
                    switch ($page) {
                        case 'add':
                            include 'add.php';
                            break;
                        case 'edit':
                            include 'edit.php';
                            break;
                        case 'delete':
                            include 'delete.php';
                            break;
                        case 'view':
                            include 'view.php';
                            break;
                        case 'rent':
                            include 'rent.php';
                            break;
                         case 'edit':
                            include 'edit.php';
                            break;
                        case 'view_single':
                            include 'view_single.php';
                        break;
                          case 'delete':
                            include 'delete.php';
                        break;
                        case 'rent_payment':
                            include 'rent_payment.php';
                            break;
                            
                        default:
                            echo '<div class="alert alert-info">Welcome to the Video Rental System, ' . htmlspecialchars($_SESSION['username']) . '!</div>';
                            break;
                    }
                    ?>
                </div>
            </section>
        </div>
        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2023 Your Company.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.2.0
            </div>
        </footer>
    </div>
    <!-- REQUIRED SCRIPTS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
</body>
</html>


