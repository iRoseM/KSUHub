<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
include 'db_connection.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']); 

    if (empty($email) || empty($password) || empty($role)) {
        echo "<script>alert('يرجى ملء جميع الحقول'); window.location.href = 'login.html';</script>";
        exit();
    }

    $valid_roles = ['student' => 'studentuser', 'clubAdmin' => 'adminuser'];
    if (!isset($valid_roles[$role])) {
        echo "<script>alert('دور غير صالح'); window.location.href = 'login.html';</script>";
        exit();
    }

    $table = $valid_roles[$role];

    if ($role == "clubAdmin") {
        $stmt = $conn->prepare("SELECT email, password, clubID, clubName, clubGoal FROM adminuser WHERE email = ?");
    } else {
        $stmt = $conn->prepare("SELECT email, password, fullName, phoneNo, college, studyingLevel, bio, volunteeringHours, profileImg, clubID FROM studentuser WHERE email = ?");
    }

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $role;
            $_SESSION['fullName'] = $user['fullName']; 
            $_SESSION['userPhone'] = $user['phoneNo']; 
            
            if ($role == "clubAdmin") {
                $_SESSION['ClubID'] = $user['clubID']; 
                header("Location: club-profile-admin.php");
            } else {
                header("Location: student-profile.php");
            }
            exit();
        } else {
            echo "<script>alert('كلمة المرور غير صحيحة'); window.location.href = 'login.html';</script>";
            exit();
        }
    } else {
        echo "<script>alert('البريد الإلكتروني غير مسجل'); window.location.href = 'login.html';</script>";
        exit();
    }
}
?>
