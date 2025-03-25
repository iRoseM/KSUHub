<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_connection.php';

// Predefined admin emails from your database
$adminEmails = [
    'ai@club.com',
    'csas@club.com',
    'cybersecurity@club.com',
    'diu@club.com',
    'entrhip@club.com',
    'ftc@club.com',
    'marketing@club.com',
    'physt@club.com'
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $fullName = trim($conn->real_escape_string($_POST['fullName']));
    $password = trim($_POST['password']);
    $phoneNo = trim($conn->real_escape_string($_POST['phoneNo']));
    $email = trim($conn->real_escape_string($_POST['email']));
    $college = trim($conn->real_escape_string($_POST['college']));
    $studyingLevel = trim($conn->real_escape_string($_POST['studyingLevel']));
    $bio = "";

    // Validation patterns
    $phonePattern = "/^05[0-9]{8}$/";
    $emailPattern = "/^4.+@student\.ksu\.edu\.sa$/";
    $passwordPattern = "/^.{8,}$/";

    // 1. Validate email format (must be student email)
    if (!preg_match($emailPattern, $email)) {
        echo "<script>alert('يجب أن يبدأ البريد الإلكتروني بـ 4 وينتهي بـ @student.ksu.edu.sa'); window.location.href = 'signup.html';</script>";
        exit();
    }

    // 2. Check against admin emails
    if (in_array(strtolower($email), array_map('strtolower', $adminEmails))) {
        echo "<script>alert('هذا البريد الإلكتروني محجوز للإدارة'); window.location.href = 'signup.html';</script>";
        exit();
    }

    // 3. Validate phone number
    if (!preg_match($phonePattern, $phoneNo)) {
        echo "<script>alert('رقم الجوال يجب أن يبدأ بـ 05 ويتكون من 10 أرقام'); window.location.href = 'signup.html';</script>";
        exit();
    }

    // 4. Validate password strength
    if (!preg_match($passwordPattern, $password)) {
        echo "<script>alert('كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل مع رقم واحد'); window.location.href = 'signup.html';</script>";
        exit();
    }

    // Check if email exists in either table (case-insensitive)
    $checkEmail = $conn->prepare("
        SELECT email FROM studentuser WHERE LOWER(email) = LOWER(?)
        UNION
        SELECT email FROM adminuser WHERE LOWER(email) = LOWER(?)
    ");
    $checkEmail->bind_param("ss", $email, $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('البريد الإلكتروني مسجل مسبقاً'); window.location.href = 'signup.html';</script>";
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert student with NULL clubID (will be updated when joining a club)
    $stmt = $conn->prepare("
        INSERT INTO studentuser 
        (email, password, fullName, phoneNo, college, studyingLevel, bio, volunteeringHours, profileImg, clubID) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 0, 'profileIcon.png', NULL)
    ");
    $stmt->bind_param("sssssss", $email, $hashedPassword, $fullName, $phoneNo, $college, $studyingLevel, $bio);

    if ($stmt->execute()) {
        // Set session variables
        $_SESSION['user_email'] = $email;
        $_SESSION['user_type'] = "student";
        $_SESSION['fullName'] = $fullName;
        $_SESSION['college'] = $college;
        $_SESSION['profileImg'] = 'profileIcon.png';
        
        echo "<script>
            alert('تم تسجيل الحساب بنجاح!');
            window.location.href = 'student-profile.php';
        </script>";
        exit();
    } else {
        error_log("Database error: " . $conn->error);
        echo "<script>
            alert('حدث خطأ أثناء التسجيل. الرجاء المحاولة لاحقاً.');
            window.location.href = 'signup.html';
        </script>";
        exit();
    }
}
?>