// return_video.php
<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rentalId = $_POST['rental_id'];
    $rental = getRentalById($rentalId);

    if ($rental) {
        $returnDate = date('Y-m-d');
        $dueDate = $rental['return_date'];
        $lateFee = 0;

        if (strtotime($returnDate) > strtotime($dueDate)) {
            $daysLate = (strtotime($returnDate) - strtotime($dueDate)) / (60 * 60 * 24);
            switch ($rental['format']) {
                case 'blu_ray':
                    $lateFee = $daysLate * $rental['blu_ray_late_fee'];
                    break;
                case 'dvd':
                    $lateFee = $daysLate * $rental['dvd_late_fee'];
                    break;
            }
        }

        if ($rental['format'] !== 'digital') {
            switch ($rental['format']) {
                case 'blu_ray':
                    $newStock = $rental['blu_ray_copies'] + $rental['quantity'];
                    $stmt = $conn->prepare("UPDATE videos SET blu_ray_copies = ? WHERE video_id = ?");
                    break;
                case 'dvd':
                    $newStock = $rental['dvd_copies'] + $rental['quantity'];
                    $stmt = $conn->prepare("UPDATE videos SET dvd_copies = ? WHERE video_id = ?");
                    break;
            }
            $stmt->bind_param("ii", $newStock, $rental['video_id']);
            $stmt->execute();
        }

        $stmt = $conn->prepare("UPDATE rentals SET status = 'returned', return_date = ?, late_fee = ? WHERE rental_id = ?");
        $stmt->bind_param("sdi", $returnDate, $lateFee, $rentalId);
        $stmt->execute();

        $_SESSION['alert'] = ['message' => 'Return processed successfully.', 'type' => 'success'];
        header('Location: index.php?page=view');
        exit;
    } else {
        $_SESSION['alert'] = ['message' => 'Rental not found.', 'type' => 'danger'];
        header('Location: index.php?page=view');
        exit;
    }
} else {
    $_SESSION['alert'] = ['message' => 'Invalid request.', 'type' => 'danger'];
    header('Location: index.php?page=view');
    exit;
}
?>
