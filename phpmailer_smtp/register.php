<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../admin/includes/conn.php');  // Corrected relative path to the database connection file
include('smtp/PHPMailerAutoload.php');  // Include PHPMailer for sending OTP

if (isset($_POST['submit'])) {
    // Retrieve form data
    $voters_id = $_POST['voters_id'];  // Capture voter ID from the form
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password
    $national_id = $_POST['national_id'];
    $otp = rand(100000, 999999); // Generate OTP
    $otp_expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));  // OTP expiry time

    // Check if the email, national ID, or Voter ID already exists
    $email_check_sql = "SELECT * FROM voters WHERE email = '$email'";
    $national_id_check_sql = "SELECT * FROM voters WHERE national_id = '$national_id'";
    $voters_id_check_sql = "SELECT * FROM voters WHERE voters_id = '$voters_id'"; // Check if Voter ID exists

    $email_check_result = mysqli_query($conn, $email_check_sql);
    $national_id_check_result = mysqli_query($conn, $national_id_check_sql);
    $voters_id_check_result = mysqli_query($conn, $voters_id_check_sql);  // Execute Voter ID check query

    if (mysqli_num_rows($email_check_result) > 0) {
        echo "This email is already in use. Please use a different email.";
    } elseif (mysqli_num_rows($national_id_check_result) > 0) {
        echo "This National ID is already in use. You cannot use the same National ID.";
    } elseif (mysqli_num_rows($voters_id_check_result) > 0) {
        echo "This Voter ID is already in use. Please choose a different Voter ID.";
    } else {
        // Handle the file upload (photo)
        $photo = $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];
        $photo_path = 'images/' . $photo;  // Save photo in 'images/' folder
        move_uploaded_file($photo_tmp, '/opt/lampp/htdocs/votesystem/' . $photo_path);  // Move file to correct directory

        // Insert voter data into the database (using 'voters' table)
        $sql = "INSERT INTO voters (voters_id, firstname, lastname, email, password, national_id, photo, otp, otp_expiry, status, created_at, updated_at)
                VALUES ('$voters_id', '$firstname', '$lastname', '$email', '$password', '$national_id', '$photo', '$otp', '$otp_expiry', 0, NOW(), NOW())";

        if (mysqli_query($conn, $sql)) {
            // Send OTP via email
            if (smtp_mailer($email, 'Your OTP Code', 'Your OTP is: ' . $otp)) {
                echo "Registration successful! OTP sent to your email.";
                // After registration, redirect to OTP verification page
                echo "<br><a href='verify_otp.php'>Verify OTP</a>";
            } else {
                echo "Error sending OTP email.";
            }
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// Function to send OTP email using PHPMailer
function smtp_mailer($to, $subject, $msg) {
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
    $mail->Host = "smtp.gmail.com";
    $mail->Port = 587;
    $mail->IsHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Username = "your mail";  // Your Gmail address
    $mail->Password = "your app password";  // Your Gmail password (use App password if 2-step verification is enabled)
    $mail->SetFrom("your mail");
    $mail->Subject = $subject;
    $mail->Body = $msg;
    $mail->AddAddress($to);
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => false
        )
    );
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
    <title>Voting System Registration</title>
</head>
<body>
    <h2>Voting System Registration</h2>
    <form action="register.php" method="post" enctype="multipart/form-data">
        <label for="voters_id">Voter ID:</label>
        <input type="text" name="voters_id" required><br><br>

        <label for="firstname">First Name:</label>
        <input type="text" name="firstname" required><br><br>

        <label for="lastname">Last Name:</label>
        <input type="text" name="lastname" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <label for="national_id">National ID:</label>
        <input type="text" name="national_id" required><br><br>

        <label for="photo">Profile Photo:</label>
        <input type="file" name="photo" accept="image/*" required><br><br>

        <input type="submit" name="submit" value="Register">
    </form>
</body>
</html>
