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
    $volunteeringHours = 0; // Default value

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
    $stmt = $conn->prepare("INSERT INTO studentuser (email, password, fullName, phoneNo, college, studyingLevel, bio, volunteeringHours) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssi", $email, $hashedPassword, $fullName, $phoneNo, $college, $studyingLevel, $bio, $volunteeringHours);

    if ($stmt->execute()) {
        // Set session variables
        $_SESSION['user_email'] = $email;
        $_SESSION['user_type'] = "student";

        // Redirect to the student dashboard or home page
        header("Location: indexStudent.php");
        exit();
    } else {
        // Handle database insertion error
        echo "<script>alert('حدث خطأ أثناء التسجيل'); window.location.href = 'signup.html';</script>";
        exit();
    }
}

// If the script reaches here, it means the request method is not POST
// Do not display any error message or redirect
?>