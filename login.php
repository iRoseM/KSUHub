<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_connection.php'; // Ensure this file properly connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Role: "student" or "clubAdmin"

    // Validate that all fields are set
    if (empty($email) || empty($password) || empty($role)) {
        echo "<script>alert('يرجى ملء جميع الحقول'); window.location.href = 'login.html';</script>";
        exit();
    }

    // Determine the table and user type based on the selected role
    if ($role == "student") {
        $table = "studentuser";
        $userType = "student";
    } elseif ($role == "clubAdmin") {
        $table = "adminuser";
        $userType = "clubAdmin";
    }

    // Fetch user data from the database
    $stmt = $conn->prepare("SELECT email, password FROM $table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // User found, verify password
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_type'] = $userType;

            // Redirect to the appropriate dashboard
            if ($userType == "student") {
                header("Location: student-profile.php");
            } elseif ($userType == "clubAdmin") {
                header("Location: club-profile-admin.html");
            }
            exit();
        } else {
            // Incorrect password
            echo "<script>alert('كلمة المرور غير صحيحة'); window.location.href = 'login.html';</script>";
            exit();
        }
    } else {
        // User not found
        echo "<script>alert('البريد الإلكتروني غير مسجل'); window.location.href = 'login.html';</script>";
        exit();
    }
}
?>