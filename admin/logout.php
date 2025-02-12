<?php
session_start();

// Unset only admin-specific session variables
unset($_SESSION['a_id']);
unset($_SESSION['role']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out</title>
    <style>
        /* Background and text styling */
        body {
            background-color: #000; /* Dark background */
            color: #fff; /* Light text color for contrast */
        }

        /* Spinner animation (same as login page) */
        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3rem;
            border-style: solid;
            border-color: transparent;
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Fade-out effect */
        .fade-out {
            opacity: 0.5;
            transition: opacity 2s ease;
        }
    </style>
</head>
<body>
    <script>
        // Add fade-out effect and show loading spinner
        document.addEventListener("DOMContentLoaded", function() {
            // Create and display the loading spinner
            var spinner = document.createElement("div");
            spinner.className = "spinner-border text-light";
            document.body.appendChild(spinner);

            // Apply fade-out effect to the page
            document.body.classList.add("fade-out");

            // Redirect after 2 seconds
            setTimeout(function() {
                window.location.href = "../admin/login.php";
            }, 2000);
        });
    </script>
</body>
</html>