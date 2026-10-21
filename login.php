<?php

session_start();

require_once "config/database.php";

$error = "";


// If the admin is already logged in, send them directly to the admin dashboard
if (isset($_SESSION["admin_id"])) {
    header("Location: admin_dashboard.php");
    exit;
}


// Process the login form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";


    // Check that both fields contain something
    if ($username === "" || $password === "") {

        $error = "Please enter your username and password.";

    } else {

        // Look for the admin in the database
        $stmt = $pdo->prepare("
            SELECT admin_id, username, password_hash
            FROM admins
            WHERE username = :username
        ");

        $stmt->execute([
            "username" => $username
        ]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);


        // Check the entered password against the stored hash
        if (
            $admin &&
            password_verify(
                $password,
                $admin["password_hash"]
            )
        ) {

            // Give the logged in admin a new session ID
            session_regenerate_id(true);


            // Store information about the logged in admin
            $_SESSION["admin_id"] =
                $admin["admin_id"];

            $_SESSION["admin_username"] =
                $admin["username"];


            // Send the admin to the dashboard
            header("Location: admin_dashboard.php");
            exit;

        } else {

            // if incorrect username or password 
            $error = "Invalid username or password.";
        }
    }
}

?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Login | Brewing Cafe</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 40px 3px;
            font-family: Georgia, "Times New Roman", serif;
            background: #ffffff;
            color: #171310;
        }

        .page {
            min-height: 480px;
            background: #fff8f0;
            padding: 39px 8px 10px;
        }

        .header {
            background: #6b5145;
            color: white;
            text-align: center;
            font-size: 22px;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .login-box {
            background: #ddd2ca;
            width: 100%;
            max-width: 372px;
            min-height: 300px;
            padding: 20px;
            margin: 0 auto;
            border-radius: 8px;
        }

        .field-label {
            display: block;
            font-size: 18px;
            margin: 0 8px 4px;
        }

        .input-box {
            display: flex;
            align-items: center;
            background: #c1b3a9;
            border-radius: 9px;
            height: 40px;
            padding: 0 10px;
            margin-bottom: 13px;
        }

        .input-box .symbol {
            font-size: 23px;
            margin-right: 7px;
        }

        .input-box input {
            min-width: 0;
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
            font-family: inherit;
            font-size: 14px;
            color: #171310;
        }

        .input-box input::placeholder {
            color: #30251f;
            opacity: 1;
        }

        .show-password {
            border: none;
            background: transparent;
            cursor: pointer;
            font-family: inherit;
            font-size: 10px;
            white-space: nowrap;
        }

        .forgot {
            display: block;
            text-align: right;
            color: #171310;
            text-decoration: none;
            font-size: 14px;
            margin-top: -5px;
            margin-bottom: 21px;
        }

        .forgot:hover {
            text-decoration: underline;
        }

        .continue-btn {
            display: block;
            margin: 0 auto;
            padding: 8px 15px;
            border: none;
            border-radius: 9px;
            background: #a18d81;
            color: #171310;
            font-family: inherit;
            font-size: 14px;
            cursor: pointer;
        }

        .continue-btn:hover {
            background: #8f786b;
            color: white;
        }

        .error {
            background: #f8e1dc;
            color: #8b2419;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 5px;
            font-size: 13px;
            text-align: center;
        }

        @media (max-width: 450px) {
            body {
                padding-top: 15px;
            }

            .page {
                padding-top: 25px;
            }

            .login-box {
                padding-left: 15px;
                padding-right: 15px;
            }
        }
    </style>
</head>

<body>

<header class="site-header">
    <a class="logo" href="index.php">☕ Brewing Cafe</a>

	<nav class="navbar">
	    <a href="index.php">Home</a>
	    <a href="menu.php">Menu</a>
	    <a href="checkout.php">Checkout</a>
	    <a href="login.php" class="active">Admin Login</a>
	</nav>
</header>

<div class="page">

    <div class="header">Admin Login</div>

    <div class="login-box">

        <form method="POST" action="login.php">

            <label class="field-label" for="username">
                Username
            </label>

            <div class="input-box">
                <span class="symbol" aria-hidden="true">♙</span>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="USERNAME"
                    autocomplete="username"
                    required
                >
            </div>

            <label class="field-label" for="password">
                Password
            </label>

            <div class="input-box">
                <span class="symbol" aria-hidden="true">♙</span>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="PASSWORD"
                    autocomplete="current-password"
                    required
                >

                <button
                    type="button"
                    class="show-password"
                    onclick="togglePassword()"
                    id="showBtn"
                >
                    SHOW
                </button>
            </div>

            <a
                href="mailto:brewingcafe@example.com?subject=Password%20Reset%20Request&body=Hello%20Brewing%20Cafe%20team,%0D%0A%0D%0AI%20forgot%20my%20admin%20password%20and%20would%20like%20help%20resetting%20it.%0D%0A%0D%0AMy%20details:%0D%0AName:%20%0D%0AUsername:%20%0D%0AEmail:%20%0D%0A%0D%0AThank%20you."
                class="forgot"
            >
                Forgot password?
            </a>

            <?php if ($error !== ""): ?>
                <div class="error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <button type="submit" class="continue-btn">
                Continue
            </button>

        </form>

    </div>
</div>

<script>
    function togglePassword() {
        const password = document.getElementById("password");
        const button = document.getElementById("showBtn");

        if (password.type === "password") {
            password.type = "text";
            button.textContent = "HIDE";
        } else {
            password.type = "password";
            button.textContent = "SHOW";
        }
    }
</script>

</body>
</html>