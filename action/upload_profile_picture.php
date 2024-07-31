<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../settings/connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$uploadDir = '../uploads/';
$uploadStatus = false;
$imageFilePath = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $userId = $_SESSION['user_id'];

    // Create directory if it doesn't exist and ensure permissions are correct
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            die('Failed to create upload directory.');
        }
    }

    // Ensure the upload directory is writable
    if (!is_writable($uploadDir)) {
        die('Upload directory is not writable. Please check permissions.');
    }

    // Set umask to ensure files are created with the correct permissions
    umask(0022);

    $imageFileName = basename($_FILES['profile_picture']['name']);
    $imageFilePath = $uploadDir . $imageFileName;
    $imageFileType = strtolower(pathinfo($imageFilePath, PATHINFO_EXTENSION));

    // Validate the uploaded file
    $check = getimagesize($_FILES['profile_picture']['tmp_name']);
    if ($check === false) {
        die('File is not an image.');
    }

    if ($_FILES['profile_picture']['size'] > 5000000) {
        die('Sorry, your file is too large.');
    }

    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
    if (!in_array($imageFileType, $allowed_extensions)) {
        die('Sorry, only JPG, JPEG, PNG & GIF files are allowed.');
    }

    // Remove old file if it exists
    foreach (glob($uploadDir . $userId . '.*') as $file) {
        if (file_exists($file)) {
            if (!unlink($file)) {
                die('Error removing old profile picture.');
            }
        }
    }

    // Move the uploaded file to the target location
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $imageFilePath)) {
        $uploadStatus = true;
    } else {
        die("Sorry, there was an error uploading your file.");
    }

    // Update database record if upload is successful
    if ($uploadStatus) {
        $updateSql = "UPDATE Users SET profile_picture = :profile_picture WHERE user_id = :user_id";
        try {
            $stmt = $conn->prepare($updateSql);
            $stmt->bindParam(':profile_picture', $imageFilePath);
            $stmt->bindParam(':user_id', $userId);

            if ($stmt->execute()) {
                header('Location: ../view/pages/Dashboard.php?success=profile_picture_updated');
                exit();
            } else {
                $errorInfo = $stmt->errorInfo();
                echo 'Database update failed. Error: ' . $errorInfo[2] . "<br>";
                exit();
            }
        } catch (PDOException $e) {
            echo 'Query failed: ' . $e->getMessage();
            exit();
        }
    } else {
        echo "Failed to upload profile picture.";
    }
} else {
    die('Invalid request.');
}
?>
