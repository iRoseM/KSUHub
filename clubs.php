<?php
session_start();

?>

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

						$sql = "SELECT * FROM adminuser";

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
								// استخراج ClubID وضمان أنه عدد صحيح
								$clubID = isset($club['clubID']) ? intval($club['clubID']) : 0;

								// تحقق من وجود ClubID وصياغة الرابط بناءً عليه
								if ($clubID > 0) {
									$clubLink = "club-profile-user.php?ClubID=" . $clubID;
								} else {
									$clubLink = "#";
								}
						?>
        <div class="col-md-6 club-card" data-name="<?= strtolower($club['clubName']) ?>" data-college="<?= strtolower($club['clubCollege']) ?>">
            <div class="single-club">
                <div class="club-img">
                    <a href="<?= $clubLink ?>" class="clubs-clubimg">
                        <img src="uploads/<?= $club['image'] ?>" alt="<?= $club['clubName'] ?>">
                    </a>
                </div>
                <div class="club-name-container">
                    <a class="club-name" href="<?= $clubLink ?>">
                        <?= $club['clubName'] ?>
                    </a>
                </div>
                <div class="club-meta">
                    <span class="club-college"><?= $club['clubCollege'] ?></span>
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
						<div class="row">
							<div class="col-md-12">
								<div class="post-pagination">
									<ul id="pagination" class="pages"></ul>
								</div>
							</div>
						</div>
					</div>
					
					<!-- aside club -->
					<div id="aside" class="col-md-3">
						
						<div class="widget search-widget">
							<form id="searchForm">
								<input id="clubSearch" class="input" type="text" placeholder="ابحث عن نادي...">
								<button type="submit"><i class="fa fa-search"></i></button>
							</form>
						</div>

						<!-- category widget-->
						<div class="widget category-widget">
							<h3>الكليات</h3>
							<a class="category" data-college="كلية علوم الحاسب والمعلومات"> كلية علوم الحاسب والمعلومات</a>
							<a class="category" data-college="كلية إدارة الأعمال">كلية إدارة الأعمال</a>
							<a class="category" data-college="معهد ريادة الأعمال">معهد ريادة الأعمال</a>
							<a class="category" data-college="كلية العلوم الطبية التطبيقية">كلية العلوم الطبية التطبيقية</a>
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
		
		<script>
		document.addEventListener('DOMContentLoaded', function () {
			const searchInput = document.getElementById('clubSearch');
			const allCards = Array.from(document.querySelectorAll('.club-card'));
			const paginationContainer = document.getElementById('pagination');
			const collegeFilters = document.querySelectorAll('.category');
			const searchForm = document.getElementById('searchForm');

			let activeCollege = '';
			const cardsPerPage = 4;
			let currentPage = 1;
			let filteredCards = [];

			// Reset search input on hard refresh
			if (window.performance && window.performance.navigation.type === 1) {
				searchInput.value = '';
			}

			function toggleNoResults(show) {
				const noResults = document.getElementById('no-results');
				if (noResults) noResults.style.display = show ? 'block' : 'none';
			}

			function renderPagination(totalPages) {
			const paginationList = document.getElementById('pagination');
			paginationList.innerHTML = '';

			if (totalPages <= 1) return;

			for (let i = 1; i <= totalPages; i++) {
				const li = document.createElement('li');
				if (i === currentPage) {
					li.classList.add('active');
					li.textContent = i;
				} else {
					const a = document.createElement('a');
					a.href = "#";
					a.textContent = i;
					a.addEventListener('click', (e) => {
						e.preventDefault();
						currentPage = i;
						showCurrentPage();
					});
					li.appendChild(a);
				}
				paginationList.appendChild(li);
			}
		}


			function showCurrentPage() {
				allCards.forEach(card => card.style.display = 'none');

				const totalPages = Math.ceil(filteredCards.length / cardsPerPage);
				const start = (currentPage - 1) * cardsPerPage;
				const end = start + cardsPerPage;

				filteredCards.slice(start, end).forEach(card => {
					card.style.display = 'block';
				});

				renderPagination(totalPages);
				toggleNoResults(filteredCards.length === 0);
			}

			function filterClubs() {
				const keyword = searchInput.value.toLowerCase();

				filteredCards = allCards.filter(card => {
					const name = card.dataset.name;
					const college = card.dataset.college.toLowerCase();
					const matchesName = name.includes(keyword);
					const matchesCollege = !activeCollege || college === activeCollege.toLowerCase();

					return matchesName && matchesCollege;
				});

				currentPage = 1;
				showCurrentPage();
			}

			// Event: Live search
			searchInput.addEventListener('input', filterClubs);

			// Event: Filter by college
			collegeFilters.forEach(filter => {
				filter.addEventListener('click', function () {
					activeCollege = this.dataset.college;
					collegeFilters.forEach(f => f.classList.remove('active'));
					this.classList.add('active');
					filterClubs();
				});
			});

			// Event: Prevent form reload
			searchForm.addEventListener('submit', function (e) {
				e.preventDefault();
				filterClubs();
			});

			// Initial render
			filterClubs();
		});
		</script>

	</body>
</html>