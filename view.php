<!DOCTYPE html>
<html>
<head>
    <title>View Videos</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="search.js"></script>
</head>
<body>
    <h1>View Videos</h1>

    <!-- Search form -->
    <form onsubmit="searchVideos(); return false;">
     <input type="text" id="search" name="search" placeholder="Search by title, director, year, or genre" style="width: 300px;">
        <button type="submit">Search</button>
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
        if (isset($_GET['search'])) {
            $searchTerm = $_GET['search'];
        } else {
            $searchTerm = "";
        }

        // Display videos based on search term
        displayVideos($conn, $searchTerm);

        $conn->close(); // Close the database connection
        ?>
    </div>

</body>
</html>
