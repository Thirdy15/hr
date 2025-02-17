<?php
// Start session and check admin login
session_start();
if (!isset($_SESSION['a_id'])) {
    header("Location: ../admin/login.php");
    exit();
}

// Include database connection
include '../db/db_conn.php';

// Fetch user info
$adminId = $_SESSION['a_id'];
$sql = "SELECT a_id, firstname, middlename, lastname, birthdate, email, role, position, department, phone_number, address, pfp FROM admin_register WHERE a_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $adminId);
$stmt->execute();
$result = $stmt->get_result();
$adminInfo = $result->fetch_assoc();

function calculateProgressCircle($averageScore) {
    return ($averageScore / 10) * 100;
}

function getTopEmployeesByCriterion($conn, $criterion, $criterionLabel, $index) {
    // SQL query to fetch the highest average score for each employee
    $sql = "SELECT e.e_id, e.firstname, e.lastname, e.department, e.pfp, e.email, 
                   AVG(ae.$criterion) AS avg_score,
                   AVG(ae.quality) AS avg_quality,
                   AVG(ae.communication_skills) AS avg_communication,
                   AVG(ae.teamwork) AS avg_teamwork,
                   AVG(ae.punctuality) AS avg_punctuality,
                   AVG(ae.initiative) AS avg_initiative
            FROM employee_register e
            JOIN admin_evaluations ae ON e.e_id = ae.e_id
            GROUP BY e.e_id
            ORDER BY avg_score DESC
            LIMIT 1";  // Select the top employee with the highest average score

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    // Path to the default profile picture
    $defaultPfpPath = '../img/defaultpfp.jpg'; // Update this path to your actual default profile picture location
    $defaultPfp = base64_encode(file_get_contents($defaultPfpPath));

    // Output the awardee's information for each criterion
    echo "<div class='category' id='category-$index' style='display: none;'>";
    echo "<h3 class='text-center mt-4'>$criterionLabel</h3>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Check if profile picture exists, else use the default picture
            if (file_exists($row['pfp']) && !empty($row['pfp'])) {
                $pfp = base64_encode(file_get_contents($row['pfp']));
            } else {
                $pfp = $defaultPfp;
            }

            // Calculate percentage for the progress circle
            $scorePercentage = calculateProgressCircle($row['avg_score']);
            
            echo "<div class='employee-card'>";
            echo "<div class='metrics-container'>";
            
            // Left metrics
            echo "<div class='metrics-column'>";
            echo "<div class='metric-box fade-in'>";
            echo "<span class='metric-label'>Quality of Work</span>";
            echo "<span class='metric-value'>" . round($row['avg_quality'], 2) . "</span>";
            echo "</div>";
            
            echo "<div class='metric-box fade-in' style='animation-delay: 0.2s;'>";
            echo "<span class='metric-label'>Communication Skills</span>";
            echo "<span class='metric-value'>" . round($row['avg_communication'], 2) . "</span>";
            echo "</div>";
            echo "</div>";

            // Center profile section
            echo "<div class='profile-section'>";
            echo "<div class='progress-circle-container'>";
            echo "<div class='progress-circle' data-progress='" . $scorePercentage . "'>";
            echo "<div class='profile-image-container'>";
            if (!empty($pfp)) {
                echo "<img src='data:image/jpeg;base64,$pfp' alt='Profile Picture' class='profile-image'>";
            }
            echo "</div>";
            echo "</div>";
            echo "</div>";
            
            echo "<div class='profile-info'>";
            echo "<h2 class='admin-name'>" . htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) . "</h2>";
            echo "<p class='department-name'>" . htmlspecialchars($row['department']) . "</p>";
            echo "</div>";
            echo "<div class='admin-id fade-in' style='animation-delay: 0.8s;'>";
            echo "Employee ID: " . htmlspecialchars($row['e_id']);
            echo "</div>";

            // New metric box below admin ID
            echo "<div class='metric-box fade-in' style='animation-delay: 0.8s;'>";
            echo "<span class='metric-label'>Initiative</span>";
            echo "<span class='metric-value'>" . round($row['avg_initiative'], 2) . "</span>";
            echo "</div>";

            // Add buttons for comments and reactions
            echo "<div class='comment-reaction-buttons'>";
            echo "  <div class='reactions text-start mt-4'>";
            echo "      <div class='reaction-button'>";
            echo "          <button class='btn btn-outline-primary' title='Like' onmouseover=\"showPopup('👍', 'like-popup')\" onmouseout=\"hidePopup('like-popup')\" onclick=\"react('like')\">";
            echo "              👍 <span id='like-count'>2</span>";
            echo "          </button>";
            echo "          <span class='popup-emoji' id='like-popup'>👍</span>";
            echo "      </div>";
            echo "      <div class='reaction-button'>";
            echo "          <button class='btn btn-outline-primary' title='Love' onmouseover=\"showPopup('❤️', 'love-popup')\" onmouseout=\"hidePopup('love-popup')\" onclick=\"react('love')\">";
            echo "              ❤️ <span id='love-count'>3</span>";
            echo "          </button>";
            echo "          <span class='popup-emoji' id='love-popup'>❤️</span>";
            echo "      </div>";
            echo "      <div class='reaction-button'>";
            echo "          <button class='btn btn-outline-primary' title='Wow' onmouseover=\"showPopup('😮', 'wow-popup')\" onmouseout=\"hidePopup('wow-popup')\" onclick=\"react('wow')\">";
            echo "              😮 <span id='wow-count'>1</span>";
            echo "          </button>";
            echo "          <span class='popup-emoji' id='wow-popup'>😮</span>";
            echo "      </div>";
            echo "      <div class='reaction-button'>";
            echo "          <button class='btn btn-outline-primary' title='Awesome' onmouseover=\"showPopup('😎', 'awesome-popup')\" onmouseout=\"hidePopup('awesome-popup')\" onclick=\"react('awesome')\">";
            echo "              😎 <span id='awesome-count'>2</span>";
            echo "          </button>";
            echo "          <span class='popup-emoji' id='awesome-popup'>😎</span>";
            echo "      </div>";
            echo "  </div>";
            echo "</div>";
            echo "<div class='text-center mt-2'>";
            echo "  <button class='btn btn-primary' onclick='openCommentModal()'>Write a Comment</button>";
            echo "</div>";

            echo "</div>"; // End profile-section

            // Right metrics
            echo "<div class='metrics-column'>";
            echo "<div class='metric-box fade-in' style='animation-delay: 0.4s;'>";
            echo "<span class='metric-label'>Teamwork</span>";
            echo "<span class='metric-value'>" . round($row['avg_teamwork'], 2) . "</span>";
            echo "</div>";
            
            echo "<div class='metric-box fade-in' style='animation-delay: 0.6s;'>";
            echo "<span class='metric-label'>Punctuality</span>";
            echo "<span class='metric-value'>" . round($row['avg_punctuality'], 2) . "</span>";
            echo "</div>";
            echo "</div>";

            echo "</div>"; // End metrics-container
            echo "</div>"; // End admin-card
        }
    } else {
        echo "<p class='text-center'>No outstanding admins found for $criterionLabel.</p>";
    }

    echo "</div>"; // End of category
    $stmt->close();
}


