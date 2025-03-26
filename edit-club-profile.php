<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
$clubID = $_SESSION['ClubID'];
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteClub'])) {
    $delete_sql = "DELETE FROM adminuser WHERE clubID = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $clubID);

    if ($stmt->execute()) {
        session_destroy();
        header("Location: clubs.php"); // redirect after deletion
        exit();
    } else {
        echo "<script>alert('حدث خطأ أثناء حذف النادي.');</script>";
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addEvent'])) {
    $eventName = $_POST['eventName'];
    $eventDescription = isset($_POST['eventDescription']) ? $_POST['eventDescription'] : null;
    $eventDate = !empty($_POST['date']) ? $_POST['date'] : null;
    $eventTime = !empty($_POST['time']) ? $_POST['time'] : null;
    $clubID = $_SESSION['ClubID'];

    // Handle image upload if provided
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = $imageName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO event (clubID, eventName, eventDescription, date, time, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $clubID, $eventName, $eventDescription, $eventDate, $eventTime, $imagePath);

    if ($stmt->execute()) {
        echo "<script>alert('تمت إضافة الفعالية بنجاح!');</script>";
    } else {
        echo "<script>alert('حدث خطأ أثناء إضافة الفعالية.');</script>";
    }
}


// Check if the user is logged in and is a student
/*
if (!isset($_SESSION['user_email']) || $_SESSION['user_type'] != "clubAdmin") {
    header("Location: index.html");
    exit();
}*/

if (isset($_SESSION['ClubID']) && ctype_digit(strval($_SESSION['ClubID']))) {
    $clubID = intval($_SESSION['ClubID']); 

    $sql = "SELECT * FROM adminuser WHERE ClubID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $clubID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $clubID = $row["clubID"];
        $clubName = $row["clubName"];
        $clubImage = $row["image"];
        $clubGoal = $row["clubGoal"];
        $clubVision = $row["clubVision"];
        $clubAccount = $row["clubAccount"];
        $clubCollege = $row["clubCollege"];
        $email = $row["email"];
    } else {
        echo "<p>النادي غير موجود</p>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KSUHub | تعديل ملف النادي</title>
    
    <!-- Tab icon -->
    <link rel="icon" href="img/KSUHub2.png" type="image/x-icon">

    <!-- Bootstrap -->
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="css/font-awesome.min.css">

    <!-- Custom stylesheet -->
    <link type="text/css" rel="stylesheet" href="css/style.css">

    <!-- Arabic font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">

    <style> 
        /* Ensure RTL Layout */
        body {
            text-align: right;
            background-color: #f8f9fa; /* Light background for better contrast */
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

        /* Ensuring h1 stays centered */
        .hero-area .container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        /* Force LTR for navbar only */
        .main-menu {
            direction: ltr !important; /* Force text direction */
            justify-content: flex-end; 
        }

        /* Revert list item order */
        .main-menu > li {
            direction: rtl;
        }

        /* Profile & Form Layout */
        .profile-container {
            margin-top: 50px;
        }

        /* Make profile and form the same height */
        .profile-container .row {
            display: flex;
            align-items: stretch; /* Ensures both divs stretch to the same height */
        }

        /* Profile Card & Volunteer Form */
        .profile-card, .volunteer-form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            flex-grow: 1; /* Makes both divs grow equally */
            min-height: 100%; /* Ensures equal height */
        }

        .profile-card {
            align-items: center; /* Center content horizontally */
            text-align: center;
            justify-content: space-between; /* Pushes elements apart */
        }

        .profile-card .profile-content {
            flex-grow: 1; /* Allows content to take full height */
            display: flex;
            flex-direction: column;
            justify-content: center; /* Centers content inside */
            align-items: center;
        }

        /* Profile Image */
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        /* Centered Profile Text */
        .profile-card h3, 
        .profile-card p {
            margin: 5px 0;
            width: 100%; 
        }

        .edit-profile-btn {
            background-color: #FF6700;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: auto; /* Push button to the bottom */
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
            resize: vertical; /* Allows vertical resizing */
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



        /* Volunteer Log */
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

        /* Center Table Headers */
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

      
    <!-- Hero Area -->
    <div class="hero-area section">
        <div class="bg-image bg-parallax overlay" style="background-image:url(./img/scacbanner.jpg); background-size: contain; background-position: center; background-repeat: no-repeat; height: 80vh; width: 100%;"></div>
        <div class="container text-center">
            <h1 class="white-text">ملف النادي</h1>
        </div>
    </div>


    <!-- Profile Section -->
    <div class="container profile-container">
        <div class="row">
            <!-- Profile Card: Right Side -->
            <div class="col-md-4">
                <div class="profile-card">
                    <img src="uploads/<?php echo $clubImage; ?>" class="profile-img" alt="Club Logo">
                    <h3><?php echo $clubName; ?></h3>
                    <p><strong>البريد الإلكتروني:</strong> <?php echo $email; ?></p>
                    <p><strong>الكلية:</strong> <?php echo $clubCollege; ?></p>
                    <p><strong>الرؤية:</strong> <?php echo $clubVision; ?></p>
                    <p><strong>الرسالة:</strong> <?php echo $clubGoal; ?></p>
                    <p><strong>حساب النادي:</strong> <a href="<?php echo $clubAccount; ?>" target="_blank"><?php echo $clubAccount; ?></a></p>
                    <form action="club-edit.php" method="post">
                        <input type="hidden" name="form_type" value="edit">
                        <button type="submit" class="edit-profile-btn">تعديل الملف الشخصي</button> <br> <br>
                    </form>
                    <form method="post" onsubmit="return confirm('هل أنت متأكد من حذف النادي؟');" style="margin-top: 10px;">
                        <input type="hidden" name="deleteClub" value="1">
                        <button type="submit" class="edit-profile-btn" style="background-color:rgb(207, 39, 24);;">حذف النادي</button>
                    </form>

                </div>
            </div>

            <div class="col-md-8">
                <div class="volunteer-form">
                    <h3>إضافة فعالية للنادي</h3>
                    <form method="POST" action="edit-club-profile.php" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="eventName"><span style="color: red;">*</span>اسم الفعالية:</label>
                            <input type="text" class="form-control" id="eventName" name="eventName" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="date">التاريخ:</label>
                            <input type="date" class="form-control" id="date" name="date">
                        </div>

                        <div class="form-group">
                            <label for="time">الوقت:</label>
                            <input type="time" class="form-control" id="time" name="time">
                        </div>

                        <div class="form-group">
                            <label for="eventDescription">تفاصيل الفعالية:</label>
                            <textarea class="form-control" id="eventDescription" name="eventDescription" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="image">صورة الفعالية:</label>
                            <input type="file" class="form-control" name="image" id="image">
                        </div>

                        <button type="submit" class="btn main-button" name="addEvent">إضافة</button>
                    </form>
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
    </div>

		<!-- jQuery Plugins -->
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/main.js"></script>

    
       
            
</body>
</html>