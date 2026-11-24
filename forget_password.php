<?php
session_start();
include('admin/includes/conn.php');
include('phpmailer_smtp/smtp/PHPMailerAutoload.php'); // Include PHPMailer for OTP

// Debugging - Display errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Process OTP request (send OTP via email)
if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    
    // Check if the email exists in the database
    $sql = "SELECT * FROM voters WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $voter_id = $user['voters_id']; // Assuming 'voters_id' is the actual column name for voter ID
        $national_id = $user['national_id']; // Assuming 'national_id' exists in the table
        $otp = rand(100000, 999999);
        $otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes')); // OTP expiry time

        // Update OTP and expiry in the database
        $update_sql = "UPDATE voters SET otp = ?, otp_expiry = ? WHERE email = ?";
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "sss", $otp, $otp_expiry, $email);
        if (mysqli_stmt_execute($stmt)) {
            // OTP sent successfully, now send it via email
            smtp_mailer($email, 'Your OTP Code for Password Reset', 'Dear ' . $user['firstname'] . ',<br>Your OTP for resetting your password is: ' . $otp . '<br>Your Voter ID: ' . $voter_id . '<br>Your National ID: ' . $national_id);

            echo "OTP has been sent to your email.<br>";
            echo "<a href='?email=$email'>Verify OTP and Reset Password</a>";
        } else {
            echo "Error updating OTP in the database.";
        }
    } else {
        echo "Email not found.";
    }
}

// OTP Verification and Password Reset
if (isset($_GET['email']) && isset($_POST['otp']) && isset($_POST['new_password'])) {
    $email = $_GET['email'];
    $entered_otp = $_POST['otp'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    // Fetch the OTP and expiry for the email from the database
    $sql = "SELECT otp, otp_expiry FROM voters WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $otp = $row['otp'];
        $otp_expiry = $row['otp_expiry'];

        // Verify the OTP and check if it is expired
        if ($entered_otp == $otp && strtotime($otp_expiry) > time()) {
            // Update the password and clear OTP fields
            $update_sql = "UPDATE voters SET password = ?, otp = NULL, otp_expiry = NULL WHERE email = ?";
            $stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($stmt, "ss", $new_password, $email);
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['success'] = 'Your password has been reset successfully.';
                header('Location: index.php');
                exit();
            } else {
                $_SESSION['error'] = 'Error updating password.';
            }
        } else {
            $_SESSION['error'] = 'Invalid OTP or OTP has expired.';
        }
    } else {
        $_SESSION['error'] = 'No user found with that email.';
    }
}

// Send OTP via email using PHPMailer
function smtp_mailer($to, $subject, $msg) {
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = "mail";
    $mail->Password = "your app password";  // Replace with actual app password
    $mail->SetFrom("your mail");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    
    if (!$mail->Send()) {
        echo $mail->ErrorInfo;
        return false;
    }
    return true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Forgot Password</h2>

    <?php if (!isset($_GET['email'])) { ?>
        <form action="" method="post">
            <label for="email">Enter your email:</label>
            <input type="email" name="email" required><br><br>
            <input type="submit" name="submit" value="Send OTP">
        </form>
    <?php } else { ?>
        <h3>Verify OTP and Reset Password</h3>
        <form action="" method="post">
            <label for="otp">Enter OTP:</label>
            <input type="text" name="otp" required><br><br>
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" required><br><br>
            <input type="submit" name="submit" value="Reset Password">
        </form>
    <?php } ?>

    <?php
    if (isset($_SESSION['error'])) {
        echo "<p style='color:red'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo "<p style='color:green'>" . $_SESSION['success'] . "</p>";
        unset($_SESSION['success']);
    }
    ?>
</body>
</html>
