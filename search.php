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

// Function to fetch and display videos based on search term
function searchVideos($conn, $searchTerm = "") {

    // Base query to select all videos
    $sql = "SELECT * FROM videos";

    // Append search criteria if provided
    if (!empty($searchTerm)) {
        $sql .= " WHERE (title LIKE '%$searchTerm%' 
                 OR director LIKE '%$searchTerm%' 
                 OR release_year LIKE '$searchTerm'
                 OR genre LIKE '%$searchTerm%')";
    }

    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Display video details
                echo "<div>";
                echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
                echo "<p>Director: " . htmlspecialchars($row['director']) . "</p>";
                echo "<p>Release Year: " . htmlspecialchars($row['release_year']) . "</p>";
                echo "<p>Genre: " . htmlspecialchars($row['genre']) . "</p>";
                echo "<p>Format: " . htmlspecialchars($row['format']) . "</p>";
                echo "<p>Price: $" . htmlspecialchars($row['price']) . "</p>";
                echo "<p>Quantity available: " . htmlspecialchars($row['quantity']) . "</p>";
                echo "<img src='uploads/" . htmlspecialchars($row['image']) . "' alt='" . htmlspecialchars($row['title']) . "' style='max-width: 200px; max-height: 200px;'>";
                
echo "<button class='btn btn-primary' onclick=\"window.location.href='index.php?page=view_single&id=" . htmlspecialchars($row['video_id']) . "'\">View</button>";

                echo "</div>";
            }
        } else {
            echo "No videos found";
        }
    } else {
        echo "Error: " . $conn->error;
    }
}

// Process search term if provided
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
} else {
    $searchTerm = "";
}

// Display videos based on search term
searchVideos($conn, $searchTerm);

$conn->close(); // Close the database connection
?>
