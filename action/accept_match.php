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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $match_id = $_POST['match_id'];

    // Check if match_id is provided
    if (empty($match_id)) {
        echo 'Match ID is required.';
        exit();
    }

    // Update the match status to 'accepted'
    $sql_update = "UPDATE Matches SET status = 'accepted' WHERE match_id = :match_id";
    try {
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bindParam(':match_id', $match_id, PDO::PARAM_STR); // Assuming match_id is a string
        $stmt_update->execute();

        // Redirect back to the messages page or another confirmation page
        header('Location: ../view/pages/messages.php?message=match_accepted');
        exit();
    } catch (PDOException $e) {
        echo 'Error updating match status: ' . $e->getMessage();
        exit();
    }
} else {
    echo 'Invalid request method.';
}
?>

