<?php
// Check if the form was actually submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Capture the data from the form inputs
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password']; 

    // 2. Format the text for the file
    $userData = "Username: " . $username . " | Email: " . $email . " | Password: " . $password . "\n";

    // 3. Save it to 'users.txt' (FILE_APPEND keeps old data and adds new data to the bottom)
    file_put_contents("users.txt", $userData, FILE_APPEND);

    // 4. Redirect instantly back to your main shapes page
    header("Location: index.html");
    exit();
}
?>