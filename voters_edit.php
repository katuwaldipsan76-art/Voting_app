<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];  // Capture email from the form
		$national_id = $_POST['national_id'];  // Capture national ID
		$voters_id = $_POST['voters_id'];  // Capture voter ID from the form
		$password = $_POST['password'];

		// Fetch current voter data
		$sql = "SELECT * FROM voters WHERE id = $id";
		$query = $conn->query($sql);
		$row = $query->fetch_assoc();

		// Check if password is changed, if not retain the old password
		if($password == $row['password']){
			$password = $row['password'];
		}
		else{
			$password = password_hash($password, PASSWORD_DEFAULT);
		}

		// Update the voter information (including email, national_id, and voter_id)
		$sql = "UPDATE voters SET voters_id = '$voters_id', firstname = '$firstname', lastname = '$lastname', email = '$email', national_id = '$national_id', password = '$password' WHERE id = '$id'";

		// Execute the update query
		if($conn->query($sql)){
			$_SESSION['success'] = 'Voter updated successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Fill up edit form first';
	}

	// Redirect back to voters page
	header('location: voters.php');
?>
