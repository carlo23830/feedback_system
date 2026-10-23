
<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

require_once "db.php";

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$subject = trim($_POST["subject"] ?? "");
$message = trim($_POST["message"] ?? "");

$valid = (
    $name !== "" &&
    $email !== "" &&
    $subject !== "" &&
    $message !== "" &&
    strlen($name) <= 100 &&
    strlen($email) <= 150 &&
    strlen($subject) <= 150 &&
    strlen($message) <= 5000 &&
    filter_var($email, FILTER_VALIDATE_EMAIL)
);

if (!$valid) {
    $conn->close();
    header("Location: index.php?status=error");
    exit;
}

$stmt = $conn->prepare(
    "INSERT INTO feedback (name, email, subject, message)
     VALUES (?, ?, ?, ?)"
);

if (!$stmt) {
    error_log("Could not prepare feedback insert.");
    $conn->close();
    header("Location: index.php?status=error");
    exit;
}

$stmt->bind_param("ssss", $name, $email, $subject, $message);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: index.php?status=success");
    exit;
}

error_log("Feedback insert failed.");
$stmt->close();
$conn->close();

header("Location: index.php?status=error");
exit;

?>
