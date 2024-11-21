<?php    
   
    session_start();
    
    function openCon() {
        $conn = new mysqli("localhost", "root", "", "dct-ccs-finals");
    
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    }
    
    function closeCon($conn) {
        $conn->close();
    }
    
    function debugLog($message) {
        error_log("[DEBUG] " . $message);
    }
    
    function loginUser($username, $password) {
        $conn = openCon();
    
        // Query to fetch user data by email
        $sql = "SELECT * FROM users WHERE email = ?"; 
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
    
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
    
            // Verify password using password_verify() with the hash stored in the database
            if (password_verify($password, $user['password'])) {
                // Set session variables for logged-in user
                $_SESSION['email'] = $user['email'];
                $_SESSION['user_id'] = $user['id'];
    
                $stmt->close();
                closeCon($conn);
                return true;
            }
        }
    
        $stmt->close();
        closeCon($conn);
        return false;
    }
    
    
    
    function isLoggedIn() {
        return isset($_SESSION['email']);
    }
    
    
    function addUser() {
        $conn = openCon();
    
        if ($conn) {
            // User input (usually from a form)
            $email = 'user2@gmail.com'; // Example email
            $password = 'password'; // Example password
            $name = 'user2'; // Example name
            
            // Hash the password before storing it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Use password_hash for encryption
    
            // Use prepared statements to prevent SQL injection
            $sql = "INSERT INTO users (email, password, name) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $email, $hashedPassword, $name); // Bind parameters
    
            if ($stmt->execute()) {
                echo "New record created successfully";
            } else {
                echo "Error: " . $stmt->error;
            }
    
            $stmt->close();
            closeCon($conn);
        } else {
            echo "Failed to connect to the database.";
        }
    }
    
?>