<?php
// Database configuration
$hostname = 'localhost'; // or your hostname
$port = 3306; // default MySQL port
$username = 'uauy4aubthoeo';
$password = '1L1kETuRt13$!';
$database = 'dbv6zl7ieploob';

// Create a connection to the database
$conn = new mysqli($hostname, $username, $password, $database, $port);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to remove apostrophes from FirstName
$updateFirstNameQuery = "UPDATE `emailupload` SET FirstName = REPLACE(FirstName, '\'', '') WHERE FirstName LIKE '%\'%' AND SendEmail=0";

// Query to remove apostrophes from LastName
$updateLastNameQuery = "UPDATE `emailupload` SET LastName = REPLACE(LastName, '\'', '') WHERE LastName LIKE '%\'%' AND SendEmail=0";

// Query to set SendEmail to 1 where email contains a space
$updateEmailQuery = "UPDATE `emailupload` SET SendEmail = 1 WHERE email LIKE '% %' AND SendEmail=0";

// Execute the FirstName update query
if ($conn->query($updateFirstNameQuery) === TRUE) {
    echo "FirstName rows updated: " . $conn->affected_rows . "<br>";
} else {
    echo "Error updating FirstName: " . $conn->error . "<br>";
}

// Execute the LastName update query
if ($conn->query($updateLastNameQuery) === TRUE) {
    echo "LastName rows updated: " . $conn->affected_rows . "<br>";
} else {
    echo "Error updating LastName: " . $conn->error . "<br>";
}

// Execute the SendEmail update query
if ($conn->query($updateEmailQuery) === TRUE) {
    echo "SendEmail rows updated: " . $conn->affected_rows . "<br>";
} else {
    echo "Error updating SendEmail: " . $conn->error . "<br>";
}

// Close the database connection
$conn->close();
?>