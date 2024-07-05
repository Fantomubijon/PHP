<?php
include_once 'functions.php'; // Include your functions file where updateVideo() is defined

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

// Include your database connection
$conn = new mysqli('localhost', 'root', '', 'video_rental_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get video ID from URL
if (isset($_GET['id'])) {
    $videoId = $_GET['id'];
} else {
    echo "Video ID not provided";
    exit;
}

// Fetch video details
$sql = "SELECT * FROM videos WHERE video_id = ? AND user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $videoId, $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $video = $result->fetch_assoc();
} else {
    echo "No video found";
    exit;
}

$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $director = $_POST['director'];
    $release_year = $_POST['release_year'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $format = $_POST['format'];
    $genre = $_POST['genre']; // Added genre field
    $image = $video['image'];

    // Check if new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image = $_FILES['image']['name'];
        $target = 'uploads/' . basename($image);

        // Attempt to move the uploaded file
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            echo "Failed to upload image";
            exit;
        }
    }

    // Call the updateVideo function and pass user_id
    updateVideo($videoId, $title, $director, $release_year, $price, $quantity, $format, $genre, $image, $_SESSION['username']); // Pass genre to updateVideo()
    echo "<script>alert('Video updated successfully');</script>";
    header('Location: index.php?page=view_single&id=' . $videoId);
    exit; // Always exit after a header redirect
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Video</title>
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
                        <h3 class="card-title">Edit Video</h3>
                    </div>
                    <div class="card-body">
                        <!-- Form to edit the video -->
                        <form action="edit.php?id=<?php echo htmlspecialchars($video['video_id']); ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($video['title']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Director</label>
                                <input type="text" class="form-control" name="director" value="<?php echo htmlspecialchars($video['director']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Release Year</label>
                                <input type="number" class="form-control" name="release_year" value="<?php echo htmlspecialchars($video['release_year']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Price</label>
                                <input type="number" step="0.01" class="form-control" name="price" value="<?php echo htmlspecialchars($video['price']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Quantity</label>
                                <input type="number" class="form-control" name="quantity" value="<?php echo htmlspecialchars($video['quantity']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Format</label>
                                <select name="format" class="form-control" required>
                                    <option value="DVD" <?php if ($video['format'] == 'DVD') echo 'selected'; ?>>DVD</option>
                                    <option value="Blu-ray" <?php if ($video['format'] == 'Blu-ray') echo 'selected'; ?>>Blu-ray</option>
                                    <option value="Digital" <?php if ($video['format'] == 'Digital') echo 'selected'; ?>>Digital</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Genre</label>
                                <input type="text" class="form-control" name="genre" value="<?php echo htmlspecialchars($video['genre']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Image</label>
                                <input type="file" class="form-control-file" name="image" accept="image/*">
                                <p>Current Image: <img src="uploads/<?php echo htmlspecialchars($video['image']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" style="max-width: 100px; max-height: 100px;"></p>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Video</button>
                            <button class="btn btn-secondary" onclick="window.location.href='index.php?page=view_single&id=<?php echo htmlspecialchars($video['video_id']); ?>'">Cancel</button>
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
