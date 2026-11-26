<?php
require_once "db.php";

$email = "priya2@example.com";
$newPassword = "Priya@123";

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE users SET password = ? WHERE email = ? AND role = 'instructor'"
);

$stmt->bind_param("ss", $hashedPassword, $email);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Password reset successfully!";
} else {
    echo "Password was not changed. Check the email and instructor role.";
}

$stmt->close();
$conn->close();
?>