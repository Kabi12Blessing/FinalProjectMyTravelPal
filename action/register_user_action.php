<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Including database connection file
require_once '../settings/connection.php';

// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username, email, password, and confirm_password are set
    if (isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {
        // Get form data
        $username = sanitize_input($_POST["username"]);
        $email = sanitize_input($_POST["email"]);
        $password = sanitize_input($_POST["password"]);
        $confirm_password = sanitize_input($_POST["confirm_password"]);

        // Initialize error messages array
        $errors = [];

        // Validate form data
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

        // If there are no errors, proceed with user registration
        if (empty($errors)) {
            // Hash the password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

            // Prepare and execute query to insert user data
            $stmt = $conn->prepare("INSERT INTO Users (username, email, password_hash) VALUES (:username, :email, :password_hash)");
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':password_hash', $password_hash);

            if ($stmt->execute()) {
                // Registration successful, redirect to login page with success message
                $_SESSION['success'] = "Registration successful. Please log in.";
                header("Location: ../login/login_view.php");
                exit();
            } else {
                $errors[] = "Registration failed. Please try again.";
            }
        }

        // If there are errors, store them in session and redirect back to form
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = ['username' => $username, 'email' => $email];
            header("Location: ../login/register_view.php");
            exit();
        }
    } else {
        $_SESSION['errors'] = ["All fields are required."];
        header("Location: ../login/register_view.php");
        exit();
    }
}
?>
