<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TravelPal</title>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://rawcdn.githack.com/BlessingLeslie/TravelPalImages/f3fa5a30650b6390c7e41a26ff7550836e3a6f24/DALL%C2%B7E%202024-06-11%2000.17.43%20-%20A%20highly%20realistic%20airport%20scene%20featuring%20two%20Black%20people.%20One%20person%20is%20sharing%20an%20item%20with%20another%20traveler%20who%20has%20extra%20luggage.%20The%20traveler%20i.webp');
            background-size: cover;
            background-position: center;
            filter: brightness(0.5); 
            z-index: -1;
        }

        .hero-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7); 
            z-index: -1;
        }

        .modal {
            display: block;
            position: relative;
            z-index: 1001;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 10px;
            position: relative;
            z-index: 2;
        }

        .modal-header, .modal-footer {
            padding: 10px;
            text-align: center;
        }

        .modal-header {
            background-color: #333;
            color: white;
        }

        .modal-footer {
            background-color: #f1f1f1;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        .modal-body input[type="text"],
        .modal-body input[type="email"],
        .modal-body input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px 0;
            display: inline-block;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .modal-body button {
            background-color: #007BFF;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 5px;
        }

        .modal-body button:hover {
            background-color: #0056b3;
        }

        .modal-body label {
            display: flex;
            align-items: center;
        }

        .modal-body input[type="checkbox"] {
            margin-right: 10px;
        }

        .login-link {
            margin-top: 20px;
            text-align: center;
        }

        .login-link a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="hero-overlay"></div>
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>TravelPal Register</h2>
            </div>
            <div class="modal-body">
                <?php
                if (isset($_SESSION['errors'])) {
                    echo '<div class="error-message">';
                    foreach ($_SESSION['errors'] as $error) {
                        echo "<p>$error</p>";
                    }
                    echo '</div>';
                    unset($_SESSION['errors']); // Clear errors after displaying
                }

                $username = $_SESSION['form_data']['username'] ?? '';
                $email = $_SESSION['form_data']['email'] ?? '';
                unset($_SESSION['form_data']); // Clear form data after using
                ?>
                <form action="../action/register_user_action.php" method="post">
                    <input type="text" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username); ?>">
                    <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($email); ?>">
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                    <button type="submit">REGISTER</button>
                    <label>
                        <input type="checkbox" required> I agree to the <a style="margin-left:5px;" href="#">Terms and Conditions</a>
                    </label>
                </form>
                <div class="login-link">
                    <p>Already have an account? <a href="login_view.php">Login here</a></p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#">Need help?</a>
            </div>
        </div>
    </div>
    <script>
        // Get the modal
        var modal = document.getElementById("registerModal");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal too, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
