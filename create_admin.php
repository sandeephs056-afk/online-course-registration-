<?php
require_once "db.php";

$email = "admin@example.com";
$newPassword = "Admin@123";
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE users SET password = ? WHERE email = ? AND role = 'admin'"
);

$stmt->bind_param("ss", $hashedPassword, $email);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo "Admin password updated successfully!";
    } else {
        echo "No password change was made. Check the admin email and role.";
    }
} else {
    echo "Error updating password.";
}

$stmt->close();
$conn->close();
?>