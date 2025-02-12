<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>
    <link href="../css/styles.css" rel="stylesheet" />
    <style>
        /* Add CSS for the loading spinner */
        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3rem;
            border-style: solid;
            border-color: transparent;
            border-top-color: #fff;
            border-radius: 50%;
            position: relative; /* Add relative positioning */
        }

        .spinner-border::before {
            content: attr(data-percentage) '%'; /* Display the percentage */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: #fff;
            font-size: 1rem;
            animation: none; /* Prevent the percentage from spinning */
        }

        /* Remove the spin animation */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(0deg);
            }
        }

        /* Add CSS for the fade-out effect */
        .fade-out {
            opacity: 0.5;
            transition: opacity 2s ease;
        }
    </style>
</head>
<body class="bg-black">
    <?php
    session_start();
    if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
        $email = $_COOKIE['email'];
        $password = $_COOKIE['password'];
    } else {
        $email = '';
        $password = '';
    }
    ?>
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container mt-5">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5 mb-2 bg-dark">
                                <div class="card-header border-bottom border-1 border-warning">
                                    <h3 class="text-center text-light font-weight-light mt-2 mb-4">Admin Login</h3>
                                    <!-- Display error message if exists -->
                                    <?php if (isset($_GET['error'])): ?>
                                        <div id="error-alert" class="alert alert-danger text-center my-2" role="alert">
                                            <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body bg-dark">
                                    <form action="../db/adminlogin_conn.php" method="post">
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputEmail" type="email" name="email"
                                                placeholder="name@example.com" value="<?php echo htmlspecialchars($email); ?>" required />
                                            <label for="inputEmail">Email address:</label>
                                        </div>
                                        <div class="form-floating mb-3">
                                            <input class="form-control" id="inputPassword" type="password" name="password" placeholder="Password" value="<?php echo htmlspecialchars($password); ?>" required />
                                            <label for="inputPassword">Password:</label>
                                            <button type="button" class="btn position-absolute top-50 end-0 translate-middle-y me-2" id="togglePassword">
                                                <i class="fas fa-eye"></i> <!-- Default icon (eye) -->
                                            </button>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center mt-1 mb-2">
                                            <div class="d-flex align-items-center">
                                                <input class="form-check-input" id="inputRememberPassword" type="checkbox" name="remember" value="" />
                                                <label class="form-check-label text-light ms-2" for="inputRememberPassword">Remember Password</label>
                                            </div>
                                            <a class="small text-info" href="../admin/forgot_pass.php">Forgot Password?</a>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-2 mb-2">
                                            <button type="submit" class="btn btn-primary w-100">Login</button>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-center mt-3 mb-0"> <a class="btn border-secondary w-100 text-light border border-1" href="../index.php">Back</a></div>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center border-top border-1 border-warning">
                                    <div class="text-center text-muted">Human Resource 2</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-dark mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Human Resource 2</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                            &middot;
                            <a href="#">Terms &amp; Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
<script>
    // Automatically hide the error alert after 10 seconds (10,000 milliseconds)
    setTimeout(function() {
        var errorAlertElement = document.getElementById('error-alert');
        if (errorAlertElement) {
            errorAlertElement.style.transition = "opacity 1s ease"; // Smooth fade-out
            errorAlertElement.style.opacity = 0; // Set the opacity to 0 (fade out)

            setTimeout(function() {
                errorAlertElement.remove(); // Remove the element from the DOM after fade-out
            }, 1000); // Wait 1 second after fade-out to remove the element completely
        }
    }, 10000); // 10 seconds delay

    const togglePassword = document.querySelector("#togglePassword");
    const passwordField = document.querySelector("#inputPassword");
    const icon = togglePassword.querySelector("i");

    togglePassword.addEventListener("click", function () {
        // Toggle the password field type
        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type", type);

        // Toggle the eye/eye-slash icon
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
    });

    // Add event listener to the login form
    document.querySelector("form").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Add animation or loading effect here
        document.body.classList.add("fade-out");

        // Create and display the loading spinner
        var spinner = document.createElement('div');
        spinner.className = 'spinner-border text-light';
        spinner.style.position = 'fixed';
        spinner.style.top = '50%';
        spinner.style.left = '50%';
        spinner.style.transform = 'translate(-50%, -50%)';
        spinner.setAttribute('data-percentage', '0'); // Initialize percentage
        document.body.appendChild(spinner);

        // Update the loading percentage
        var percentage = 0;
        var interval = setInterval(function() {
            percentage += 10;
            spinner.setAttribute('data-percentage', percentage);
            if (percentage >= 100) {
                clearInterval(interval);
            }
        }, 200);

        if (document.getElementById('inputRememberPassword').checked) {
            document.cookie = "email=" + document.getElementById('inputEmail').value + "; max-age=31536000; path=/";
            document.cookie = "password=" + document.getElementById('inputPassword').value + "; max-age=31536000; path=/";
        } else {
            document.cookie = "email=" + document.getElementById('inputEmail').value + "; max-age=31536000; path=/";
            document.cookie = "password=; max-age=0; path=/";
        }

        // Redirect to the admin dashboard after 2 seconds
        setTimeout(function() {
            event.target.submit(); // Submit the form after the delay
        }, 2000);
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>