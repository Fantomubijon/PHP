<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php'; // Include your database connection file

// Function to add a video
function addVideo($title, $production, $release_year, $genre, $trailer_link, $duration, $plot, $image, $blu_ray_copies, $blu_ray_price, $blu_ray_late_fee, $dvd_copies, $dvd_price, $dvd_late_fee, $digital_link, $digital_price) {
    global $conn;

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // SQL insertion
    $sql = "INSERT INTO videos (title, production, release_year, genre, trailer_link, duration, plot, image, blu_ray_copies, blu_ray_price, blu_ray_late_fee, dvd_copies, dvd_price, dvd_late_fee, digital_link, digital_price) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssssiddiddsd", $title, $production, $release_year, $genre, $trailer_link, $duration, $plot, $image, $blu_ray_copies, $blu_ray_price, $blu_ray_late_fee, $dvd_copies, $dvd_price, $dvd_late_fee, $digital_link, $digital_price);
    $stmt->execute();

    $stmt->close();
}

// Function to display videos
function displayVideos($conn, $searchTerm = "") {
    $sql = "SELECT * FROM videos WHERE title LIKE '%$searchTerm%' OR production LIKE '%$searchTerm%' OR release_year LIKE '%$searchTerm%' OR genre LIKE '%$searchTerm%'";
    $result = $conn->query($sql);

    if (!empty($searchTerm)){
        echo '<h4>Showing results of "'. htmlspecialchars($searchTerm). '"</h4>';
    }

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<div class="flw-item">';
            echo '<div class="film-poster">';
            echo '<div class="pick film-poster-quality">' . htmlspecialchars($row['genre']) . '</div>';
            echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" title="' . htmlspecialchars($row['title']) . '" alt="' . htmlspecialchars($row['title']) . '" class="film-poster-img" style="height: 282px;">';
            echo '<a href="index.php?page=view_single&id=' . htmlspecialchars($row['video_id']) . '" title="' . htmlspecialchars($row['title']) . '" class="film-poster-ahref flw-item-tip"><i class="fa fa-play"></i></a>';
            echo '</div>';
            echo '<div class="film-detail film-detail-fix">';
            echo '<h3 class="film-name"><a href="index.php?page=view_single&id=' . htmlspecialchars($row['video_id']) . '" title="' . htmlspecialchars($row['title']) . '">' . htmlspecialchars($row['title']) . '</a></h3>';
            echo '<div class="fd-infor">';
            echo '<span class="fdi-item">' . htmlspecialchars($row['release_year']) . '</span>';
            echo '<span class="fdi-item">·</span>'; // Dot separator
            echo '<span class="fdi-item">' . htmlspecialchars($row['duration']) . ' M</span>';
            echo '</div>';
            echo '</div>';
            echo '<div class="clearfix"></div>';
            echo '</div>';
        }
    }
}

// Function to get a video by ID
function getVideoById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM videos WHERE video_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $video = $result->fetch_assoc();
    $stmt->close();
    return $video;
}

// Function to update a video
function updateVideo($conn, $id, $title, $production, $release_year, $genre, $trailer_link, $duration, $plot, $image, $blu_ray_copies, $blu_ray_price, $blu_ray_late_fee, $dvd_copies, $dvd_price, $dvd_late_fee, $digital_link, $digital_price) {
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if (!empty($image)) {
        $sql = "UPDATE videos SET title=?, production=?, release_year=?, genre=?, trailer_link=?, duration=?, plot=?, image=?, blu_ray_copies=?, blu_ray_price=?, blu_ray_late_fee=?, dvd_copies=?, dvd_price=?, dvd_late_fee=?, digital_link=?, digital_price=? WHERE video_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssssiddiddsdi", $title, $production, $release_year, $genre, $trailer_link, $duration, $plot, $image, $blu_ray_copies, $blu_ray_price, $blu_ray_late_fee, $dvd_copies, $dvd_price, $dvd_late_fee, $digital_link, $digital_price, $id);
    } else {
        $sql = "UPDATE videos SET title=?, production=?, release_year=?, genre=?, trailer_link=?, duration=?, plot=?, blu_ray_copies=?, blu_ray_price=?, blu_ray_late_fee=?, dvd_copies=?, dvd_price=?, dvd_late_fee=?, digital_link=?, digital_price=? WHERE video_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssiddiddsdi", $title, $production, $release_year, $genre, $trailer_link, $duration, $plot, $blu_ray_copies, $blu_ray_price, $blu_ray_late_fee, $dvd_copies, $dvd_price, $dvd_late_fee, $digital_link, $digital_price, $id);
    }

    $stmt->execute();
    $stmt->close();
}

// Function to delete a video
function deleteVideo($conn, $id) {
    // First, fetch the video to get the image file name
    $stmt = $conn->prepare("SELECT image FROM videos WHERE video_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    if ($image) {
        // Delete the image file from the uploads folder
        $imagePath = 'uploads/' . $image;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Delete the video record from the database
        $stmt = $conn->prepare("DELETE FROM videos WHERE video_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        return true;
    } else {
        return false;
    }
}

// Function to get rented videos by user
function getRentedVideosByUser($conn, $userId) {
    $stmt = $conn->prepare("SELECT v.image, v.title, r.format, r.status, DATEDIFF(r.return_date, CURDATE()) AS due_in_days, v.digital_link, r.rental_id,r.quantity 
                            FROM rentals r
                            JOIN videos v ON r.video_id = v.video_id
                            WHERE r.user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $videos = [];
    while ($row = $result->fetch_assoc()) {
        $videos[] = $row;
    }

    $stmt->close();
    return $videos;
}

// Function to update video quantity
function updateVideoQuantity($conn, $videoId, $column, $quantity) {
    $stmt = $conn->prepare("UPDATE videos SET $column = $column - ? WHERE video_id = ?");
    $stmt->bind_param('ii', $quantity, $videoId);
    $stmt->execute();
    $stmt->close();
}

// Function to create a rental record
function createRental($conn, $userId, $videoId, $format, $quantity, $totalPrice) {
    $rentalDate = date('Y-m-d');
    $returnDate = date('Y-m-d', strtotime('+1 week'));
    $stmt = $conn->prepare("INSERT INTO rentals (user_id, video_id, format, quantity, rental_date, return_date, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iissssd", $userId, $videoId, $format, $quantity, $rentalDate, $returnDate, $totalPrice);
    $stmt->execute();
    $stmt->close();
    return $conn->insert_id;
}

// Function to get rental details by ID
function getRentalById($conn, $rentalId) {
    $stmt = $conn->prepare("SELECT r.*, v.title, v.blu_ray_price, v.dvd_price, v.digital_price, DATEDIFF(r.return_date, CURDATE()) AS due_in_days FROM rentals r JOIN videos v ON r.video_id = v.video_id WHERE r.rental_id = ?");
    $stmt->bind_param('i', $rentalId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to get user by username
function getUserByUsername($conn, $username) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}

// Function to update rental status
function updateRentalStatus($conn, $rentalId, $status) {
    $sql = "UPDATE rentals SET status = ? WHERE rental_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $rentalId);
    $stmt->execute();
    $stmt->close();
}

?>
