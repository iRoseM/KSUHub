<?php
    
    session_start();
    //1. connect to DB
    $conn = mysqli_connect("localhost", "root", "root", "KSUHub", 8889);
    
    //2. check connection error
    if(mysqli_connect_error() != null){
        echo '<p>Error! cant connect to database</p>';
        die(mysqli_connect_errno());
    }
    
    // Validate Input
if (empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
    echo "<script>alert('جميع الحقول مطلوبة!'); window.history.back();</script>";
    exit();
}

    // Get and sanitize inputs
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Determine Table Based on Role
    $table = ($role == "student") ? "StudentUser" : "AdminUser";

    // 3. Query: Check if Email Exists
    $sql = "SELECT * FROM $table WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 0) {
        echo "<script>alert('البريد الإلكتروني غير مسجل!'); window.history.back();</script>";
        exit();
    }

    // Verify Password
    $row = mysqli_fetch_assoc($result);
    if (!password_verify($password, $row['password'])) {
        echo "<script>alert('كلمة المرور غير صحيحة!'); window.history.back();</script>";
        exit();
    }

    // Start User Session
    $_SESSION['email'] = $email;
    $_SESSION['role'] = $role;
    $_SESSION['fullName'] = $row['fullName'];

    echo "<script>alert('تم تسجيل الدخول بنجاح!'); window.location.href='dashboard.php';</script>";

    // 5. Close Connection
    mysqli_close($conn);
    ?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>KSUHub | تسجيل الدخول </title>
        <!-- tab icon -->
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
        <link href="https://fonts.googleapis.com/css2?family=Advent+Pro:ital,wght@0,100..900;1,100..900&family=Baloo+Da+2:wght@400..800&family=Bebas+Neue&family=Cinzel:wght@400..900&family=Courgette&family=Noto+Kufi+Arabic:wght@100..900&family=Ovo&family=Quattrocento:wght@400;700&family=Quicksand:wght@300..700&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    </head>

    <body>

        <header id="header" class="transparent-nav">
            <div class="head-container">
                <div class="navbar-header">
                    <!-- Logo -->
                    <div class="navbar-brand">
                        <a class="logo" href="index.html">
                            <img src="./img/logo-alt.png" alt="KSUHub Logo">
                        </a>
                    </div>

                    <!-- Mobile toggle -->
                    <button class="navbar-toggle">
                        <span></span>
                    </button>

                </div>
            </div>
        </header>

    
    <!-- Background Image -->
    <div class="bg-image bg-parallax overlay" style="background-image: url(./img/home-background.png);"></div>

    <!-- Main Content Wrapper -->
    <div class="home-wrapper">
        <div class="container">
            
                <div class="col-md-6">
                    <div class="log-login-box">
                        <h2 class="log-white-text text-center" style="text-align: center;"> KSUHub تســجيل الدخول إلى</h2>
                        <form action="dashboard.html" method="POST">
                            <div class="log-form-group">
                                <!-- Email -->
                            <div class="log-form-group">
                                <label class="log-white-text">البريد الإلكتروني</label>
                                 <input type="email" name="email" class="log-form-control" placeholder="أدخل البريد الإلكتروني" pattern="4.+@student\.ksu\.edu\.sa" required>
                            </div>
                            </div>
                            <div class="log-form-group">
                                <label class="log-white-text">كلـمة المــرور</label>
                                <input type="password" name="password" class="log-form-control" placeholder="أدخل كلمة المرور" required>
                            </div>

                            <!-- Role Selection Radio Buttons -->
                            <div class="log-form-group">
                                <div class="role-radio-buttons" style="text-align: center;">
                                    <label class="radio-label">
                                        <input type="radio" name="role" value="clubAdmin" required>
                                        <span class="radio-custom"></span>
                                        مسؤول نادي
                                    </label>
                                    <label class="radio-label">
                                        <input type="radio" name="role" value="student" required>
                                        <span class="radio-custom"></span>
                                        طالب
                                    </label>
                                </div>
                            </div>
                            <!-- <button type="submit" class="log-main-button log-icon-button btn-block">LOGIN</button> -->
                            <button type="submit" class="log-main-button log-icon-button btn-block">تســجيل الدخول</button>
                            <p class="text-center log-white-text mt-3" style="text-align: center;">
                                لـيس لديـك حسـاب؟ 
                                <a href="signup.php" class="log-white-text"><strong>سـجّل الآن</strong></a>
                            </p>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 text-content-wrapper">
                        <h1 class="white-text" style="color: #feb47b; text-align: right;">.!أهــلًا وسهــلًا </h1>
                        <p class="lead white-text" style="width: 400px; text-align: right; padding: 0px; margin: 0px;">
                            سجل دخولك الآن لتستأنف رحلتك معنا. تفاعل مع زملائك، تابع الفعاليات، وكن جزءًا من مجتمع جامعي مليء بالإبداع والتميز
                        </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>