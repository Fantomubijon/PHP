<?php
include_once 'functions.php'; // Include your functions file where addVideo() is defined

// Check if user is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $director = $_POST['director'];
    $release_year = $_POST['release_year'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $format = $_POST['format'];
    $genre = $_POST['genre']; // Added genre field
    $image = $_FILES['image']['name'];
    $target = 'uploads/' . basename($image);

    // Check if uploads directory exists
    if (!is_dir('uploads')) {
        mkdir('uploads', 0777, true);
    }

    // Attempt to move the uploaded file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        // Call the addVideo function and pass user_id
        addVideo($title, $director, $release_year, $price, $quantity, $format, $genre, $image); // Pass genre to addVideo()
        echo "<script>alert('Video added successfully');</script>";
        header('Location: index.php?page=add');
        exit; // Always exit after a header redirect
    } else {
        echo "Failed to upload image";
        error_log("Failed to move uploaded file: " . $_FILES['image']['error']);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Video</title>
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
                        <h3 class="card-title">Add New Video</h3>
                    </div>
                    <div class="card-body">
                        <!-- Form to add a new video -->
                        <form action="add.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="form-group">
                                <label>Director</label>
                                <input type="text" class="form-control" name="director" required>
                            </div>
                            <div class="form-group">
                                <label>Release Year</label>
                                <input type="number" class="form-control" name="release_year" required>
                            </div>
                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" step="0.01" class="form-control" name="price" required>
                            </div>
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" class="form-control" name="quantity" required>
                            </div>
                            <div class="form-group">
                                <label>Format</label>
                                <select name="format" class="form-control" required>
                                    <option value="DVD">DVD</option>
                                    <option value="Blu-ray">Blu-ray</option>
                                    <option value="Digital">Digital</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Genre</label> <!-- Added genre field -->
                                <input type="text" class="form-control" name="genre" required>
                            </div>
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" class="form-control-file" name="image" accept="image/*" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Video</button>
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
