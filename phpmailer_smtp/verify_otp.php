<?php
session_start();
include('admin/includes/conn.php');  // Correct path to the database connection file


// Get action from URL (either 'signup' or 'forgot')
$action = $_GET['action'];
$email = $_GET['email'];  // Email passed as query parameter

if (isset($_POST['submit'])) {
    $otp_entered = $_POST['otp'];

    // Fetch OTP and expiry from the database
    $sql = "SELECT otp, otp_expiry, status FROM voters WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $db_otp = $row['otp'];
        $db_otp_expiry = $row['otp_expiry'];

        // Check if the entered OTP matches the one in the database and if it is not expired
        if ($otp_entered == $db_otp && strtotime($db_otp_expiry) > time()) {
            // OTP verified successfully, perform action based on the 'action' parameter
            if ($action == 'signup') {
                // For signup verification, update the user status to 'active'
                $update_status = "UPDATE voters SET status = 1 WHERE email = '$email'";
                mysqli_query($conn, $update_status);
                echo "Signup successful! Your account is now verified.";
                echo "<br><a href='login.php'>Login</a>";
            } elseif ($action == 'forgot') {
                // For forgot password, redirect to password reset page
                echo "OTP verified! Please reset your password.";
                echo "<br><a href='reset_password.php?email=$email'>Reset Password</a>";
            }
        } else {
            echo "Invalid OTP or OTP expired!";
        }
    } else {
        echo "No such email registered!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
</head>
<body>
    <h2>Enter OTP to Verify</h2>
    <form action="verify_otp.php?action=<?php echo $action; ?>&email=<?php echo $email; ?>" method="POST">
        <input type="text" name="otp" placeholder="Enter OTP" required><br><br>
        <button type="submit" name="submit">Verify OTP</button>
    </form>
</body>
</html>
