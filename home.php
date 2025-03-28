<?php
//error_reporting(E_ALL); 
//ini_set('log_errors','1'); 
//ini_set('display_errors','1'); 

session_start();
include 'db_connection.php';

/*
if (!isset($_SESSION['user_email']) || !in_array($_SESSION['user_type'], ["student", "clubAdmin"])) {
    header("Location: index.html");
    exit();
}*/

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>KSUHub | الصفحة الرئيسية  </title>
		<link rel="icon" href="img/KSUHub2.png" type="image/x-icon">
		
		<!-- Bootstrap -->
		<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css"/>
		
		<!-- Font Awesome Icon -->
		<link rel="stylesheet" href="css/font-awesome.min.css">
		
		<!-- Custom stylesheet  --> 
		<link type="text/css" rel="stylesheet" href="css/style.css"/>
		
		<!-- Arabic font -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">
		
		<!-- Icons -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

		<!-- Home -->
		<div id="home" class="hero-area">
			<!-- Backgound Image -->
			<div class="bg-image bg-parallax overlay" style="background-image:url(./img/home-background.png)"></div>
			<div class="home-wrapper">
				<div class="container">
					<div class="row">
						<div class="col-md-8">
							<h1 class="white-text">وجهتك للفُرص، الفعاليات، والتَجربة</h1>
							<p class="lead white-text">استكشف أندية جامعة الملك سعود، واجعل حياتك الجامعية أكثر إشراقًا!</p>
							
							<a class="main-button icon-button" href="clubs.php">تعرف على النوادي!</a>
									
						</div>
					</div>
				</div>
			</div>

		</div>

		<!-- Clubs -->
		<div id="clubs" class="section">
                    <div class="container">
                        <!-- Row for Header -->
                        <div class="row">
                            <div class="section-header text-center">
                                <h2>أشهر النوادي</h2>
                                <p class="lead">هنا المنافسة والتحدي!</p>
                            </div>
                        </div>

                        <div id="clubs-wrapper">
						<?php
                          $sql = "SELECT ClubID, clubName, clubCollege, image FROM adminuser LIMIT 8";  
                          $result = $conn->query($sql);

                          if ($result->num_rows > 0) {
                              echo '<div class="row">';

                             while ($club = $result->fetch_assoc()) {
                                $clubID = isset($club['ClubID']) ? intval($club['ClubID']) : 0;

                                    if ($clubID > 0) {
                                        $clubLink = "club-profile-user.php?ClubID=" . $clubID;
                                  } else {
                                     $clubLink = "#";
                                       }
                         ?>

        <div class="col-md-3 col-sm-6 col-xs-6">
            <div class="club">
                <!-- Club Image -->
                <a href="<?= $clubLink ?>" class="club-img" style="width: 100%; height: 200px;">
                    <img src="uploads/<?= htmlspecialchars($club['image']) ?>" alt="<?= htmlspecialchars($club['clubName']) ?> logo">
                    <i class="club-link-icon fa fa-link"></i>
                </a>

                <!-- Club Title -->
                <a class="club-title" href="<?= $clubLink ?>"> 
                    <?= htmlspecialchars($club['clubName']) ?>  
                </a>

                <!-- Club Description/Goal -->
                <div class="club-details">
                    <span class="club-category"><?= htmlspecialchars($club['clubCollege']) ?></span> 
                </div>
            </div>
        </div>

        <?php
    }

    echo '</div>';
} else {
    echo '<p>There is no clubs right now</p>';
}

?>
 </div>

                        <!-- View All Clubs Button -->
                        <div class="row">
                            <div class="center-btn">
                                <a class="main-button icon-button" href="clubs.php">جميع الأندية</a> <!-- Link to view all clubs -->
                            </div>
                        </div>
                    </div>
                </div>
                
                <!--Events-->
                <?php
    // Get total number of events first
    $count_result = $conn->query("SELECT COUNT(*) as total FROM event");
    $total = $count_result->fetch_assoc()['total'];

    if ($total > 0) {
        // Loop back around using modulo
        $offset = floor((time() - strtotime('2024-01-01')) / (3 * 86400)) % $total;

        $event_sql = "SELECT * FROM event LIMIT 1 OFFSET $offset";
        $event_result = $conn->query($event_sql);

        if ($event_result->num_rows > 0) {
            $event = $event_result->fetch_assoc();
?>
            <div id="cta" class="section">
                <!-- Background Image -->
                <div class="bg-image bg-parallax overlay" style="background-image:url(uploads/<?= $event['image'] ?>)"></div>
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="white-text"><?= $event['eventName'] ?></h2>
                            <p class="lead white-text"><?= $event['eventDescription'] ?></p>
							<a class="main-button icon-button" href="club-profile-user.php?ClubID=<?= $event['clubID'] ?>">!إذهب لصفحة النادي</a>
							</div>
                    </div>
                </div>
            </div>
<?php
        }
    } else {
?>
    <div id="cta" class="section" style="background: linear-gradient(135deg, #276880 , #cce5f3);">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="white-text">⏳</h2>
                    <p class="lead white-text">ترقبوا جديد سعود!</p>
                </div>
            </div>
        </div>
    </div>
<?php
    }
