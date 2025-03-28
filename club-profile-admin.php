<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
session_start();
include_once 'db_connection.php';

// Check if the user is logged in and is a student
/*
if (!isset($_SESSION['user_email']) || $_SESSION['user_type'] != "clubAdmin") {
    header("Location: index.html");
    exit();
}*/

if (isset($_SESSION['ClubID']) && ctype_digit(strval($_SESSION['ClubID']))) {
    $clubID = intval($_SESSION['ClubID']); 

    $sql = "SELECT clubName, clubVision, ClubGoal ,image, clubAccount FROM adminuser WHERE ClubID = ?";
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
        $clubGoal = $row["ClubGoal"];
    } else {
        echo "<p>النادي غير موجود</p>";
        exit;
    }


$membersQuery = "SELECT s.email, s.fullName, s.college, s.studyingLevel, s.bio FROM membership m 
                 JOIN studentuser s ON m.email = s.email 
                 WHERE m.status = 'Approved' AND m.clubID = ?";
$stmt = $conn->prepare($membersQuery);
$stmt->bind_param("i", $clubID);
$stmt->execute();
$membersResult = $stmt->get_result();

$requestsQuery = "SELECT s.email, s.fullName, s.college, s.studyingLevel, s.bio FROM membership m 
                  JOIN studentuser s ON m.email = s.email 
                  WHERE m.status = 'Pending' AND m.clubID = ?";
