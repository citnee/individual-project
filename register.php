<?php
session_start();
$message = "";
$messageType = "";

function loadUsers() {
    if (!file_exists("users.txt")) return [];
    $users = [];
    foreach (file("users.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $parts = explode(":", $line, 2);
        if (count($parts) == 2) $users[$parts[0]] = $parts[1];
    }
    return $users;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm  = trim($_POST["confirm_password"]);
    $users    = loadUsers();

    if (strlen($username) < 3 || strlen($username) > 20) {
        $message = "Username must be 3-20 characters."; $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters."; $messageType = "error";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match."; $messageType = "error";
    } elseif (isset($users[$username])) {
        $message = "Username already taken."; $messageType = "error";
    } else {
        file_put_contents("users.txt", $username . ":" . password_hash($password, PASSWORD_DEFAULT) . "\n", FILE_APPEND | LOCK_EX);
        $message = "Account created! You can now log in."; $messageType = "success";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $users    = loadUsers();

    if (empty($username) || empty($password)) {
        $message = "Please fill in all fields."; $messageType = "error";
    } elseif (!isset($users[$username]) || !password_verify($password, $users[$username])) {
        $message = "Invalid username or password."; $messageType = "error";
    } else {
        $_SESSION["user"] = $username;
        $message = "Welcome back, " . htmlspecialchars($username) . "!"; $messageType = "success";
    }
}

if (isset($_GET["logout"])) { session_destroy(); header("Location: register.php"); exit(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="enter-style.css">
    <title>Register / Login</title>
    <style>
        .message { padding: 10px; margin-bottom: 15px; border-radius: 5px; }
        .success { background-color: #2a5c2a; color: #90ee90; }
        .error   { background-color: #5c2a2a; color: #ff9090; }
    </style>
</head>
<body>
<?php if (isset($_SESSION["user"])): ?>
    <h2>Welcome, <?= htmlspecialchars($_SESSION["user"]) ?>!</h2>
    <a href="register.php?logout=1">Logout</a>
<?php else: ?>
    <?php if (!empty($message)): ?>
        <div class="message <?= $messageType ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <h2>Register</h2>
    <form method="POST" action="">
        <input type="text"     name="username"         placeholder="Username" required>
        <input type="password" name="password"         placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        <button type="submit"  name="register">Register</button>
    </form>
    <h2>Login</h2>
    <form method="POST" action="">
        <input type="text"     name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit"  name="login">Login</button>
    </form>
<?php endif; ?>
</body>
</html>