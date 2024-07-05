<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include your database connection
$conn = new mysqli('localhost', 'root', '', 'video_rental_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Get video ID from URL
if (isset($_GET['id'])) {
    $videoId = $_GET['id'];
} else {
    echo "Video ID not provided";
    exit;
}

// Fetch video details
$sql = "SELECT * FROM videos WHERE video_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $videoId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $video = $result->fetch_assoc();
} else {
    echo "No video found";
    exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Video Details</title>
</head>
<body>
    <h1>Video Details</h1>
    <div>
        <h2><?php echo htmlspecialchars($video['title']); ?></h2>
        <p>Director: <?php echo htmlspecialchars($video['director']); ?></p>
        <p>Release Year: <?php echo htmlspecialchars($video['release_year']); ?></p>
        <p>Genre: <?php echo htmlspecialchars($video['genre']); ?></p>
        <p>Format: <?php echo htmlspecialchars($video['format']); ?></p>
        <p>Price: $<?php echo htmlspecialchars($video['price']); ?></p>
        <p>Quantity available: <?php echo htmlspecialchars($video['quantity']); ?></p>
        <img src="uploads/<?php echo htmlspecialchars($video['image']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>" style="max-width: 200px; max-height: 200px;">

        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <button class="btn btn-primary" onclick="window.location.href='index.php?page=edit&id=<?php echo htmlspecialchars($video['video_id']); ?>'">Edit</button>
                <button class="btn btn-primary" onclick="window.location.href='index.php?page=delete&id=<?php echo htmlspecialchars($video['video_id']); ?>'">Delete</button>
            <?php elseif ($_SESSION['role'] == 'user'): ?>
                <button class="btn btn-primary" onclick="window.location.href='index.php?page=rent&id=<?php echo htmlspecialchars($video['video_id']); ?>'">Rent</button>
            <?php endif; ?>
        <?php endif; ?>

        <button class="btn btn-secondary" onclick="window.location.href='index.php?page=view'">Back</button>
    </div>
</body>
</html>
