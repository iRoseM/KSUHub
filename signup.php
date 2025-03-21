<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_connection.php'; // Ensure this file properly connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    exit();
    // Validate that all required fields are set
    if (
        isset($_POST['fullName'], $_POST['password'], $_POST['phoneNo'], $_POST['email'], $_POST['college'], $_POST['studyingLevel'])
    ) {
        // Retrieve form data
        $fullName = $_POST['fullName'];
        $password = $_POST['password'];
        $phoneNo = $_POST['phoneNo'];
        $email = $_POST['email'];
        $college = $_POST['college'];
        $studyingLevel = $_POST['studyingLevel'];
        $bio = ""; // Optional field, can be left empty
        $clubID = NULL; // Assuming clubID is optional and can be NULL

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

            // Redirect to the student dashboard or home page
            header("Location: student-profile.html");
            exit();
        } else {
            // Handle database insertion error
            echo "<script>alert('حدث خطأ أثناء التسجيل'); window.location.href = 'signup.html';</script>";
            exit();
        }
    } else {
        // Handle missing form fields
        echo "<script>alert('يرجى ملء جميع الحقول المطلوبة'); window.location.href = 'signup.html';</script>";
        exit();
    }
}
?>