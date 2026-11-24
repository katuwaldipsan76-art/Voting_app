<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$email = $_POST['email'];  // Capture email
		$national_id = $_POST['national_id'];  // Capture national ID
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$filename = $_FILES['photo']['name'];

		// Check if photo exists and move it
		if(!empty($filename)){
			move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
		}

		// Insert voter details into the database using national_id as voters_id
		$sql = "INSERT INTO voters (voters_id, password, firstname, lastname, email, national_id, photo) 
				VALUES ('$national_id', '$password', '$firstname', '$lastname', '$email', '$national_id', '$filename')";

		// Execute the query and check if it was successful
		if($conn->query($sql)){
			$_SESSION['success'] = 'Voter added successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}

	} else {
		$_SESSION['error'] = 'Fill up add form first';
	}

	// Redirect back to voters page
	header('location: voters.php');
?>
