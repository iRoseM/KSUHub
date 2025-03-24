<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>KSUHub | النوادي</title>
		
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

		<!-- Icons -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">


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
						<li><a href="student-profile.php">الملف الشخصي</a></li>
					
						<li class="logout-item"><a href="logout.php" class="logout-button" style="margin-right: 0;"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a></li>
					</ul>
				</nav>

			</div>
		</header>

		<div class="hero-area section">
			<!-- Backgound Image -->
			<div class="bg-image bg-parallax overlay" style="background-image:url(./img/home-background.png)"></div>

			<div class="container">
				<div class="row">
					<div class="col-md-10 col-md-offset-1 text-center">
						<h1 style="text-align: center;" class="white-text">صفحة النوادي</h1>
					</div>
				</div>
			</div>
		</div>

		<!-- Clubs -->
		<div id="club" class="section">

			<!-- container -->
			<div class="container">
				<div class="row">
                                    
					<!-- main club -->
					<div id="main" class="col-md-9">
						
                                            <?php
                                            include 'db_connection.php';

                                            $search = isset($_GET['search']) ? $_GET['search'] : '';
                                            $sql = "SELECT * FROM adminuser";

                                            if (!empty($search)) {
                                                $search = $conn->real_escape_string($search);
                                                $sql .= " WHERE clubName LIKE '%$search%'";
                                            }
                                            
                                            $college = isset($_GET['college']) ? $_GET['college'] : '';
                                            if (!empty($college)) {
                                                $college = $conn->real_escape_string($college);
                                                if (strpos($sql, 'WHERE') !== false) {
                                                    $sql .= " AND affiliation LIKE '%$college%'";
                                                } else {
                                                    $sql .= " WHERE affiliation LIKE '%$college%'";
                                                }
                                            }
                                            $result = $conn->query($sql);
                                            
                                            
                                            
                                            
                                            
                                            if ($result->num_rows > 0) {
                                                while ($club = $result->fetch_assoc()) {
                                                    ?>
                                                    <div class="col-md-6">
                                                        <div class="single-club">
                                                            <div class="club-img">
                                                                <a href="#" class="clubs-clubimg">
                                                                    <img src="#" alt="<?= $club['clubName'] ?>">
                                                                </a>
                                                            </div>
                                                            <div class="club-name-container">
                                                                <a class="club-name" href="#">
                                                                    <?= $club['clubName'] ?>
                                                                </a>
                                                            </div>
                                                            <div class="club-meta">
<!--                                                                <span class="club-college"> php $club['affiliation'] php </span>-->
                                                                <div class="pull-right">
                                                                    <span class="club-meta-author" style="color: <?= $club['is_open'] ? 'green' : 'red' ?>;">
                                                                        <p>Unkown</p>
                                                                            <!--How could "availability" be implemented when we don't have a count for members in each club? nor the membership count restrictions of each club-->
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            } else {
                                                echo '<p class="text-center">لا توجد أندية حالياً.</p>';
                                            }
                                            ?>

						<div class="row">
							
                                                    <!-- pagination -->
							<div class="col-md-12">
								<div class="post-pagination">
									<a href="#" class="pagination-back pull-left"><i class="fas fa-arrow-left"></i> للخلف</a>
									<ul class="pages">
										<li class="active">1</li>
										<li><a href="#">2</a></li>
										<li><a href="#">3</a></li>
										<li><a href="#">4</a></li>
									</ul>
									<a href="#" class="pagination-next pull-right">للأمام<i class="fas fa-arrow-right"></i></a>
								</div>
							</div>
						</div>
					</div>
					
					<!-- aside club -->
					<div id="aside" class="col-md-3">

						<!-- search widget -->
						<div class="widget search-widget">                                                        
                                                        <form method="GET" action="clubs.php">
                                                            <input class="input" type="text" name="search" placeholder="ابحث عن نادي..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                                                            <button type="submit"><i class="fa fa-search"></i></button>
                                                        </form> 
						</div>

						<!-- category widget -->
						<div class="widget category-widget">
							<h3>الكليات</h3>
							<a class="category" href="#">CCIS <span>9</span></a> <!-- computer science -->
							<a class="category" href="#">CBA<span>4</span></a> <!--Business-->
							<a class="category" href="#">CD <span>5</span></a> <!-- midcine-->
							<a class="category" href="#">CAMS <span>7</span></a> <!-- applied medical sciences-->
							<a class="category" href="#">CTA <span>3</span></a> <!-- tournism and arcmaeology السياحة-->                                 
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

                
                
		<!-- preloader -->
		<div id='preloader'><div class='preloader'></div></div>


		<!-- jQuery Plugins -->
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/main.js"></script>

	</body>
</html>
