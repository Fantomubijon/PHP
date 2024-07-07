<!DOCTYPE html>
<html>
<head>
    <title>View Videos</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .search-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-input {
            flex: 1;
        }
        .btn-primary-submit {
            margin-left: 10px;
        }
        .flw-item {
            display: inline-block;
            width: 188px;
            margin: 10px;
            vertical-align: top;
        }
        .film-poster {
            position: relative;
        }
        .film-poster-quality {
            position: absolute;
            top: 5px;
            left: 5px;
            background-color: #007bff;
            color: #fff;
            padding: 2px 5px;
            font-size: 12px;
        }
        .film-poster-img {
            width: 100%;
            height: auto;
        }
        .film-poster-ahref {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
            color: #fff;
            display: none;
        }
        .film-poster:hover .film-poster-ahref {
            display: block;
        }
        .film-detail {
            text-align: left;
        }
        .film-name {
            font-size: 16px;
            margin: 5px 0;
        }
        .fd-infor {
            font-size: 14px;
            color: #888;
        }
        .fdi-item {
            margin-right: 5px;
        }
    </style>

</head>
<body>
    <h1>View Videos</h1>

    <!-- Search form -->
    <form onsubmit="searchVideos(); return false;" class="search-container">
        <input type="text" id="search" name="search" placeholder="Enter Title, Genre, Production, Year" autocomplete="off" class="form-control search-input">
        <button type="submit" class="btn btn-primary btn-primary-submit"><i class="fas fa-arrow-right"></i></button>
    </form>

    <hr>

    <!-- Display search results dynamically here -->
    <div id="search-results">
        <?php
        // Include your database connection
        $conn = new mysqli('localhost', 'root', '', 'video_rental_system');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Include the file where displayVideos() is defined
        require_once 'functions.php';

        // Process search term if provided
        $searchTerm = isset($_GET['search']) ? $_GET['search'] : "";

        // Display videos based on search term
        displayVideos($conn, $searchTerm);

        $conn->close(); // Close the database connection
        ?>
    </div>

    <script>
        function searchVideos() {
            // Get the search term from the input field
            var searchTerm = document.getElementById('search').value.trim();

            // Perform an AJAX request
            $.ajax({
                url: 'search.php',
                method: 'GET',
                data: { search: searchTerm },
                success: function(response) {
                    // Update the search results container with the response
                    document.getElementById('search-results').innerHTML = response;
                },
                error: function() {
                    console.error('Request failed');
                }
            });
        }
    </script>
</body>
</html>

