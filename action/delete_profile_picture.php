<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../settings/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in.']);
    exit();
}

$userId = $_SESSION['user_id'];
$uploadDir = '../../uploads/profile_pictures/';

try {
    // Find and delete the user's profile picture
    foreach (glob($uploadDir . $userId . '.*') as $file) {
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // Update the database to remove the profile picture path
    $sql = "UPDATE Users SET profile_picture = NULL WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Profile picture deleted successfully.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $e->getMessage()]);
}
exit();
?>
