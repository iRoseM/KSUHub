<?php
include_once 'db_connection.php';
$sql = "SELECT clubName, clubVision, image , clubAccount FROM adminuser WHERE clubID = 1"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $clubName = $row["clubName"];
    $clubVision = $row["clubVision"];
    $clubImage = $row["image"];
    $clubAccount = $row["clubAccount"];
} else {
    echo "error";
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
                    <a class="logo" href="home.html">
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
                    <li><a href="home.html">الصفحة الرئيسية</a></li>
                    <li><a href="clubs.html"> النوادي</a></li>
                    <li><a href="student-profile.html">الملف الشخصي</a></li>
                
                    <li class="logout-item"><a href="logout.php" class="logout-button" style="margin-right: 0;"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
                </ul>
            </nav>

        </div>
    </header>

    <div id="club-home" class="hero-area">
        <!-- Backgound Image -->
        <div class="bg-image bg-parallax overlay" style="background-image:url(uploads/<?php echo $clubImage; ?>); background-size: cover; background-position: center; background-repeat: no-repeat; height: 80vh; width: 100%;"></div>
        <!-- /Backgound Image -->

            <div class="container">
                <div class="row">
                    <div class="col-md-8" style="margin-top: 40rem;">
                        <h1 class="white-text"><?php echo "مرحبا بكم في " . $clubName; ?></h1>
                        <p class="lead white-text">Join us and be part of a great community.</p>
                    </div>
                </div>
            </div>

        <div class="center-btn-container" style="display: flex; justify-content: center; gap: 15px;  margin-top: 6rem;">
            <a class="main-button icon-button" href="#">! أنضم الينا  </a>
        </div>
        
        <!-- About -->
		<div id="about" class="section">

			<!-- container -->
			<div class="container">
				<div class="row">

					<div class="col-md-6" >
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
								<p>نهتم بتنمية مهارات  الطالب التقنية والاجتماعية وغرس مفاهيم المبادرة والقيم الاخلاقيه التي تساهم في الواقع العملي .</p>
							</div>
						</div>
						<div class="feature">
							<div class="feature-content">
								<h4>حساب النادي على منصة X :</h4>
								<p> <?php echo $clubAccount; ?> </p>
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
       <!-- container -->
    <div class="container">

        <!-- row -->
        <div class="row">
            <div class="section-header text-center">
                <h2>فعاليات النادي </h2>
            </div>
        </div>
        <!-- /row -->

        <!-- /Inner-Clubs -->
        <div id="clubs-wrapper">

            <!-- row -->
            <div class="row justify-content-center"> <!-- Added justify-content-center -->

                <!-- single Club -->
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <div class="club">
                        <a href="#" class="club-img">
                            <img src="./img/kanaba.png" alt="">
                            <i class="club-link-icon fa fa-link"></i>
                        </a>
                        <a class="club-title" href="#">فعالية كنبة </a>
                        <div class="club-details">
                            <span class="club-category"> فعالية "كنبة" هي لقاء حواري مُلهم يُقام داخل النادي، حيث يتم استضافة نخبة من دكتورات وأستاذات كلية علوم
                                الحاسب والمعلومات. تهدف "كنبة" إلى تقديم فرصة للأعضاء للتواصل المباشر مع الشخصيات
                                الأكاديمية البارزة، والاستفادة من خبراتهن في بيئة حوارية محفزة وملهمة.</span>
                            <span class="club-price club-free">Opened</span>
                        </div>
                    </div>
                </div>
                <!-- /single Club -->

                <!-- single Club -->
                <div class="col-md-3 col-sm-6 col-xs-6">
                    <div class="club">
                        <a href="#" class="club-img" style="background-color:rgb(209, 127, 127);">
                            <img src="./img/Eid.png" alt="">
                            <i class="club-link-icon fa fa-link"></i>
                        </a>
                        <a class="club-title" href="#"> فعالية عيد </a>
                        <div class="club-details">
                            <span class="club-category">فعالية "عيد" هي احتفالية مميزة تُقام خلال الفصل الدراسي بعد
                                عيد الفطر، حيث تُعنى اللجنة المسؤولة بتنظيم الفعالية من الألف
                                إلى الياء</span>
                            <span class="club-price club-free">Opened</span>
                        </div>
                    </div>
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
                    <li><a href="home.html">الصفحة الرئيسية</a></li>
                    <li><a href="clubs.html"> النوادي</a></li>
                    <li><a href="student-profile.html">الملف الشخصي</a></li>
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

</body>
</html>
