<?php
session_start();

require_once '../settings/connection.php';

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

header('Content-Type: application/json');

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {
        $username = sanitize_input($_POST["username"]);
        $email = sanitize_input($_POST["email"]);
        $password = sanitize_input($_POST["password"]);
        $confirm_password = sanitize_input($_POST["confirm_password"]);

        $errors = [];

        if (empty($username)) {
            $errors[] = "Username is required.";
        }
        if (empty($email)) {
            $errors[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (empty($password)) {
            $errors[] = "Password is required.";
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{6,}$/', $password)) {
            $errors[] = "Password must contain at least 6 characters, including at least one uppercase letter, one number, and one special character.";
        }
        if (empty($confirm_password)) {
            $errors[] = "Confirm password is required.";
        } elseif ($password !== $confirm_password) {
            $errors[] = "Passwords do not match.";
        }

        if (empty($errors)) {
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            try {
                $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
                $stmt->bindValue(':username', $username);
                $stmt->bindValue(':email', $email);
                $stmt->bindValue(':password_hash', $password_hash);

                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = "Registration successful. Please log in.";
                } else {
                    $errors[] = "Registration failed. Please try again.";
                }
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $response['success'] = false;
            $response['errors'] = $errors;
        }
    } else {
        $response['success'] = false;
        $response['errors'] = ["All fields are required."];
    }
} else {
    $response['success'] = false;
    $response['errors'] = ["Invalid request method."];
}

echo json_encode($response);
?>
