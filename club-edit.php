<?php
//    ini_set('display_errors', 1);
//    error_reporting(E_ALL);
    session_start();
    include 'db_connection.php';

    if (!isset($_SESSION['ClubID'])) {
        echo "Club not logged in";
        exit;
    }
    
    $clubID = $_SESSION['ClubID'];
    
    // Fetch current club data
    $sql = "SELECT * FROM adminuser WHERE ClubID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $clubID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $club = $result->fetch_assoc();

        $email = $club['email'];
        $clubName = $club['clubName'];
        $clubVision = $club['clubVision'];
        $clubGoal = $club['clubGoal'];
        $clubAccount = $club['clubAccount'];
        $clubCollege = $club['clubCollege'];
        $clubImage = $club['image'];
    } else {
        echo "Club not found.";
        exit;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_type']) && $_POST['form_type'] === 'update') {
        $clubName = $_POST['clubName'];
        $email = $_POST['email'];
        $clubCollege = $_POST['clubCollege'];
        $clubVision = $_POST['clubVision'];
        $clubGoal = $_POST['clubGoal'];
        $clubAccount = $_POST['clubAccount'];

        // Handle image upload if any
        $imagePath = null;
        if (isset($_FILES['profileImg']) && $_FILES['profileImg']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            $imageName = basename($_FILES['profileImg']['name']);
            $targetPath = $uploadDir . $imageName;

            if (move_uploaded_file($_FILES['profileImg']['tmp_name'], $targetPath)) {
                $imagePath = $imageName;
            } else {
                echo "<script>alert('فشل في رفع الصورة.');</script>";
            }
        }

        // Build SQL query with optional image update
        $sql = "UPDATE adminuser SET clubName=?, clubCollege=?, clubVision=?, clubGoal=?, clubAccount=?";
        $params = [$clubName, $clubCollege, $clubVision, $clubGoal, $clubAccount];

        if ($imagePath) {
            $sql .= ", image=?";
            $params[] = $imagePath;
        }

        $sql .= " WHERE clubID=?";
        $params[] = $clubID;

        // Prepare & bind
        $types = str_repeat("s", count($params) - 1) . "i"; // All strings except clubID (int)
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo "<script>alert('تم تحديث الملف بنجاح.'); window.location.href='club-profile-admin.php';</script>";
        } else {
            echo "<script>alert('حدث خطأ أثناء التحديث.');</script>";
        }

        $stmt->close();
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
        /* RTL Support */
        body {
            text-align: right;
            background-color: #f8f9fa;
        }

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

        /* Form Container */
        .edit-profile-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Title */
        .edit-profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        /* Input Fields */
        .form-control {
            font-size: 16px;
            padding: 12px 16px;
            height: 50px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        textarea.form-control {
            height: auto;
            resize: vertical;
        }


        /* Save Button */
        .save-btn {
            background-color: #FF6700;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .save-btn:hover {
            background-color: #d55a00;
        }

        /* Back Button */
        .back-btn {
            background-color: #6c757d;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            font-size: 16px;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }
        
    </style>
</head>

<body>
    <header id="header" class="transparent-nav">
                <div class="head-container">
                    <div class="navbar-header">
                        <!-- Logo -->
                        <div class="navbar-brand">
                            <a class="logo" href="home.php">
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
        <div class="bg-image bg-parallax overlay" style="background-image: url('uploads/<?= htmlspecialchars($clubImage) ?>'); background-size: contain; background-position: center; background-repeat: no-repeat; height: 80vh; width: 100%;"></div>
        <div class="container text-center">
                <h1 class="white-text">تعديل ملف النادي</h1>
            </div>
        </div>


        <!-- Edit Profile Form -->
        <div class="edit-profile-container">
            <h2>تعديل ملف النادي</h2>
            <form action="club-edit.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="clubName">اسم النادي:</label>
                <input type="text" class="form-control" id="clubName" name="clubName" value="<?= htmlspecialchars($clubName) ?>" required>
            </div>

            <div class="form-group">
                <label for="email">البريد الإلكتروني:</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly style="direction: ltr; text-align: left;">
            </div>

            <div class="form-group">
                <label for="clubCollege">الكلية:</label>
                <select class="form-control" id="clubCollege" name="clubCollege" required>
                    <option value="" disabled <?= empty($clubCollege) ? 'selected' : '' ?>>اختر الكلية</option>
                    <option value="كلية الحاسب والمعلومات" <?= $clubCollege == "كلية الحاسب والمعلومات" ? 'selected' : '' ?>>كلية الحاسب والمعلومات</option>
                    <option value="كلية إدارة الأعمال" <?= $clubCollege == "كلية إدارة الأعمال" ? 'selected' : '' ?>>كلية إدارة الأعمال</option>
                    <option value="كلية الطب" <?= $clubCollege == "كلية الطب" ? 'selected' : '' ?>>كلية الطب</option>
                    <option value="كلية العلوم الطبية التطبيقية" <?= $clubCollege == "كلية العلوم الطبية التطبيقية" ? 'selected' : '' ?>>كلية العلوم الطبية التطبيقية</option>
                    <option value="معهد ريادة الأعمال" <?= $clubCollege == "معهد ريادة الأعمال" ? 'selected' : '' ?>>معهد ريادة الأعمال</option>
                </select>
            </div>

            <div class="form-group">
                <label for="clubVision">رؤية النادي:</label>
                <textarea class="form-control" id="clubVision" name="clubVision"><?= htmlspecialchars($clubVision) ?></textarea>
            </div>

            <div class="form-group">
                <label for="clubGoal">رسالة النادي:</label>
                <textarea class="form-control" id="clubGoal" name="clubGoal"><?= htmlspecialchars($clubGoal) ?></textarea>
            </div>

            <div class="form-group">
                <label for="clubAccount">حساب النادي (رابط):</label>
                <input type="url" class="form-control" id="clubAccount" name="clubAccount" value="<?= htmlspecialchars($clubAccount) ?>">
            </div>

            <div class="form-group">
                <label for="profileImg"> شعار النادي:</label>
                <input type="file" class="form-control" id="profileImg" name="profileImg" accept="image/*">
            </div>
            <input type="hidden" name="form_type" value="update">           
            <button type="submit" class="save-btn">حفظ التعديلات</button>
            <a href="club-profile-admin.php" class="btn back-btn">رجوع</a>
        </form>

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
        <!-- preloader -->
        <div id='preloader'><div class='preloader'></div></div>

        <!-- jQuery Plugins -->
        <script type="text/javascript" src="js/jquery.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
        <script type="text/javascript" src="js/main.js"></script>
    
</body>
</html>
