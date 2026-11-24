<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $platform = mysqli_real_escape_string($conn, $_POST['platform']);

    // Validate required fields
    if(empty($firstname) || empty($lastname) || empty($position) || empty($platform)){
        $_SESSION['error'] = 'All fields are required';
        header('location: candidates.php');
        exit();
    }

    // Handle file upload
    $filename = '';
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] === 0){
        $target_dir = "../images/";
        $filename = time() . '_' . basename($_FILES['photo']['name']);
        $target_file = $target_dir . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file is an image
        $check = getimagesize($_FILES['photo']['tmp_name']);
        if($check !== false) {
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                // File uploaded successfully
            } else {
                $_SESSION['error'] = 'Error uploading file.';
                header('location: candidates.php');
                exit();
            }
        } else {
            $_SESSION['error'] = 'File is not a valid image.';
            header('location: candidates.php');
            exit();
        }
    }

    // Insert candidate into the database
    $sql = "INSERT INTO candidates (position_id, firstname, lastname, photo, platform) 
            VALUES ('$position', '$firstname', '$lastname', '$filename', '$platform')";

    if($conn->query($sql)){
        $_SESSION['success'] = 'Candidate added successfully';
    } else {
        $_SESSION['error'] = 'Database Error: ' . $conn->error;
    }
} else {
    $_SESSION['error'] = 'Fill up add form first';
}

header('location: candidates.php');
?>
