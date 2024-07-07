<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

$username = $_SESSION['username'];
$user = getUserByUsername($username);
$userId = $user['user_id'];

$rentedVideos = getRentedVideosByUser($userId);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Rented Videos</title>
<style>
    .qr-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        text-align: center;
        padding-top: 60px;
    }
    .qr-modal-content {
        margin: 5% auto;
        padding: 20px;
        border: none; /* Remove border for a cleaner look */
        width: 60%; /* Adjust modal width as needed */
        max-width: 600px; /* Max width to ensure modal is viewable */
        background-color: #fff;
        position: relative;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    }
    .qr-modal-content .close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 24px;
        color: #aaa;
        cursor: pointer;
    }
    .qr-modal-content img {
        max-width: 80%; /* Ensure QR code image fits within modal */
        height: auto;
        display: block;
        margin: 0 auto;
    }
    .qr-modal-content p {
        margin-top: 10px;
    }


    .rented-video {
        position: relative;
        display: inline-block;
        width: 188px;
        margin: 10px;
        vertical-align: top;
    }
    .rented-video:hover .rented-video-overlay {
        display: block;
    }
    .rented-video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Semi-transparent overlay */
        display: none;
        justify-content: center;
        align-items: center;
        text-align: center;
    }
    .rented-video-button {
        color: #fff;
        background-color: #007bff;
        border: none;
        padding: 8px 16px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        margin-top: 10px;
    }

    </style>
</head>
<body>
   <div class="container">
    <h1>Rented Videos</h1>
    <?php if (!empty($rentedVideos)): ?>
        <div class="rented-videos">
            <?php foreach ($rentedVideos as $video): ?>
                <div class="rented-video">
                    <div class="rented-video-overlay">
                        <?php if ($video['format'] == 'digital'): ?>
                            <button class="rented-video-button" onclick="showQRCode('<?= htmlspecialchars($video['digital_link']) ?>')">View Link</button>
                        <?php else: ?>
                            <a class="rented-video-button" href="#">Return</a>
                        <?php endif; ?>
                    </div>
                    <img src="uploads/<?= htmlspecialchars($video['image']) ?>" alt="<?= htmlspecialchars($video['title']) ?>" style="height: 282px;">
                    <p><?= htmlspecialchars($video['title']) ?></p>
                    <p>DUE in <?= htmlspecialchars($video['due_in_days']) ?> days</p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No videos rented.</p>
    <?php endif; ?>
</div>


<div id="qrModal" class="qr-modal">
    <div class="qr-modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <img id="qrImage" src="img/LinkQR.png" alt="QR Code">
        <p id="digitalLink"></p>
    </div>
</div>


    <script>
        function showQRCode(link) {
            document.getElementById('digitalLink').innerText = link;
            document.getElementById('qrModal').style.display = "block";
        }

        function closeModal() {
            document.getElementById('qrModal').style.display = "none";
        }

        window.onclick = function(event) {
            var modal = document.getElementById('qrModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