?>

				
		<div id="why-us" class="section">
			
			<!-- container -->
			<div class="container">

				<div class="section-header text-center">
					<h2>من نحن؟</h2>
					<p class="lead">تعرف على موقعنا!</p>
				</div>

				<!-- Team Section -->
				<section id="ksuhub-team" class="ksuhub-section">
					<div class="ksuhub-container">
						<div class="ksuhub-header text-center">
							<h2 class="section-header text-center">فـــريـقنا</h2>
						</div>
						<div class="ksuhub-team-grid">
							<!-- Team Member 1-->
							<div class="ksuhub-team-card">
								<div class="ksuhub-team-image">
									<img src="img/arwa.jpg" alt="Instructor 1" class="ksuhub-img">
								</div>
								<div class="ksuhub-team-social">
									<a href="https://x.com/ar_aam5" class="ksuhub-social-link"><i class="fab fa-twitter"></i></a>
									<a href="https://www.linkedin.com/in/arwa-a-79b8962a7/" class="ksuhub-social-link"><i class="fab fa-linkedin-in"></i></a>
								</div>
								<div class="ksuhub-team-info">
									<h5 class="ksuhub-instructor-name">أروى المــطيري</h5>
									<p class="ksuhub-instructor-role">تقنـية المعـلومات</p>
								</div>
							</div>

							<!-- Team Member 2-->
							<div class="ksuhub-team-card">
								<div class="ksuhub-team-image">
									<img src="img/jory.jpg" alt="Instructor 2" class="ksuhub-img">
								</div>
								<div class="ksuhub-team-social">
									<a href="https://x.com/iijawharah" class="ksuhub-social-link"><i class="fab fa-twitter"></i></a>
									<a href="https://www.linkedin.com/in/aljawharah-alwabel-9426a3244/" class="ksuhub-social-link"><i class="fab fa-linkedin-in"></i></a>
								</div>
								<div class="ksuhub-team-info">
									<h5 class="ksuhub-instructor-name">الجــوهرة الوابــل</h5>
									<p class="ksuhub-instructor-role">تقنـية المعـلومات</p>
								</div>
							</div>

							<!-- Team Member 3-->
							<div class="ksuhub-team-card">
								<div class="ksuhub-team-image">
									<img src="img/layan.jpg" alt="Instructor 3" class="ksuhub-img">
								</div>
								<div class="ksuhub-team-social">
									<a href="https://x.com/vxwz0?s=21" class="ksuhub-social-link"><i class="fab fa-twitter"></i></a>
									<a href="https://www.linkedin.com/in/layan-alfawzan-217178295/" class="ksuhub-social-link"><i class="fab fa-linkedin-in"></i></a>
								</div>
								<div class="ksuhub-team-info">
									<h5 class="ksuhub-instructor-name">لـيـان الفـوزان</h5>
									<p class="ksuhub-instructor-role">تقنـية المعـلومات</p>
								</div>
							</div>

							<!-- Team Member 4-->
							<div class="ksuhub-team-card">
								<div class="ksuhub-team-image">
									<img src="img/rozie.jpg" alt="Instructor 4" class="ksuhub-img">
								</div>
								<div class="ksuhub-team-social">
									<a href="https://x.com/_lr73" class="ksuhub-social-link"><i class="fab fa-twitter"></i></a>
									<a href="https://www.linkedin.com/in/rose-mady-879895292/" class="ksuhub-social-link"><i class="fab fa-linkedin-in"></i></a>
								</div>
								<div class="ksuhub-team-info">
									<h5 class="ksuhub-instructor-name">روز مــاضي</h5>
									<p class="ksuhub-instructor-role">تقنـية المعـلومات</p>
								</div>
							</div>
						</div>
					</div>
				</section>

				<hr class="section-hr">
				<div class="row">
					<div class="col-md-5 col-md-offset-1"> 
						<a class="about-ksu" href="#">
							<img src="img/aboutUs.webp" alt="">
						</a>
					</div>
					<div class="col-md-6">
						<p class="lead">هدفنا منصة موحدة لجميع نوادي جامعة الملك سعود</p>
						<p>موقعنا هو منصتك لاكتشاف أندية جامعة الملك سعود والانضمام إليها والتفاعل مع أنشطتها. نوفر لك طريقة سهلة للتسجيل في الأندية، متابعة الفعاليات، وتوثيق ساعات التطوع. هدفنا هو تسهيل تجربة المشاركة الطلابية وجعل الحياة الجامعية أكثر إثراءً وتنوعًا.</p>
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

		<!-- preloader -->
		<div id='preloader'><div class='preloader'></div></div>


		<!-- jQuery Plugins -->
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/main.js"></script>

	</body>
</html>