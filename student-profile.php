<?php

error_reporting(E_ALL); 
ini_set('log_errors','1'); 
ini_set('display_errors','1'); 

session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_email']) || $_SESSION['user_type'] != "student"){
    header("Location: index.html");
    exit();
}

$email = $_SESSION['user_email'];

$successProfile = "";
$successVolunteer = "";

if (isset($_SESSION['success_profile_message'])) {
    $successProfile = $_SESSION['success_profile_message'];
    unset($_SESSION['success_profile_message']);
}

if (isset($_SESSION['success_volunteer_message'])) {
    $successVolunteer = $_SESSION['success_volunteer_message'];
    unset($_SESSION['success_volunteer_message']);
}


// Handle form submission (add volunteer hours)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hours'], $_POST['date'], $_POST['workDescription'], $_POST['committee'])) {
    $hours = $_POST['hours'];
    $date = $_POST['date'];
    $work = $_POST['workDescription'];

    // Get the student's membershipID
    $membershipQuery = "SELECT membershipID FROM membership WHERE email = ?";
    $membershipStmt = $conn->prepare($membershipQuery);
    $membershipStmt->bind_param("s", $email);
    $membershipStmt->execute();
    $membershipResult = $membershipStmt->get_result();
    $membership = $membershipResult->fetch_assoc();

    if ($membership) {
        $membershipID = $membership['membershipID'];

        $getMaxQuery = "SELECT MAX(volunteeringID) AS maxID FROM volunteeringhours";
        $getMaxResult = $conn->query($getMaxQuery);
        $maxIDRow = $getMaxResult->fetch_assoc();
        $newID = $maxIDRow['maxID'] + 1;


        $insertQuery = "INSERT INTO volunteeringhours (volunteeringID, membershipID, email, totalHours, date, workDescription) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("iisiss", $newID, $membershipID, $email, $hours, $date, $work);


        if ($stmt->execute()) {
            $volunteeringID = $newID;
        
            // Insert into 'record' table
            $recordQuery = "INSERT INTO record (membershipID, voulnteeringID, email) VALUES (?, ?, ?)";
            $recordStmt = $conn->prepare($recordQuery);
            $recordStmt->bind_param("iis", $membershipID, $newID, $email);

            $recordStmt->execute();
        
            // Update totalHours in studentuser
            $updateQuery = "UPDATE studentuser SET volunteeringHours = volunteeringHours + ? WHERE email = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("is", $hours, $email);
            $updateStmt->execute();
            
            $_SESSION['success_volunteer_message'] = "تمت إضافة الساعات التطوعية بنجاح.";

            // Redirect or success message
            header("Location: student-profile.php");
            exit();
        } else {
            echo "خطأ أثناء حفظ الساعات التطوعية.";
        }
        
    } else {
        echo "لم يتم العثور على عضويتك.";
    }
}

// Fetch student info
$query = "SELECT fullName, email, college, studyingLevel, bio, profileImg FROM studentuser WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

// Fetch volunteer hours with committee info from membership table
$volunteerQuery = "
    SELECT v.date, m.committee, v.workDescription, v.totalHours
    FROM volunteeringhours v
    JOIN membership m ON v.membershipID = m.membershipID
    WHERE v.email = ?
";
$volunteerStmt = $conn->prepare($volunteerQuery);
$volunteerStmt->bind_param("s", $email);
$volunteerStmt->execute();
$volunteerResult = $volunteerStmt->get_result();

$totalHours = 0;
$volunteerEntries = [];

while ($row = $volunteerResult->fetch_assoc()) {
    $volunteerEntries[] = $row;
    $totalHours += $row['totalHours'];
}
?>


