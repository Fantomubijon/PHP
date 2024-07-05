<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>

<?php
function addVideo($title, $director, $release_year, $price, $quantity, $format, $genre, $image) {
    $conn = new mysqli('localhost', 'root', '', 'video_rental_system');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL insertion
    $sql = "INSERT INTO videos (title, director, release_year, price, quantity, format, genre, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiisss", $title, $director, $release_year, $price, $quantity, $format, $genre, $image);
    $stmt->execute();

    $stmt->close();
    $conn->close();
}
?>

<?php
// Include your database connection
$conn = new mysqli('localhost', 'root', '', 'video_rental_system');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Function to fetch and display videos
function displayVideos($conn, $searchTerm = "") {

    if (!isset($_SESSION['username'])) {
        echo "User not logged in";
        return;
    }

    // Base query to select all videos
    $sql = "SELECT * FROM videos";

    // Append search criteria if provided
    if (!empty($searchTerm)) {
        // Use AND instead of WHERE if adding to an existing WHERE clause
        $sql .= " AND (title LIKE '%$searchTerm%' OR director LIKE '%$searchTerm%' OR release_year = '$searchTerm')";
    }

    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Display video details
                echo "<div>";
                echo "<h2>" . $row['title'] . "</h2>";
                echo "<p>Director: " . $row['director'] . "</p>";
                echo "<p>Release Year: " . $row['release_year'] . "</p>";
                echo "<p>Genre: " . $row['genre'] . "</p>";
                echo "<p>Format: " . $row['format'] . "</p>";
                echo "<p>Price: $" . $row['price'] . "</p>";
                echo "<p>Quantity available: " . $row['quantity'] . "</p>";
                echo "<img src='uploads/" . $row['image'] . "' alt='" . $row['title'] . "' style='max-width: 200px; max-height: 200px;'>";
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
?>

<?php
// Function to get a video by ID
function getVideoById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM videos WHERE video_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $video = $result->fetch_assoc();
    $stmt->close();
    return $video;
}

// Function to update a video
function updateVideo($id, $title, $director, $release_year, $price, $quantity, $format, $genre, $image) {
    global $conn;
    if ($image) {
        $stmt = $conn->prepare("UPDATE videos SET title = ?, director = ?, release_year = ?, price = ?, quantity = ?, format = ?, genre = ?, image = ? WHERE video_id = ? ");
        $stmt->bind_param("ssdiisssi", $title, $director, $release_year, $price, $quantity, $format, $genre, $image, $id);
    } else {
        $stmt = $conn->prepare("UPDATE videos SET title = ?, director = ?, release_year = ?, price = ?, quantity = ?, format = ?, genre = ? WHERE id = ? ");
        $stmt->bind_param("ssdiissi", $title, $director, $release_year, $price, $quantity, $format, $genre, $id);
    }
    $stmt->execute();
    $stmt->close();
}

// Function to delete a video
function deleteVideo($id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM videos WHERE video_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
?>
