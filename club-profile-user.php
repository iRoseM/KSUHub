<?php
session_start();
include 'db_connection.php';

if (!empty($_GET['ClubID']) && ctype_digit($_GET['ClubID'])) {
    $clubID = intval($_GET['ClubID']);

    $sql = "SELECT clubName, clubVision, image, clubAccount FROM adminuser WHERE ClubID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $clubID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $clubName = $row["clubName"];
        $clubVision = $row["clubVision"];
        $clubImage = $row["image"];
        $clubAccount = $row["clubAccount"];
    } else {
        echo "<p>النادي غير موجود</p>";
        exit;
    }

    $sqlEvents = "SELECT eventName, eventDescription, image FROM event WHERE clubID = ?";
    $stmtEvents = $conn->prepare($sqlEvents);
    $stmtEvents->bind_param("i", $clubID);
    $stmtEvents->execute();
    $resultEvents = $stmtEvents->get_result();

    if ($resultEvents->num_rows > 0) {
        $events = [];
        while ($event = $resultEvents->fetch_assoc()) {
            $events[] = $event;
        }
    } else {
        $events = [];
    }

    //Ar
    // for the join form
    $isAlreadyApplied = false;
    $applicationStatus = '';

    if (isset($_SESSION['user_email'])) {
        $email = $_SESSION['user_email'];

        $checkSql = "SELECT status FROM membership WHERE email = ? AND clubID = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("si", $email, $clubID);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $isAlreadyApplied = true;
            $applicationStatus = $checkResult->fetch_assoc()['status'];
        }

        $checkStmt->close();
    }
    //

    $stmt->close();
    $stmtEvents->close();
} else {
    echo "<p>ID غير صحيح</p>";
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clubID'], $_POST['committee'])) {
    $clubID = intval($_POST['clubID']); 

    if ( isset($_SESSION['user_email'])) {
        $email = $_SESSION['user_email'];
        $committee = $_POST['committee'];

        $insertSql = "INSERT INTO membership (email, clubID, status, committee) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $status = 'pending';

        $insertStmt->bind_param("ssss", $email, $clubID, $status, $committee);

        if ($insertStmt->execute()) {
            $_SESSION['success_message'] = "تم ارسال طلبك بنجاح!";
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;

        } else {
            echo "حدث خطأ أثناء الانضمام. يرجى المحاولة مرة أخرى.";
        }

        $insertStmt->close();
    } else {
        echo "لم يتم العثور على بيانات المستخدم في السيشن.";
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KSUHub | ملف النادي</title>

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

    <style> 
        /* Members & Requests */
        html {
            scroll-behavior: smooth;
        }

        .list-group-item {
            padding: 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .list-group-item:hover {
            background: #f4f4f4;
        }

        .accept-request:hover {
            color: #28a745 !important;
            transform: scale(1.1);
            transition: all 0.2s;
        }

        .reject-request:hover {
            color: #dc3545 !important;
            transform: scale(1.1);
            transition: all 0.2s;
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
            

    <div id="club-home" class="hero-area">
        <!-- Background Image -->
        <div class="bg-image bg-parallax overlay" style="background-image:url(uploads/<?php echo $clubImage; ?>); background-size: cover; background-position: center; background-repeat: no-repeat; height: 80vh; width: 100%;"></div>
        <!-- /Background Image -->

        <div class="container">
            <div class="row">
                <div class="col-md-8" style="margin-top: 40rem;">
                    <h1 class="white-text"><?php echo "مرحبا بكم في " . $clubName; ?></h1>
                    <p class="lead white-text">Join us and be part of a great community.</p>
                </div>
            </div>
        </div>
            
        
        <!--Ar-->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div style="margin: 80px auto 0; max-width: 600px;">
                <div class="alert alert-success alert-success-message text-center" role="alert">
                    <?php 
                        echo $_SESSION['success_message']; 
                        unset($_SESSION['success_message']);
                    ?>
                </div>
            </div>
        <?php endif; ?>
        <!--Ar-->

        <div class="center-btn-container" style="display: flex; justify-content: center; gap: 15px;  margin-top: 6rem;">
            <a id="joinButton" class="main-button icon-button" href="#">! أنضم الينا</a>
        </div>
        
        <!-- About -->
        <div id="about" class="section">
            <!-- container -->
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-header">
                            <h2>رؤيتنا</h2>
                        </div>
                        <div class="feature">
                            <div class="feature-content">
                                <h4>الرؤية :</h4>
                                <p><?php echo $clubVision; ?></p>
                            </div>
                        </div>
                        <div class="feature">
                            <div class="feature-content">
                                <h4>الرسالة:</h4>
                                <p>نهتم بتنمية مهارات الطالب التقنية والاجتماعية وغرس مفاهيم المبادرة والقيم الاخلاقيه التي تساهم في الواقع العملي.</p>
                            </div>
                        </div>
                        <div class="feature">
                            <div class="feature-content">
                                <h4>حساب النادي على منصة X :</h4>
                                <p><?php echo $clubAccount; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="about-img">
                            <img src="uploads/<?php echo $clubImage; ?>" alt="Logo">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Ar-->
    <div id="joinSection" class="center-btn-container" style="display: flex; justify-content: center; margin-top: 6rem;">
        <?php if ($isAlreadyApplied): ?>
            <div class="alert alert-info text-center" role="alert" style="font-family: 'Noto Kufi Arabic', sans-serif; padding: 20px 25px; border-radius: 12px; font-size: 18px; direction: rtl;">
                لقد قمت بالتقديم لهذا النادي مسبقًا.
                <br>
                <span style="font-weight: bold;">حالة الطلب:</span>
                <span style="color: #0056b3;">
                    <?php
                        if ($applicationStatus === 'Pending') echo 'قيد المراجعة';
                        elseif ($applicationStatus === 'Approved') echo 'مقبول';
                        elseif ($applicationStatus === 'Rejected') echo 'مرفوض';
                        else echo htmlspecialchars($applicationStatus);
                    ?>
                </span>
            </div>

        <?php else: ?>
            <form id="joinForm" method="POST" action="" style="display: flex; flex-direction: row-reverse; align-items: center; gap: 15px; flex-wrap: wrap;">
                <input type="hidden" name="clubID" value="<?php echo $clubID; ?>">

                <select id="committee" name="committee" required 
                    style="padding: 10px 20px; border-radius: 8px; border: 1px solid #ccc; font-family: 'Noto Kufi Arabic', sans-serif; background-color: #fff; color: #333;">
                    <option value="">-- اختر لجنة --</option>
                    <option value="العلاقات العامة">العلاقات العامة</option>
                    <option value="التقنية">التقنية</option>
                    <option value="المحتوى">المحتوى</option>
                    <option value="التصميم">التصميم</option>
                    <option value="الإعلام">الإعلام</option>
                </select>

                <button type="submit" id="joinButton" class="main-button icon-button">تقديم طلب الانضمام</button>
            </form>
        <?php endif; ?>
    </div>
    <!--Ar-->
    
    <!-- Club Events -->
    <div class="container">
        <div class="row">
            <div class="section-header text-center">
                <h2>فعاليات النادي</h2>
            </div>
        </div>

        <div id="clubs-wrapper">
            <div class="row justify-content-center">
                <?php
                if (!empty($events)) {
                    foreach ($events as $event) {
                        echo "
                        <div class='col-md-3 col-sm-6 col-xs-6'>
                            <div class='club'>
                                <a href='#' class='club-img'>
                                    <img src='uploads/{$event['image']}' alt=''>
                                    <i class='club-link-icon fa fa-link'></i>
                                </a>
                                <a class='club-title' href='#'>{$event['eventName']}</a>
                                <div class='club-details'>
                                    <span class='club-category'>{$event['eventDescription']}</span>
                                    <span class='club-price club-free'>Opened</span>
                                </div>
                            </div>
                        </div>";
                    }
                } else {
                    echo "<p>لا توجد فعاليات حالياً ل {$clubName}</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <footer id="footer" class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="footer-logo">
                        <a class="logo" href="home.html">
                            <img src="./img/logo.png" alt="logo">
                        </a>
                    </div>
                </div>
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
        </div>
    </footer>


    <script>
        /*
    document.getElementById('joinButton').addEventListener('click', function() {
        var clubID = <?php echo $clubID; ?>; // Get the ClubID dynamically from PHP
        // Send the AJAX request to PHP to insert the user into the club_members table
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '', true); // Send to the same page
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && xhr.status == 200) {
                alert(xhr.responseText); // Show success or failure message
            }
        };
        xhr.send('clubID=' + clubID);
    });
    */

    //Ar
    // Auto-hide only the success alert after 4 seconds
    setTimeout(() => {
        const successAlert = document.querySelector('.alert-success-message');
        if (successAlert) successAlert.style.display = 'none';
    }, 4000);


    // Scroll to the form when the user clicks "! أنضم الينا"
    document.getElementById('joinButton').addEventListener('click', function (e) {
        e.preventDefault();
        const formSection = document.getElementById('joinSection');
        if (formSection) formSection.scrollIntoView({ behavior: 'smooth' });
    });




</script>

</body>
</html>
