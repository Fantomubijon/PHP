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
                    <p class="card-text">Director: <?= htmlspecialchars($video['director']) ?></p>
                    <p class="card-text">Release Year: <?= htmlspecialchars($video['release_year']) ?></p>
                    <p class="card-text">Price: <?= htmlspecialchars($video['price']) ?></p>
                    <p class="card-text">Quantity: <?= htmlspecialchars($video['quantity']) ?></p>
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
    header('Location: index.php?page=view'); // Redirect to the video list page
    exit();
} else {
    // No ID was provided or confirmation not set
    setAlert('No video ID specified.', 'danger');
    header('Location: index.php?page=view');
    exit();
}
?>
