<?php

    //1. connect to DB
    $conn = mysqli_connect("localhost", "root", "root", "KSUHub", 8889);
    
    //2. check connection error
    if(mysqli_connect_error() != null){
        echo '<p>Error! cant connect to database</p>';
        die(mysqli_connect_errno());
    }
    
    // Validate Required Fields
    if (
        empty($_POST['fullName']) || empty($_POST['email']) || empty($_POST['password']) ||
        empty($_POST['phoneNo']) || empty($_POST['college']) || empty($_POST['studyingLevel'])
    ) {
        echo "<script>alert('جميع الحقول مطلوبة! يرجى ملء جميع البيانات.'); window.history.back();</script>";
        exit();
    }
    
    // Sanitize and Validate Input Data
    $fullName = mysqli_real_escape_string($conn, $_POST['fullName']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Encrypt password
    $phoneNo = mysqli_real_escape_string($conn, $_POST['phoneNo']);
    $college = mysqli_real_escape_string($conn, $_POST['college']);
    $studyingLevel = mysqli_real_escape_string($conn, $_POST['studyingLevel']);
    
    // Check for Duplicate Email or Phone Number
    $checkQuery = "SELECT * FROM StudentUser WHERE email = '$email' OR phoneNo = '$phoneNo'";
    $result = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('البريد الإلكتروني أو رقم الجوال مسجل بالفعل! الرجاء استخدام بيانات مختلفة.'); window.history.back();</script>";
        exit();
    }
    
    // 3. query: Insert Data into Database
    $sql = "INSERT INTO StudentUser (email, fullName, password, phoneNo, college, studyingLevel) 
            VALUES ('$email', '$fullName', '$password', '$phoneNo', '$college', '$studyingLevel')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('تم تسجيل الحساب بنجاح!'); window.location.href='dashboard.html';</script>";
    } else {
        echo "<script>alert('حدث خطأ أثناء التسجيل: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
    
    //5. end connection
    mysqli_close($conn);
    
?>
<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KSUHub | تسجيل حساب </title>

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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
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
            <div class="row">
                <!-- Signup Form -->
                <div class="col-md-6">
                    <div class="log-signup-box">
                        <h2 class="log-white-text text-center">KSUHub تســجيل حســاب</h2>
                        <form action="dashboard.html" method="POST">
                            <!-- Full Name -->
                            <div class="log-form-group">
                                <label class="log-white-text">الاسم الكامل</label>
                                <input type="text" name="fullName" class="log-form-control" placeholder="أدخل الاسم الكامل" required>
                            </div>

                            <!-- Password -->
                            <div class="log-form-group">
                                <label class="log-white-text">كلمة المرور</label>
                                <input type="password" name="password" class="log-form-control" placeholder="أدخل كلمة المرور (أكثر من 8 أحرف)" minlength="8" required>
                            </div>

                            <!-- Phone Number -->
                            <div class="log-form-group">
                                <label class="log-white-text">رقم الجوال</label>
                                <input type="tel" name="phoneNo" class="log-form-control" placeholder="أدخل رقم الجوال (يبدأ بـ 05)" pattern="05[0-9]{8}" required>
                            </div>

                            <!-- Email -->
                            <div class="log-form-group">
                                <label class="log-white-text">البريد الإلكتروني</label>
                                <input type="email" name="email" class="log-form-control" placeholder="أدخل البريد الإلكتروني الجامعي (يبدأ بـ 4)" pattern="4.+@student\.ksu\.edu\.sa" required>
                            </div>

                            <!-- College -->
                            <div class="log-form-group">
                                <label class="log-white-text">الكليــة</label>
                                <select name="college" class="log-form-control" required>
                                    <option value="" disabled selected>اختر الكلية</option>
                                    <option value="كلية الحاسب">كلية الحاسب وتقنية المعلومات</option>
                                    <option value="كلية الهندسة">كلية الهندسة</option>
                                    <option value="كلية العلوم">كلية العلوم</option>
                                    <option value="كلية الطب">كلية الطب</option>
                                    <option value="كلية إدارة الأعمال">كلية إدارة الأعمال</option>
                                    <option value="كلية الآداب">كلية الآداب</option>
                                </select>
                            </div>

                            <!-- Studying Level -->
                            <div class="log-form-group">
                                <label class="log-white-text">المستوى الدراسي</label>
                                <select class="log-form-control" required>
                                    <option value="" disabled selected>اختر المستوى الدراسي</option>
                                    <option value="sophomore">السنة الأولى المشتركة</option>
                                    <option value="junior">السنة الثانية</option>
                                    <option value="senior">السنة الثالثة</option>
                                    <option value="graduate">السنة الرابعة</option>
                                    <option value="junior">خريــج</option>
                                </select>
                            </div>

                            <!-- Signup Button -->
                            <button type="submit" class="log-main-button log-icon-button btn-block">تســجيل الحســاب</button>

                            <!-- Login Link -->
                            <p class="text-center log-white-text mt-3" style="text-align: center;">
                                لـديـك حسـاب؟ 
                                <a href="login.html" class="log-white-text"><strong>سـجّل الدخول</strong></a>
                            </p>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Text Content -->
                <div class="col-md-6 text-content-wrapper" style="margin-top: 320px; ">
                    <h1 class="white-text" style="color: #feb47b; text-align: right; padding-left: 80px;">.أنشئ حسابك وابدأ رحلتك🌟</h1>
                    <p class="lead white-text" style="width: 400px; text-align: right; padding-left: 80px; margin: 0px;">
                        سجل حسابك الآن لتفتح أبوابًا جديدة من الفرص. انضم إلى مجتمع نشط، تفاعل مع زملائك، وكن جزءًا من فعاليات مميزة تنتظرك
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>