// Function to get the current reactions from the database
function getReactions($conn) {
    $sql = "SELECT reaction, COUNT(*) as count FROM reactions GROUP BY reaction";
    $result = $conn->query($sql);
    $reactions = [];
    while ($row = $result->fetch_assoc()) {
        $reactions[$row['reaction']] = $row['count'];
    }
    return $reactions;
}

// Function to save a reaction to the database
function saveReaction($conn, $reaction) {
    $sql = "INSERT INTO reactions (reaction) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $reaction);
    $stmt->execute();
    $stmt->close();
}

// Handle reaction submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reaction'])) {
    saveReaction($conn, $_POST['reaction']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$reactions = getReactions($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Outstanding Admins</title>
    <link href="../css/styles.css" rel="stylesheet" />
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link href="../css/calendar.css" rel="stylesheet"/>
    <link href="../css/awardee.css" rel="stylesheet"/>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="sb-nav-fixed bg-black">
    <nav class="sb-topnav navbar navbar-expand navbar-dark border-bottom border-1 border-secondary bg-dark">
        <a class="navbar-brand ps-3 text-muted" href="../admin/dashboard.php" style="font-size: 18px;">Microfinance</a>
        <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars text-light"></i></button>
        <div class="d-flex ms-auto me-0 me-md-3 my-2 my-md-0 align-items-center">
            <div class="text-light me-3 p-2 rounded shadow-sm bg-gradient" id="currentTimeContainer" 
                style="background: linear-gradient(45deg, #333333, #444444); border-radius: 5px; font-size: 14px;">
                <span class="d-flex align-items-center">
                    <span class="pe-2">
                        <i class="fas fa-clock"></i> 
                        <span id="currentTime">00:00:00</span>
                    </span>
                    <button class="btn btn-outline-warning btn-sm ms-2" type="button" onclick="toggleCalendar()" style="font-size: 14px;">
                        <i class="fas fa-calendar-alt"></i>
                        <span id="currentDate">00/00/0000</span>
                    </button>
                </span>
            </div>
            <form class="d-none d-md-inline-block form-inline">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" style="font-size: 14px;" />
                    <button class="btn btn-warning" id="btnNavbarSearch" type="button" style="font-size: 14px;"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion bg-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu ">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading text-center text-muted">Your Profile</div>
                        <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                            <li class="nav-item dropdown text">
                                <a class="nav-link dropdown-toggle text-light d-flex justify-content-center ms-4" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo (!empty($adminInfo['pfp']) && $adminInfo['pfp'] !== 'defaultpfp.jpg') 
                                        ? htmlspecialchars($adminInfo['pfp']) 
                                        : '../img/defaultpfp.jpg'; ?>" 
                                        class="rounded-circle border border-light" width="120" height="120" alt="Profile Picture" />
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="../admin/profile.php">Profile</a></li>
                                    <li><a class="dropdown-item" href="#!">Settings</a></li>
                                    <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                                    <li><hr class="dropdown-divider" /></li>
                                    <li><a class="dropdown-item" href="../admin/logout.php">Logout</a></li>
                                </ul>
                            </li>
                            <li class="nav-item text-light d-flex ms-3 flex-column align-items-center text-center">
                                <span class="big text-light mb-1">
                                    <?php
                                        if ($adminInfo) {
                                        echo htmlspecialchars($adminInfo['firstname'] . ' ' . $adminInfo['middlename'] . ' ' . $adminInfo['lastname']);
                                        } else {
                                        echo "Admin information not available.";
                                        }
                                    ?>
                                </span>      
                                <span class="big text-light">
                                    <?php
                                        if ($adminInfo) {
                                        echo htmlspecialchars($adminInfo['role']);
                                        } else {
                                        echo "User information not available.";
                                        }
                                    ?>
                                </span>
                            </li>
                        </ul>
                        <div class="sb-sidenav-menu-heading text-center text-muted border-top border-1 border-secondary mt-3">Admin Dashboard</div>
                        <a class="nav-link text-light" href="../admin/dashboard.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>
                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapseTAD" aria-expanded="false" aria-controls="collapseTAD">
                            <div class="sb-nav-link-icon"><i class="fa fa-address-card"></i></div>
                            Time and Attendance
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseTAD" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link text-light" href="../admin/attendance.php">Attendance</a>
                                <a class="nav-link text-light" href="../admin/timesheet.php">Timesheet</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLM" aria-expanded="false" aria-controls="collapseLM">
                            <div class="sb-nav-link-icon"><i class="fas fa-calendar-times"></i></div>
                            Leave Management
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLM" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link text-light" href="../admin/leave_requests.php">Leave Requests</a>
                                <a class="nav-link text-light" href="../admin/leave_history.php">Leave History</a>
                                <a class="nav-link text-light"  href="../admin/leave_allocation.php">Set Leave</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePM" aria-expanded="false" aria-controls="collapsePM">
                            <div class="sb-nav-link-icon"><i class="fas fa-line-chart"></i></div>
                            Performance Management
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapsePM" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link text-light" href="../admin/evaluation.php">Evaluation</a>
                            </nav>
                        </div>
                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSR" aria-expanded="false" aria-controls="collapseSR">
                            <div class="sb-nav-link-icon"><i class="fa fa-address-card"></i></div>
                            Social Recognition
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseSR" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link text-light" href="../admin/awardee.php">Awardee</a>
                                <a class="nav-link text-light" href="../admin/recognition.php">Generate Certificate</a>
                            </nav>
                        </div>
                        <div class="sb-sidenav-menu-heading text-center text-muted border-top border-1 border-secondary">Account Management</div>
                        <a class="nav-link collapsed text-light" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                            <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                            Accounts
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link text-light" href="../admin/calendar.php">Calendar</a>
                                <a class="nav-link text-light" href="../admin/admin.php">Admin Accounts</a>
                                <a class="nav-link text-light" href="../admin/employee.php">Employee Accounts</a>
                            </nav>
                        </div>
                        <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                        </div>
                    </div>
                </div>
                <div class="sb-sidenav-footer bg-black text-light border-top border-1 border-secondary">
                    <div class="small">Logged in as: <?php echo htmlspecialchars($adminInfo['role']); ?></div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main class="container-fluid position-relative bg-black">
                <div class="container" id="calendarContainer" 
                    style="position: fixed; top: 9%; right: 0; z-index: 1050; 
                    width: 700px; display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="calendar" class="p-2"></div>
                        </div>
                    </div>
                </div>   
                <h1 class="mb-2 text-light ms-2">Outstanding Admins by Evaluation Criteria</h1>            
                <div class="container text-light">

                <?php getTopEmployeesByCriterion($conn, 'quality', 'Quality of Work', 1); ?>
                <?php getTopEmployeesByCriterion($conn, 'communication_skills', 'Communication Skills', 2); ?>
                <?php getTopEmployeesByCriterion($conn, 'teamwork', 'Teamwork', 3); ?>
                <?php getTopEmployeesByCriterion($conn, 'punctuality', 'Punctuality', 4); ?>
                <?php getTopEmployeesByCriterion($conn, 'initiative', 'Initiative', 5); ?>

                    <!-- Navigation buttons for manually controlling the categories -->
                    <div class="text-center">
                        <button class="btn btn-primary" onclick="showPreviousCategory()">Previous</button>
                        <button class="btn btn-primary" onclick="showNextCategory()">Next</button>
                    </div>
                </div>
            </main>
            
            <footer class="py-4 bg-dark text-light mt-auto border-top border-secondary">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Your Website 2024</div>
                        <div>
                            <a href="#">Privacy Policy</a>
                             &middot;
                            <a href="#">Terms & Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
                //CALENDAR 
                let calendar;
            function toggleCalendar() {
                const calendarContainer = document.getElementById('calendarContainer');
                    if (calendarContainer.style.display === 'none' || calendarContainer.style.display === '') {
                        calendarContainer.style.display = 'block';
                        if (!calendar) {
                            initializeCalendar();
                         }
                        } else {
                            calendarContainer.style.display = 'none';
                        }
            }

            function initializeCalendar() {
                const calendarEl = document.getElementById('calendar');
                    calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                        },
                        height: 440,  
                        events: {
                        url: '../db/holiday.php',  
                        method: 'GET',
                        failure: function() {
                        alert('There was an error fetching events!');
                        }
                        }
                    });

                    calendar.render();
            }

            document.addEventListener('DOMContentLoaded', function () {
                const currentDateElement = document.getElementById('currentDate');
                const currentDate = new Date().toLocaleDateString(); 
                currentDateElement.textContent = currentDate; 
            });

            document.addEventListener('click', function(event) {
                const calendarContainer = document.getElementById('calendarContainer');
                const calendarButton = document.querySelector('button[onclick="toggleCalendar()"]');

                    if (!calendarContainer.contains(event.target) && !calendarButton.contains(event.target)) {
                        calendarContainer.style.display = 'none';
                        }
            });
        //CALENDAR END

        //TIME 
        function setCurrentTime() {
            const currentTimeElement = document.getElementById('currentTime');
            const currentDateElement = document.getElementById('currentDate');

            const currentDate = new Date();
    
            currentDate.setHours(currentDate.getHours() + 0);
                const hours = currentDate.getHours();
                const minutes = currentDate.getMinutes();
                const seconds = currentDate.getSeconds();
                const formattedHours = hours < 10 ? '0' + hours : hours;
                const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
                const formattedSeconds = seconds < 10 ? '0' + seconds : seconds;

            currentTimeElement.textContent = `${formattedHours}:${formattedMinutes}:${formattedSeconds}`;
            currentDateElement.textContent = currentDate.toLocaleDateString();
        }
        setCurrentTime();
        setInterval(setCurrentTime, 1000);
        //TIME END

        let currentCategoryIndex = 1;
        const totalCategories = 5; // Total number of categories

        function showNextCategory() {
            // Hide the current category
            document.getElementById(`category-${currentCategoryIndex}`).style.display = 'none';

            // Update to the next category index, loop back to 1 if at the end
            currentCategoryIndex = (currentCategoryIndex % totalCategories) + 1;

            // Show the next category
            document.getElementById(`category-${currentCategoryIndex}`).style.display = 'block';
        }

        function showPreviousCategory() {
            // Hide the current category
            document.getElementById(`category-${currentCategoryIndex}`).style.display = 'none';

            // Update to the previous category index, loop back to totalCategories if at the start
            currentCategoryIndex = (currentCategoryIndex - 1) || totalCategories;

            // Show the previous category
            document.getElementById(`category-${currentCategoryIndex}`).style.display = 'block';
        }

        // Start the slideshow, show the first category immediately
        window.onload = function() {
            // Show the first category immediately
            document.getElementById(`category-1`).style.display = 'block';
            
            // Start the slideshow after showing the first category
            setInterval(showNextCategory, 3000); // Change every 3 seconds
        };
         // Toggle the visibility of the emoji container when button is clicked
         function toggleEmojis() {
            const emojiContainer = document.getElementById("emojiContainer");
            emojiContainer.style.display = emojiContainer.style.display === "none" || emojiContainer.style.display === "" ? "flex" : "none";
        }

        // Load the saved reaction (if any) from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const savedReaction = localStorage.getItem('userReaction');
            if (savedReaction) {
                document.getElementById('savedReactionText').innerHTML = `You previously reacted with: ${savedReaction}`;
                const reactionItems = document.querySelectorAll('.reaction-item');
                reactionItems.forEach(item => {
                    if (item.querySelector('span').textContent === savedReaction) {
                        item.querySelector('.user-reaction').style.display = 'inline';
                    }
                });
            }
        });

        // Function to save the user's reaction
        function saveReaction(reaction) {
            // Display the selected reaction
            const reactionText = document.getElementById("reactionText");
            reactionText.style.display = "block";
            reactionText.innerHTML = `You reacted with: ${reaction}`;

            // Save the reaction to localStorage
            localStorage.setItem('userReaction', reaction);

            // Show the saved reaction below
            document.getElementById('savedReactionText').innerHTML = `You saved your reaction: ${reaction}`;

            // Show the selected emoji
            const selectedEmoji = document.getElementById("selectedEmoji");
            selectedEmoji.innerHTML = getEmoji(reaction);
        }

        // Function to get the emoji based on the reaction
        function getEmoji(reaction) {
            switch (reaction) {
                case 'Like': return '👍';
                case 'Love': return '❤️';
                case 'Haha': return '😂';
                case 'Wow': return '😲';
                case 'Sad': return '😢';
                case 'Angry': return '😡';
                default: return '';
            }
        }

        function submitReaction(reaction) {
            document.getElementById('reactionInput').value = reaction;
            document.getElementById('reactionForm').submit();
        }

        // Load the saved reaction (if any) from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const savedReaction = localStorage.getItem('userReaction');
            if (savedReaction) {
                document.getElementById('savedReactionText').innerHTML = `You previously reacted with: ${savedReaction}`;
                const reactionItems = document.querySelectorAll('.reaction-item');
                reactionItems.forEach(item => {
                    if (item.querySelector('span').textContent === savedReaction) {
                        item.querySelector('.user-reaction').style.display = 'inline';
                    }
                });

                // Show the selected emoji
                const selectedEmoji = document.getElementById("selectedEmoji");
                selectedEmoji.innerHTML = getEmoji(savedReaction);
            }
        });

        function openCommentModal() {
            document.getElementById('commentModal').style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function postComment(event, input) {
            if (event.key === 'Enter' && input.value.trim() !== '') {
                let commentList = document.querySelector('.modal-body .comment-list');
                let newComment = document.createElement('p');
                newComment.textContent = input.value;
                newComment.classList.add('comment');
                commentList.appendChild(newComment);

                input.value = '';

                // Optional: Send comment to the backend using AJAX
                // fetch('save_comment.php', {
                //     method: 'POST',
                //     body: JSON.stringify({ comment: input.value }),
                //     headers: { 'Content-Type': 'application/json' }
                // });
            }
        }

        // Reaction Popup
        function showPopup(emoji, popupId) {
            document.getElementById(popupId).style.opacity = '1';
        }

        function hidePopup(popupId) {
            document.getElementById(popupId).style.opacity = '0';
        }

        // Initialize progress circles
        document.addEventListener('DOMContentLoaded', function() {
            const circles = document.querySelectorAll('.progress-circle');
            circles.forEach(circle => {
                const progress = circle.getAttribute('data-progress');
                const circumference = 2 * Math.PI * 90; // for r=90
                const strokeDashoffset = circumference - (progress / 100) * circumference;
                
                // Create SVG circle
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('class', 'progress-ring');
                svg.setAttribute('width', '200');
                svg.setAttribute('height', '200');
                
                const circleElement = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circleElement.setAttribute('class', 'progress-ring__circle');
                circleElement.setAttribute('stroke', '#22d3ee');
                circleElement.setAttribute('stroke-width', '4');
                circleElement.setAttribute('fill', 'transparent');
                circleElement.setAttribute('r', '90');
                circleElement.setAttribute('cx', '100');
                circleElement.setAttribute('cy', '100');
                circleElement.style.strokeDasharray = `${circumference} ${circumference}`;
                circleElement.style.strokeDashoffset = strokeDashoffset;
                
                svg.appendChild(circleElement);
                circle.insertBefore(svg, circle.firstChild);
            });
        });
    </script>

    <!-- Comment Modal -->
    <div id="commentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('commentModal')">&times;</span>
            <input type="text" class="comment-input" placeholder="Write your comment..." onkeypress="postComment(event, this)">
            <div class="modal-body">
                <div class="comment-list"></div>
            </div>
        </div>
    </div>

    <!-- Reaction Modal -->
    <div id="reactionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('reactionModal')">&times;</span>
            <div class="modal-body"></div>
        </div>
    </div>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="../js/admin.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>