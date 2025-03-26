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
		<style>
			.category-filter.active {
				font-weight: bold;
				color: #007bff;
			}
			#pagination .page-btn {
				background: #fff;
				border: 1px solid #ccc;
				padding: 6px 12px;
				margin: 2px;
				cursor: pointer;
			}

			#pagination .page-btn.active {
				background: #007bff;
				color: #fff;
				border-color: #007bff;
			}

		</style>

    </head>
	<body>
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const searchInput = document.getElementById('clubSearch');
			const cards = Array.from(document.querySelectorAll('.club-card')); // Converted to array for pagination
			const paginationContainer = document.getElementById('pagination');
			let activeCollege = '';
			const cardsPerPage = 6;
			let currentPage = 1;

			// Filter + paginate visible clubs
			function filterClubs() {
				const keyword = searchInput.value.toLowerCase();

				cards.forEach(card => {
					const name = card.dataset.name;
					const college = card.dataset.college;
					const matchesName = name.includes(keyword);
					const matchesCollege = !activeCollege || college === activeCollege;

					if (matchesName && matchesCollege) {
						card.style.display = 'block';
					} else {
						card.style.display = 'none';
					}
				});

				currentPage = 1;
				paginate();
			}

			function getVisibleCards() {
				return cards.filter(card => card.style.display !== 'none');
			}

			function paginate() {
				const visibleCards = getVisibleCards();
				const totalPages = Math.ceil(visibleCards.length / cardsPerPage);

				// Hide all first
				cards.forEach(card => card.style.display = 'none');

				// Show only current page cards
				const start = (currentPage - 1) * cardsPerPage;
				const end = start + cardsPerPage;
				visibleCards.slice(start, end).forEach(card => card.style.display = 'block');

				renderPagination(totalPages);
				toggleNoResults(visibleCards.length === 0);
			}

			function renderPagination(totalPages) {
				paginationContainer.innerHTML = '';

				if (totalPages <= 1) return;

				for (let i = 1; i <= totalPages; i++) {
					const btn = document.createElement('button');
					btn.textContent = i;
					btn.className = 'page-btn';
					if (i === currentPage) btn.classList.add('active');
					btn.addEventListener('click', () => {
						currentPage = i;
						paginate();
					});
					paginationContainer.appendChild(btn);
				}
			}

			function toggleNoResults(show) {
				const noResults = document.getElementById('no-results');
				if (noResults) noResults.style.display = show ? 'block' : 'none';
			}

			// Event: Search typing
			searchInput.addEventListener('input', filterClubs);

			// Event: College filter click
			const collegeFilters = document.querySelectorAll('.category-filter');
			collegeFilters.forEach(filter => {
				filter.addEventListener('click', function () {
					activeCollege = this.dataset.college;
					collegeFilters.forEach(f => f.classList.remove('active'));
					this.classList.add('active');
					filterClubs();
				});
			});

			// Initial render
			filterClubs();
		});
	</script>


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
                            <li><a href="home.php">الصفحة الرئيسية</a></li> <!-- Changed to .php -->
                            <li><a href="clubs.php">النوادي</a></li> <!-- Changed to .php -->
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
						<p id="no-results" class="text-center" style="display: none; margin-top: 20px;">لا توجد نتائج مطابقة لبحثك</p>
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
                                                    <div class="col-md-6 club-card" data-name="<?= strtolower($club['clubName']) ?>">
														<div class="single-club">
															<div class="club-img">
																<a href="club-profile-user.php?id=<?= $club['clubID'] ?>" class="clubs-clubimg">
																	<img src="uploads/<?= $club['image'] ?>" alt="<?= $club['clubName'] ?>">
																</a>
															</div>
															<div class="club-name-container">
																<a class="club-name" href="club-profile-user.php?id=<?= $club['clubID'] ?>">
																	<?= $club['clubName'] ?>
																</a>
															</div>
														</div>
													</div>
                                                    <?php
                                                }
                                            } else {
                                                echo '<p class="text-center">لا توجد أندية حالياً.</p>';
                                            }
                                            ?>
											<!-- Pagination Container -->
											<div id="pagination" class="text-center" style="margin-top: 30px;"> </div>
					</div>
					
					<!-- aside club -->
					<div id="aside" class="col-md-3">

						<!-- search widget -->
						<div class="widget search-widget">                                                        
							<form method="GET" action="clubs.php">
								<input id="clubSearch" class="input" type="text" name="search" placeholder="ابحث عن نادي...">
								<button type="submit"><i class="fa fa-search"></i></button>
							</form> 
						</div>

						<!-- category widget (if we added colleges) the script to this part is still not deleted yet-->
						<div class="widget category-widget">
							<h3>الكليات</h3>
							<a class="category-filter" data-college="ccis">CCIS</a>
							<a class="category-filter" data-college="cba">CBA</a>
							<a class="category-filter" data-college="cd">CD</a>
							<a class="category-filter" data-college="cams">CAMS</a>
							<a class="category-filter" data-college="cta">CTA</a>
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

                
                
		<!-- preloader -->
		<div id='preloader'><div class='preloader'></div></div>


		<!-- jQuery Plugins -->
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/main.js"></script>

	</body>
</html>