$stmt = $conn->prepare($requestsQuery);
$stmt->bind_param("i", $clubID);
$stmt->execute();
$requestsResult = $stmt->get_result();



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

    $stmt->close();
    $stmtEvents->close();
} else {
    echo "<p>ID غير صحيح أو لم يتم تسجيل الدخول كأدمن</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['email'])) {
    header("Content-Type: application/json");
    
    $email = $_POST['email'];
    $action = $_POST['action'];
    $clubID = $_SESSION['ClubID'];
    
    // تحديد الحالة بناء على الإجراء
    $status = ($action == 'accept') ? 'Approved' : 'Rejected';
    
    // تحديث الحالة في قاعدة البيانات
    $sql = "UPDATE membership SET status = ? WHERE email = ? AND clubID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $email, $clubID);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم تحديث الحالة بنجاح"]);
    } else {
        echo json_encode(["success" => false, "message" => "حدث خطأ أثناء التحديث: " . $stmt->error]);
    }
    
    $stmt->close();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_member' && isset($_POST['email'])) {
    header("Content-Type: application/json");
    
    $email = $_POST['email'];
    $clubID = $_SESSION['ClubID'];
    
    // حذف العضو من جدول العضوية
    $sql = "DELETE FROM membership WHERE email = ? AND clubID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $clubID);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "تم حذف العضو بنجاح"]);
    } else {
        echo json_encode(["success" => false, "message" => "حدث خطأ أثناء الحذف: " . $stmt->error]);
    }
    
    $stmt->close();
    exit;
}

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
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="css/font-awesome.min.css">

    <!-- Custom stylesheet -->
    <link type="text/css" rel="stylesheet" href="css/style.css">

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
        
        .editclubbtn{
            background-color: #FF6700;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            width: 100%; 
            max-width: 500px; 
            text-align: center;
            margin-top: 15px; 
        }
        .editclubbtn:hover{
            background-color: #d55a00;
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
            

    <div id="club-home" class="hero-area">
        <!-- Background Image -->
        <div class="bg-image bg-parallax overlay" style="background-image:url(uploads/<?php echo $clubImage; ?>); background-size: cover; background-position: center; background-repeat: no-repeat; height: 80vh; width: 100%;"></div>
        <!-- /Background Image -->

        <div class="container">
            <div class="row">
                <div class="col-md-8" style="margin-top:30rem;">
                    <h1 class="white-text" ><?php echo "مرحبا بكم في " . $clubName; ?></h1>
                    <p class="lead white-text">Join us and be part of a great community.</p>
                </div>
            </div>
        </div>
        <div class="btn-container" style="display: flex; justify-content: right;  margin-top: 23rem; margin-right: 5px;">
            <form action="edit-club-profile.php" method="POST">
                <button type="submit" class="editclubbtn">تعديل ملف النادي</button>
            </form>
        </div>

        <div class="center-btn-container" style="display: flex; justify-content: center; gap: 15px;  margin-top: 10rem;">
            
            <a class="main-button icon-button" href="#membership-requests">طلبات العضوية</a>
            <a class="main-button icon-button" href="#members">لمحة عن الأعضاء</a>
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
        <p><?php echo htmlspecialchars($clubVision, ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
</div>

<?php if (!empty($clubGoal)) { ?>
    <div class="feature">
        <div class="feature-content">
            <h4>الرسالة:</h4>
            <p><?php echo htmlspecialchars($clubGoal, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>
<?php } ?>

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


    <div class="Home" style="margin-top: 0rem;"> 
    <div id="members" class="section py-5">
        <div class="container">
            <div class="section-header text-center mb-4">
                <h2 class="fw-bold">لمحة عن الأعضاء</h2>
            </div>
            <div class="row justify-content-center">
                <?php if ($membersResult->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">الاسم</th>
                                    <th scope="col">الكلية</th>
                                    <th scope="col">المستوى الدراسي</th>
                                    <th scope="col">النبذة</th>
                                    <th scope="col">إدارة العضو</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $membersResult->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo !empty($row['fullName']) ? htmlspecialchars($row['fullName']) : 'اسم غير متوفر'; ?></td>
                                        <td><?php echo !empty($row['college']) ? htmlspecialchars($row['college']) : 'كلية غير متوفرة'; ?></td>
                                        <td><?php echo !empty($row['studyingLevel']) ? htmlspecialchars($row['studyingLevel']) : 'مستوى غير متوفر'; ?></td>
                                        <td><?php echo !empty($row['bio']) ? nl2br(htmlspecialchars($row['bio'])) : 'لا توجد نبذة'; ?></td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-danger delete-member" 
                                                    data-email="<?php echo !empty($row['email']) ? htmlspecialchars($row['email']) : ''; ?>" 
                                                    aria-label="Delete member <?php echo !empty($row['fullName']) ? htmlspecialchars($row['fullName']) : 'Unknown'; ?>">
                                                    حذف العضو
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center fs-5 text-muted">لا يوجد أعضاء</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div id="membership-requests" class="section py-5">
        <div class="container">
            <div class="section-header text-center mb-4">
                <h2 class="fw-bold">طلبات العضوية</h2>
            </div>
            <div class="row justify-content-center">
                <?php if ($requestsResult->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">الاسم</th>
                                    <th scope="col">الكلية</th>
                                    <th scope="col">المستوى الدراسي</th>
                                    <th scope="col">نبذة</th>
                                    <th scope="col">حالة الطلب</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $requestsResult->fetch_assoc()): ?>
                                    <tr>
                                       <td><?php echo !empty($row['fullName']) ? htmlspecialchars($row['fullName']) : 'اسم غير متوفر'; ?></td>
                                       <td><?php echo !empty($row['college']) ? htmlspecialchars($row['college']) : 'كلية غير متوفرة'; ?></td>
                                       <td><?php echo !empty($row['studyingLevel']) ? htmlspecialchars($row['studyingLevel']) : 'مستوى غير متوفر'; ?></td>
                                       <td><?php echo !empty($row['bio']) ? nl2br(htmlspecialchars($row['bio'])) : 'لا توجد نبذة'; ?></td>
                                       <td>
    <div class="d-flex justify-content-center gap-2">
        <button class="btn btn-success accept-request" 
            data-email="<?php echo !empty($row['email']) ? htmlspecialchars($row['email']) : ''; ?>" 
            aria-label="Accept request from <?php echo !empty($row['fullName']) ? htmlspecialchars($row['fullName']) : 'Unknown'; ?>">
            قبول
        </button>
        <button class="btn btn-danger reject-request" 
            data-email="<?php echo !empty($row['email']) ? htmlspecialchars($row['email']) : ''; ?>" 
            aria-label="Reject request from <?php echo !empty($row['fullName']) ? htmlspecialchars($row['fullName']) : 'Unknown'; ?>">
            رفض
        </button>
    </div>
</td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center fs-5 text-muted">لا يوجد طلبات عضوية</p>
                <?php endif; ?>
            </div>
        </div>
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
document.addEventListener("DOMContentLoaded", function () {
    // معالجة قبول/رفض الطلبات
    document.querySelectorAll(".accept-request, .reject-request").forEach(button => {
        button.addEventListener("click", function () {
            let email = this.getAttribute("data-email");
            let isAccept = this.classList.contains("accept-request");
            let action = isAccept ? "accept" : "reject";
            let requestRow = this.closest("tr");
            let buttonGroup = requestRow.querySelectorAll("button");

            // تعطيل الأزرار أثناء المعالجة
            buttonGroup.forEach(btn => {
                btn.disabled = true;
                btn.classList.add("disabled");
            });

            // إعداد بيانات الإرسال
            const formData = new FormData();
            formData.append('email', email);
            formData.append('action', action);

            // إرسال الطلب
            fetch(window.location.href, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `email=${encodeURIComponent(email)}&action=${encodeURIComponent(action)}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then(data => {
                console.log("Server response:", data); // للتصحيح
                if (data.success) {
                    requestRow.remove();
                } else {
                    alert("خطأ: " + (data.message || "حدث خطأ غير معروف"));
                    buttonGroup.forEach(btn => {
                        btn.disabled = false;
                        btn.classList.remove("disabled");
                    });
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("حدث خطأ في الاتصال بالخادم");
                buttonGroup.forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove("disabled");
                });
            });
        });
    });

    // معالجة حذف الأعضاء
    document.querySelectorAll(".delete-member").forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            
            if (!confirm("هل أنت متأكد من رغبتك في حذف هذا العضو؟")) {
                return false;
            }

            let email = this.getAttribute("data-email");
            let memberRow = this.closest("tr");
            let buttonGroup = memberRow.querySelectorAll("button");

            // تعطيل الأزرار أثناء المعالجة
            buttonGroup.forEach(btn => {
                btn.disabled = true;
                btn.classList.add("disabled");
            });

            // إعداد بيانات الإرسال
            const formData = new FormData();
            formData.append('email', email);
            formData.append('action', 'delete_member');

            // إرسال الطلب
            fetch(window.location.href, {
                method: "POST",
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    memberRow.remove();
                    alert("تم حذف العضو بنجاح");
                } else {
                    alert("خطأ: " + (data.message || "حدث خطأ غير معروف"));
                    buttonGroup.forEach(btn => {
                        btn.disabled = false;
                        btn.classList.remove("disabled");
                    });
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("حدث خطأ في الاتصال بالخادم");
                buttonGroup.forEach(btn => {
                    btn.disabled = false;
                    btn.classList.remove("disabled");
                });
            });
        });
    });
});
</script>

</body>
</html>
