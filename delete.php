<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'functions.php';

// Function to set session alerts
function setAlert($message, $type = 'success') {
    $_SESSION['alert'] = ['message' => $message, 'type' => $type];
}

// Check if a valid video ID is passed and deletion has not yet been confirmed
if (isset($_GET['id']) && !isset($_GET['confirm'])) {
    $videoId = htmlspecialchars($_GET['id']);
    $video = getVideoById($videoId); // Retrieve video details

    if ($video) {
        ?>
        <div class="container">
            <h1>Delete Video</h1>
            <p>Are you sure you want to delete this video?</p>
            <div class="card">
                <div class="card-body">
                    <p class="card-text">Title: <?= htmlspecialchars($video['title']) ?></p>
                    <p class="card-text">Production: <?= htmlspecialchars($video['production']) ?></p>
                    <p class="card-text">Release Year: <?= htmlspecialchars($video['release_year']) ?></p>
                    <p class="card-text">Genre: <?= htmlspecialchars($video['genre']) ?></p>
                    <p class="card-text">Trailer Link: <?= htmlspecialchars($video['trailer_link']) ?></p>
                    <p class="card-text">Duration: <?= htmlspecialchars($video['duration']) ?> minutes</p>
                    <p class="card-text">Plot: <?= htmlspecialchars($video['plot']) ?></p>
                    <p class="card-text">Blu-ray Copies: <?= htmlspecialchars($video['blu_ray_copies']) ?></p>
                    <p class="card-text">Blu-ray Price: <?= htmlspecialchars($video['blu_ray_price']) ?></p>
                    <p class="card-text">Blu-ray Late Fee: <?= htmlspecialchars($video['blu_ray_late_fee']) ?></p>
                    <p class="card-text">DVD Copies: <?= htmlspecialchars($video['dvd_copies']) ?></p>
                    <p class="card-text">DVD Price: <?= htmlspecialchars($video['dvd_price']) ?></p>
                    <p class="card-text">DVD Late Fee: <?= htmlspecialchars($video['dvd_late_fee']) ?></p>
                    <p class="card-text">Digital Link: <?= htmlspecialchars($video['digital_link']) ?></p>
                    <p class="card-text">Digital Price: <?= htmlspecialchars($video['digital_price']) ?></p>
                </div>
            </div>
            <div>
                <a href="delete.php?confirm=yes&id=<?= $videoId; ?>" class="btn btn-danger">Delete</a>
                <a href="index.php?page=view" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
        <?php
    } else {
        setAlert("Video not found.", "danger");
        header('Location: index.php?page=view');
        exit();
    }
} elseif (isset($_GET['confirm']) && $_GET['confirm'] == 'yes' && isset($_GET['id'])) {
    // Confirm deletion
    if (deleteVideo($_GET['id'])) {
        setAlert('Video deleted successfully.', 'success');
    } else {
        setAlert('Failed to delete video. Video not found.', 'danger');
    }
    header('Location: index.php?page=view');
    exit();
} else {
 
    setAlert('No video ID specified.', 'danger');
    header('Location: index.php?page=view');
    exit();
}
?>
