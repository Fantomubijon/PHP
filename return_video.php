<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

// Check if rental ID is provided
if (isset($_GET['id'])) {
    $rentalId = htmlspecialchars($_GET['id']);
    $rental = getRentalById($rentalId);

    if ($rental) {
        $videoId = $rental['video_id'];
        $video = getVideoById($videoId);

        // Calculate due date and check if overdue
        $dueDate = strtotime($rental['return_date']);
        $today = strtotime(date('Y-m-d'));
        $daysLate = max(0, floor(($today - $dueDate) / (60 * 60 * 24)));
        $isOverdue = ($daysLate > 0);

        // Determine action button text and link based on overdue status
        if ($isOverdue) {
            $actionText = "Pay Late Fee";
            $actionLink = "pay_late_fee.php?id={$rentalId}";
        } else {
            $actionText = "Confirm Return";
            $actionLink = "confirm_return.php?id={$rentalId}";
        }

        // Display confirmation details
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Return Video</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                .details {
                    margin-bottom: 20px;
                }
                .overdue {
                    color: red;
                }
                .not-overdue {
                    color: green;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Return Video</h1>
                <div class="details">
                    <p>Video Title: <?= htmlspecialchars($video['title']) ?></p>
                    <p>Due Date: <?= date('Y-m-d', $dueDate) ?></p>
                    <p>Days Late: <?= $daysLate ?></p>
                    <p>Status: <?= $isOverdue ? '<span class="overdue">Overdue</span>' : '<span class="not-overdue">Not Overdue</span>' ?></p>
                </div>
                <a class="btn btn-primary" href="<?= $actionLink ?>"><?= $actionText ?></a>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo '<p>Rental not found.</p>';
    }
} else {
    echo '<p>No rental ID provided.</p>';
}
?>
