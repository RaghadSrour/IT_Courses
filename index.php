
<!DOCTYPE html>
<html>
<head>
    <title>Sign Up Form</title>
    
</head>
<body>
    <h1>Sign Up Form</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <label for="gender">Gender:</label>
        <select name="gender" id="gender" required>
            <option value="male">Male</option>
            <option value="female">Female</option>
            
        </select>

        <label for="remember_me">Remember Me:</label>
        <input type="checkbox" name="remember_me" id="remember_me">

        <label for="image">Image (max size 1M):</label>
        <input type="file" name="image" id="image" accept="image/*" required>

        <input type="submit" name="submit" value="Sign Up">
    </form>

    <?php

$host = "localhost";
$username = "root";
$password = "";
$dbname = "Web2";


$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

// Form submission
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password
    $gender = $_POST['gender'];
    $rememberMe = isset($_POST['rememberMe']) ? 1 : 0;


	
    // Image upload
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $maxFileSize = 1 * 1024 * 1024; // 1M in bytes

    // Validate image file size
    if ($_FILES["image"]["size"] > $maxFileSize) {
        $error = "Error: Image file size exceeds the maximum limit of 1MB.";
    }

    // Validate image file type
    $allowedExtensions = array("jpg", "jpeg", "png", "gif");
    if (!in_array($imageFileType, $allowedExtensions)) {
        $error = "Error: Only JPG, JPEG, PNG, and GIF images are allowed.";
    }




	 // If no errors, proceed with database insertion
	 if (!isset($error)) {
        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, gender, remember_me, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssis", $name, $email, $password, $gender, $rememberMe, $targetFile);

        // Upload image file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            if ($stmt->execute()) {
                $success = "Success: User registration completed.";
            } else {
                $error = "Error: Failed to insert data into database.";
            }
        } else {
            $error = "Error: Failed to upload image.";
        }

        // Close prepared statement
        $stmt->close();
    }
}

$conn->close();

    ?>
</body>
</html>