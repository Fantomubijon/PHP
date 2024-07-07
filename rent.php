<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'functions.php';

// Check if user is logged in and is a user
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $videoId = htmlspecialchars($_GET['id']);
    $video = getVideoById($videoId);

    if ($video) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Rent Video</title>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <style>
                .out-of-stock {
                    color: red;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Rent Video</h1>
                <div class="card">
                    <div class="card-body">
                        <p class="card-text">Title: <?= htmlspecialchars($video['title']) ?></p>
                        <p class="card-text">Production: <?= htmlspecialchars($video['production']) ?></p>
                        <p class="card-text">Release Year: <?= htmlspecialchars($video['release_year']) ?></p>
                        <p class="card-text">Genre: <?= htmlspecialchars($video['genre']) ?></p>
                        <img src="uploads/<?= htmlspecialchars($video['image']) ?>" alt="<?= htmlspecialchars($video['title']) ?>" style="height: 282px;">
                    </div>
                </div>

                <form action="process_rent.php" method="post">
                    <input type="hidden" name="video_id" value="<?= $videoId ?>">
                    <div class="form-group">
                        <label>Format:</label><br>
                        <input type="radio" name="format" value="blu_ray" data-price="<?= $video['blu_ray_price'] ?>" data-stock="<?= $video['blu_ray_copies'] ?>" <?= $video['blu_ray_copies'] == 0 ? 'disabled' : '' ?> required> Blu-ray (<?= $video['blu_ray_copies'] ?> available<?= $video['blu_ray_copies'] == 0 ? ' - <span class="out-of-stock">(OUT OF STOCK)</span>' : '' ?>)<br>
                        <input type="radio" name="format" value="dvd" data-price="<?= $video['dvd_price'] ?>" data-stock="<?= $video['dvd_copies'] ?>" <?= $video['dvd_copies'] == 0 ? 'disabled' : '' ?> required> DVD (<?= $video['dvd_copies'] ?> available<?= $video['dvd_copies'] == 0 ? ' - <span class="out-of-stock">(OUT OF STOCK)</span>' : '' ?>)<br>
                        <input type="radio" name="format" value="digital" data-price="<?= $video['digital_price'] ?>" required> Digital (Link)
                    </div>

                    <div class="form-group" id="quantity-group" style="display: none;">
                        <label>Quantity:</label>
                        <input type="number" name="quantity" id="quantity" min="1" oninput="this.value = Math.abs(this.value)">
                    </div>

                    <div class="form-group">
                        <label>Return Due Date:</label>
                        <input type="text" id="due_date" readonly>
                    </div>

                    <div class="form-group">
                        <label>Total Price:</label>
                        <input type="text" id="total_price" readonly>
                    </div>

                    <button type="submit" class="btn btn-primary">Confirm Rental</button>
                </form>
            </div>

            <script>
                $(document).ready(function() {
                    // Calculate the due date (one week from today)
                    var dueDate = new Date();
                    dueDate.setDate(dueDate.getDate() + 7);
                    var dd = dueDate.getDate();
                    var mm = dueDate.getMonth() + 1; // January is 0
                    var yyyy = dueDate.getFullYear();
                    if (dd < 10) dd = '0' + dd;
                    if (mm < 10) mm = '0' + mm;
                    $('#due_date').val(mm + '/' + dd + '/' + yyyy);

                    $('input[name="format"]').on('change', function() {
                        var price = $(this).data('price');
                        var stock = $(this).data('stock');

                        if ($(this).val() === 'digital') {
                            $('#quantity-group').hide();
                            $('#quantity').val(1);
                        } else {
                            $('#quantity-group').show();
                            $('#quantity').attr('max', stock);
                            $('#quantity').val(1);
                        }

                        updateTotalPrice(price, $('#quantity').val());
                    });

                    $('#quantity').on('input', function() {
                        var price = $('input[name="format"]:checked').data('price');
                        updateTotalPrice(price, $(this).val());
                    });

                    function updateTotalPrice(price, quantity) {
                        var totalPrice = price * quantity;
                        $('#total_price').val(totalPrice.toFixed(2));
                    }
                });
            </script>
        </body>
        </html>
        <?php
    } else {
        echo "Video not found.";
    }
} else {
    echo "Invalid video ID.";
}
?>
