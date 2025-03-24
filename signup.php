<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_connection.php'; // Ensure this file properly connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $fullName = $_POST['fullName'];
    $password = $_POST['password'];
    $phoneNo = $_POST['phoneNo'];
    $email = $_POST['email'];
    $college = $_POST['college'];
    $studyingLevel = $_POST['studyingLevel'];
    $bio = ""; // Optional field, can be left empty
    $clubID = NULL; // Assuming clubID is optional and can be NULL

    // Define validation patterns
    $phonePattern = "/^05[0-9]{8}$/"; // Phone number must start with 05 and be 10 digits long
    $emailPattern = "/^4.+@student\.ksu\.edu\.sa$/"; // Email must start with 4 and end with @student.ksu.edu.sa
    $passwordPattern = "/^.{8,}$/"; // Password must be at least 8 characters long

    // Validate phone number
    if (!preg_match($phonePattern, $phoneNo)) {
        echo "<script>alert('رقم الجوال غير صحيح. يجب أن يبدأ بـ 05 ويتكون من 10 أرقام.'); window.location.href = 'signup.html';</script>";
        exit();
    }

    // Validate email
    if (!preg_match($emailPattern, $email)) {
        echo "<script>alert('البريد الإلكتروني غير صحيح. يجب أن يبدأ بـ 4 وينتهي بـ @student.ksu.edu.sa'); window.location.href = 'signup.html';</script>";
        exit();
    }

    // Validate password
    if (!preg_match($passwordPattern, $password)) {
        echo "<script>alert('كلمة المرور يجب أن تتكون من 8 أحرف على الأقل.'); window.location.href = 'signup.html';</script>";
        exit();
    }

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Check if email already exists in the studentuser table
    $checkEmail = $conn->prepare("SELECT email FROM studentuser WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        // Email already exists, display error message as a popup
        echo "<script>alert('البريد الإلكتروني مسجل مسبقًا'); window.location.href = 'signup.html';</script>";
        exit();
    }

    // Insert new student user
    $stmt = $conn->prepare("INSERT INTO studentuser (email, password, fullName, phoneNo, college, studyingLevel, bio, clubID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $email, $hashedPassword, $fullName, $phoneNo, $college, $studyingLevel, $bio, $clubID);

    if ($stmt->execute()) {
        // Set session variables
        $_SESSION['user_email'] = $email;
        $_SESSION['user_type'] = "student";

        // Display success message and redirect
        echo "<script>alert('تم تسجيل الحساب بنجاح!'); window.location.href = 'student-profile.php';</script>";
        exit();
    } else {
        // Handle database insertion error
        echo "<script>alert('حدث خطأ أثناء التسجيل'); window.location.href = 'signup.html';</script>";
        exit();
    }
}
?>