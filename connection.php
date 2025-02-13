<?php
    $servername = "localhost";
    $username = "root";
    $password = "Rev2005@!!";
    $db_name = "srm_sports";
    $port = 4307; // Specify the port number

    // Create connection
    $conn = new mysqli($servername, $username, $password, $db_name, $port);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>