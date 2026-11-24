<?php
include 'includes/session.php';

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $position = mysqli_real_escape_string($conn, $_POST['position']);
    $platform = mysqli_real_escape_string($conn, $_POST['platform']);

    $sql = "UPDATE candidates SET firstname = '$firstname', lastname = '$lastname', position_id = '$position', platform = '$platform' WHERE id = '$id'";

    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Candidate updated successfully';
    } else {
        $_SESSION['error'] = 'Error: ' . $conn->error;
    }
} else {
    $_SESSION['error'] = 'Fill up edit form first';
}

header('location: candidates.php');
?>
