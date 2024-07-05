<?php
include_once 'functions.php'; // Include your functions file where rental functions are defined

// Check if user is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $video_id = $_POST['video_id'];
    $rental_period = $_POST['rental_period'];
    $user = $_SESSION['username'];
    
    // Call function to rent the video
    $result = rentVideo($video_id, $rental_period, $user);
    if ($result['success']) {
        echo "<script>alert('Video rented successfully');</script>";
    } else {
        echo "<script>alert('Error: " . $result['message'] . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Rent Video</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Add your page content here -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Rent Video</h3>
                    </div>
                    <div class="card-body">
                        <!-- Form to rent a video -->
                        <form action="rent.php" method="post">
                            <div class="form-group">
                                <label>Video ID</label>
                                <input type="number" class="form-control" name="video_id" required>
                            </div>
                            <div class="form-group">
                                <label>Rental Period (days)</label>
                                <input type="number" class="form-control" name="rental_period" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Rent Video</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <!-- /.content-wrapper -->

    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
</div>
<!-- ./wrapper -->
</body>
</html>