<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KSUHub | الملف الشخصي</title>
    
    <!-- Tab icon -->
    <link rel="icon" href="img/KSUHub2.png" type="image/x-icon">

    <!-- Bootstrap -->
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="css/font-awesome.min.css">

    <!-- Custom stylesheet -->
    <link type="text/css" rel="stylesheet" href="css/style.css"/>

    <!-- Arabic font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">

    <style> 
        body {
            text-align: right;
        }

        /* Hero Section */
        .hero-area {
            height: 80vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .hero-area .container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Profile & Form Layout */
        .profile-container {
            margin-top: 50px;
        }

        .profile-container .row {
            display: flex;
            align-items: stretch; 
        }

        /* Profile Card & Volunteer Form */
        .profile-card, .volunteer-form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            flex-grow: 1; 
            min-height: 100%; 
        }

        .profile-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            justify-content: space-between; 
            padding: 25px;
        }

        .profile-card .profile-content {
            flex-grow: 1; 
            display: flex;
            flex-direction: column;
            justify-content: center; 
            align-items: center;
        }

        .profile-img {
            width: 100px; 
            height: 100px;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .profile-card h3 {
            margin: 10px 0;
            font-size: 18px; 
            font-weight: bold;
        }

        .profile-card p {
            margin: 5px 0;
            font-size: 14px;
            color: #555;
        }

        .profile-card p strong {
            font-weight: bold;
            color: #333;
        }

        .edit-profile-btn {
            background-color: #FF6700;
            color: white;
            padding: 12px 0;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%; 
            max-width: 500px; 
            text-align: center;
            margin-top: 15px; 
        }

        .edit-profile-btn:hover {
            background-color: #d55a00;
        }

        .volunteer-form .form-group {
            margin-bottom: 15px;
        }

        .volunteer-form select, 
        .volunteer-form input,
        .volunteer-form textarea {
            width: 100%;
            padding: 14px; 
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            height: 55px;
        }

        /* Larger Textarea */
        textarea {
            height: 120px;
            resize: vertical; 
        }

        /* Submit Button */
        .main-button {
            background-color: #FF6700;
            color: white;
            padding: 14px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .main-button:hover {
            background-color: #d55a00;
        }

        .volunteer-log {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        /* Table Styling */
        .volunteer-log table {
            width: 100%;
            text-align: center;
        }

        .volunteer-log th {
            background-color: #FF6700;
            color: white;
            padding: 12px;
            text-align: center !important;
            vertical-align: middle;
        }

        .volunteer-log td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        /* Responsive Layout */
        @media (max-width: 768px) {
            .profile-container .row {
                flex-direction: column; /* Stack elements on small screens */
            }

            .profile-card, 
            .volunteer-form, 
            .volunteer-log {
                width: 100%;
                text-align: center;
            }

            .profile-card {
                margin-bottom: 20px;
            }
            
            .volunteer-form {
                margin-top: 10px;
            }
        }

    </style>
</head>

<body>   
    
    <header id="header" class="transparent-nav">
                <div class="head-container">
                    <div class="navbar-header">
                        <!-- Logo -->
                        <div class="navbar-brand">
                            <a class="logo" href="home.php"> <!-- Changed to .php -->
                                <img src="./img/logo-alt.png" alt="logo">
                            </a>
                        </div>
                        <!-- Mobile toggle -->
                        <button class="navbar-toggle">
                            <span></span>
                        </button>
                    </div>

                    <!-- Navigation -->
                    <nav id="nav">
                        <ul class="main-menu nav navbar-nav navbar-right">
                            <li><a href="home.php">الصفحة الرئيسية</a></li>
                            <li><a href="clubs.php">النوادي</a></li>
                                <!-- Show profile link based on user type -->
                                <?php if($_SESSION['user_type'] == "student"): ?>
                                    <li><a href="student-profile.php">الملف الشخصي</a></li>
                                <?php else: ?>
                                    <li><a href="club-profile-admin.php">ملف النادي</a></li>
                                <?php endif; ?>

                                <!-- Logout button (only for logged-in users) -->
                                <li class="logout-item">
                                    <a href="logout.php" class="logout-button">
                                        <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                                    </a>
                                </li>
                        </ul>
                    </nav>
                </div>
            </header>

    <div style="direction: rtl;">
    <!-- Hero Area -->
    <div class="hero-area section">
        <div class="bg-image bg-parallax overlay" style="background-image:url(./img/std-profile-background.png); background-size: contain; background-position: center; background-repeat: no-repeat; height: 80vh; width: 100%;"></div>
        <div class="container text-center">
            <h1 class="white-text">ملف الطالب</h1>
        </div>
    </div>


    <!-- Profile Section -->
    <div class="container profile-container">
            <?php if (!empty($successProfile)): ?>
            <div class="alert alert-success text-center" style="margin-bottom: 20px;">
                <?= $successProfile ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Card: Right Side -->
            <div class="col-md-4">
                <div class="profile-card">
                    <?php
                    $profileImage = (!empty($student['profileImg']) && file_exists("uploads/" . $student['profileImg']))
                        ? "uploads/" . $student['profileImg']
                        : "img/profileIcon.png";
                    ?>
                    <img src="<?= $profileImage . '?v=' . time() ?>" class="profile-img" alt="Student Picture">

                    <h3><?= $student['fullName'] ?></h3>
                    <p><strong>البريد الإلكتروني:</strong> <span style="direction: ltr; text-align: left; display: inline-block;"><?= htmlspecialchars($student['email']) ?></span></p>
                    <p><strong>الكلية:</strong> <?= htmlspecialchars($student['college']) ?></p>
                    <p><strong>المستوى الدراسي:</strong> <?= htmlspecialchars($student['studyingLevel']) ?></p>
                    <p><strong>نبذة:</strong> <?= nl2br($student['bio']) ?></p>

                    <form action="student-edit.php" method="get">
                        <button type="submit" class="edit-profile-btn">تعديل الملف الشخصي</button>
                    </form>
                </div>
            </div>

            <!-- Volunteer Form: Left Side -->
            <div class="col-md-8">
                <div class="volunteer-form">
                    <?php
                        $committees = [];
                        $committeeQuery = "SELECT DISTINCT committee FROM membership WHERE email = ?";
                        $committeeStmt = $conn->prepare($committeeQuery);
                        $committeeStmt->bind_param("s", $email);
                        $committeeStmt->execute();
                        $committeeResult = $committeeStmt->get_result();

                        while ($row = $committeeResult->fetch_assoc()) {
                            $committees[] = $row['committee'];
                        }
                    ?>
                    <h3>إضافة الساعات التطوعية</h3>
                    <?php if (!empty($successVolunteer)): ?>
                        <div class="alert alert-success text-center" style="margin-bottom: 20px;">
                            <?= $successVolunteer ?>
                        </div>
                    <?php endif; ?>


                    <form action="student-profile.php" method="post">
                        <div class="form-group">
                            <label for="committee">اللجنة:</label>
                            <select class="form-control" id="committee" name="committee" required <?= empty($committees) ? 'disabled' : '' ?>>
                                <?php if (empty($committees)): ?>
                                    <option value="">أنت لست ضمن أي لجنة حالياً</option>
                                <?php else: ?>
                                    <option value="" disabled selected>اختر اللجنة</option>
                                    <?php foreach ($committees as $committee): ?>
                                        <option value="<?= $committee ?>"><?= $committee ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>

                        </div>

                        <div class="form-group">
                            <label for="hours">عدد الساعات:</label>
                            <input type="number" class="form-control" id="hours" name="hours" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="date">التاريخ:</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="workDescription">تفاصيل العمل:</label>
                            <textarea class="form-control" id="workDescription" name="workDescription" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn main-button">إضافة الساعات</button>
                    </form>
                </div>
            </div>
        </div>

    
        <!-- Volunteer Hours Log -->
        <div class="volunteer-log">
            <h3>سجل الساعات التطوعية</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>اللجنة</th>
                        <th>التفاصيل</th>
                        <th>الساعات</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($volunteerEntries as $entry): ?>
                    <tr>
                        <td><?= $entry['date'] ?></td>
                        <td><?= $entry['committee'] ?></td>
                        <td><?= $entry['workDescription'] ?></td>
                        <td><?= $entry['totalHours'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($volunteerEntries)): ?>
                    <tr>
                        <td colspan="4">لا توجد ساعات تطوعية مسجلة حتى الآن.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>

            <!-- Total Volunteer Hours -->
            <div class="text-center" style="padding-top: 10px;">
            <h3>إجمالي الساعات التطوعية: <span id="totalHours"><?= $totalHours ?></span> ساعة</h3>
            </div>
        </div>

    </div>
</div>

    <!-- Footer -->
		<footer id="footer" class="section">
			<div class="container">
				<div class="row">
					<!-- footer logo -->
					<div class="col-md-6">
						<div class="footer-logo">
							<a class="logo" href="home.html">
								<img src="./img/logo.png" alt="logo">
							</a>
						</div>
					</div>

					<!-- footer nav -->
					<div class="col-md-6">
						<ul class="footer-nav">
							<li><a href="home.php">الصفحة الرئيسية</a></li> 
                                                        <li><a href="clubs.php">النوادي</a></li>
                                                            <!-- Show profile link based on user type -->
                                                            <?php if($_SESSION['user_type'] == "student"): ?>
                                                                <li><a href="student-profile.php">الملف الشخصي</a></li>
                                                            <?php else: ?>
                                                                <li><a href="club-profile-admin.php">ملف النادي</a></li>
                                                            <?php endif; ?>
						</ul>
					</div>
				</div>

				<!-- row -->
				<div id="bottom-footer" class="row">

					<!-- copyright -->
					<div class="col-md-8 col-md-pull-4">
						<div class="footer-copyright" style="text-align: right;">
							<span>KSUHub &copy; | جميع الحقوق محفوظة حتى عام 2025</span>
						</div>
					</div>

				</div>
			</div>
		</footer>
		<div id='preloader'><div class='preloader'></div></div>

		<!-- jQuery Plugins -->
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/main.js"></script>

        <script>
            // إخفاء رسالة النجاح بعد 3 ثواني
            setTimeout(function () {
                const alert = document.querySelector('.alert-success');
                if (alert) {
                    alert.style.display = 'none';
                }
            }, 3000); 
        </script>

            
</body>
</html>
