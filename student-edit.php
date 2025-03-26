<?php
    session_start();
    include 'db_connection.php';

    if (!isset($_SESSION['user_email']) || $_SESSION['user_type'] != "student") {
        header("Location: login.html");
        exit();
    }

    $email = $_SESSION['user_email'];

    // Fetch student data to pre-fill the form
    $query = "SELECT fullName, email, college, studyingLevel, bio, profileImg FROM studentuser WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();

    // Handle form submission (if the student clicked "Save")
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = $_POST['fullName'];
        $college = $_POST['college'];
        $level = $_POST['studyingLevel'];
        $bio = $_POST['bio'];
    
        // Keep the current image by default
        $profileImgName = $student['profileImg'];
    
        // Validation patterns
        $emailPattern = "/^4.+@student\.ksu\.edu\.sa$/";
        $levelOptions = ["السنة الأولى المشتركة", "السنة الثانية", "السنة الثالثة", "السنة الرابعة", "خريج"];
        $collegeOptions = ["كلية الحاسب وتقنية المعلومات", "كلية الهندسة", "كلية العلوم", "كلية الطب", "كلية إدارة الأعمال", "كلية الآداب"];

        // Email check
        if (!preg_match($emailPattern, $email)) {
            echo "<script>alert('البريد الإلكتروني غير صحيح. يجب أن يبدأ بـ 4 وينتهي بـ @student.ksu.edu.sa'); window.location.href = 'student-edit.php';</script>";
            exit();
        }

        // Level check
        if (!in_array($level, $levelOptions)) {
            echo "<script>alert('المستوى الدراسي غير صالح.'); window.location.href = 'student-edit.php';</script>";
            exit();
        }

        // College check
        if (!in_array($college, $collegeOptions)) {
            echo "<script>alert('الكلية غير صالحة.'); window.location.href = 'student-edit.php';</script>";
            exit();
        }

        // Handle image upload if a new one is provided
        if (isset($_FILES['profileImg']) && $_FILES['profileImg']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profileImg']['tmp_name'];
            $fileExtension = pathinfo($_FILES['profileImg']['name'], PATHINFO_EXTENSION);
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        
            if (in_array(strtolower($fileExtension), $allowedExtensions)) {
                $newFileName = uniqid("profile_") . "." . $fileExtension;
                $uploadDir = "uploads/";
                $destPath = $uploadDir . $newFileName;
        
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    //  Delete old image
                    if (!empty($student['profileImg']) && file_exists("uploads/" . $student['profileImg'])) {
                        unlink("uploads/" . $student['profileImg']);
                    }
                    $profileImgName = $newFileName;
                } else {
                    echo " فشل في رفع الصورة.";
                }
            } else {
                echo " نوع الملف غير مسموح.";
            }
        }
        
        
    
        // Update all fields including profile image
        $updateQuery = "UPDATE studentuser SET fullName=?, college=?, studyingLevel=?, bio=?, profileImg=? WHERE email=?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ssssss", $name, $college, $level, $bio, $profileImgName, $email);
    
        if ($updateStmt->execute()) {
            $_SESSION['success_profile_message'] = "تم حفظ التعديلات بنجاح.";
            header("Location: student-profile.php");
            exit();
        } else {
            echo "حدث خطأ أثناء حفظ التعديلات.";
        }
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
            <div class="bg-image bg-parallax overlay" style="background-image:url(./img/std-profile-background.png); background-size: contain; background-position: center; background-repeat: no-repeat; height: 80vh; width: 100%;"></div>
            <div class="container text-center">
                <h1 class="white-text">تعديل الملف الشخصي</h1>
            </div>
        </div>


        <!-- Edit Profile Form -->
        <div class="edit-profile-container">
            <h2>تعديل الملف الشخصي</h2>
            <form action="student-edit.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">اسم الطالب:</label>
                    <input type="text" class="form-control" id="name" name="fullName" value="<?= htmlspecialchars($student['fullName']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">البريد الإلكتروني:</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" readonly style="direction: ltr; text-align: left;">
                </div>

                <div class="form-group">
                    <label for="college">الكلية:</label>
                    <select class="form-control" id="college" name="college" required>
                        <option value="" disabled <?= empty($student['college']) ? 'selected' : '' ?>>اختر الكلية</option>
                        <option value="كلية الحاسب وتقنية المعلومات" <?= $student['college'] == "كلية الحاسب وتقنية المعلومات" ? 'selected' : '' ?>>كلية الحاسب وتقنية المعلومات</option>
                        <option value="كلية الهندسة" <?= $student['college'] == "كلية الهندسة" ? 'selected' : '' ?>>كلية الهندسة</option>
                        <option value="كلية العلوم" <?= $student['college'] == "كلية العلوم" ? 'selected' : '' ?>>كلية العلوم</option>
                        <option value="كلية الطب" <?= $student['college'] == "كلية الطب" ? 'selected' : '' ?>>كلية الطب</option>
                        <option value="كلية إدارة الأعمال" <?= $student['college'] == "كلية إدارة الأعمال" ? 'selected' : '' ?>>كلية إدارة الأعمال</option>
                        <option value="كلية الآداب" <?= $student['college'] == "كلية الآداب" ? 'selected' : '' ?>>كلية الآداب</option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="level">المستوى الدراسي:</label>
                    <select class="form-control" id="level" name="studyingLevel" required>
                        <option value="" disabled <?= empty($student['studyingLevel']) ? 'selected' : '' ?>>اختر المستوى الدراسي</option>
                        <option value="السنة الأولى المشتركة" <?= $student['studyingLevel'] == "السنة الأولى المشتركة" ? 'selected' : '' ?>>السنة الأولى المشتركة</option>
                        <option value="السنة الثانية" <?= $student['studyingLevel'] == "السنة الثانية" ? 'selected' : '' ?>>السنة الثانية</option>
                        <option value="السنة الثالثة" <?= $student['studyingLevel'] == "السنة الثالثة" ? 'selected' : '' ?>>السنة الثالثة</option>
                        <option value="السنة الرابعة" <?= $student['studyingLevel'] == "السنة الرابعة" ? 'selected' : '' ?>>السنة الرابعة</option>
                        <option value="خريج" <?= $student['studyingLevel'] == "خريج" ? 'selected' : '' ?>>خريج</option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="bio">نبذة:</label>
                    <textarea class="form-control" id="bio" name="bio" rows="3"><?= htmlspecialchars($student['bio']) ?></textarea>
                </div>

                <div class="form-group">
                    <label for="profileImg">تحديث الصورة الشخصية:</label>
                    <input type="file" class="form-control" id="profileImg" name="profileImg" accept="image/*">
                </div>


                <button type="submit" class="save-btn">حفظ التعديلات</button>
                <a href="student-profile.php" class="btn back-btn">رجوع</a>
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
                            <li><a href="home.html">الصفحة الرئيسية</a></li>
                            <li><a href="clubs.html"> النوادي</a></li>
                            <li><a href="student-profile.php">الملف الشخصي</a></li>
                            <li><a href="contact.html">تواصل معنا</a></li>
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
