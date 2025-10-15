<?php
require_once __DIR__ . '/yubico/Yubico.php';

// Replace with your actual Client ID and Secret Key
$yubi = new Auth_Yubico('100643', 'BxZwcQCr8ixST1nD9HazHhqW1Xo=');

// Example: Predefined users and hashed passwords
$passwords = [
    'User' => password_hash('UserPass', PASSWORD_BCRYPT), // Example hashed password
    'Admin' => password_hash('AdminPass123', PASSWORD_BCRYPT),
	"4210" => '$2y$10$yC2k2KrOJwbHrvuLqaKW7Oe71kDZEOtjsm7X/VuCALxyVRphjXQwK',
	"5226" => '$2y$10$udKEkieIJ/k/2RPFsTJd3u55E2xsOjCzRuBHy9891Q3h1.KkUT6m2',
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $credential = filter_input(INPUT_POST, 'credential', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

    // Validate input
    if (empty($username) || empty($credential) || empty($type)) {
        echo "Invalid input: All fields are required.";
        exit;
    }

    // Authenticate based on type
    if ($type === 'password') {
        // Password-based authentication
        if (array_key_exists($username, $passwords) && password_verify($credential, $passwords[$username])) {
            echo "success";
        } else {
            echo "Invalid username or password.";
        }
    } elseif ($type === 'yubikey') {
        // YubiKey OTP validation
        $auth = $yubi->verify($credential);
        if (PEAR::isError($auth)) {
            echo "Authentication failed: " . $auth->getMessage();
        } else {
            echo "success";
        }
    } elseif ($type === 'proton') {
        // Example validation for Proton Drive login
        // Replace with actual credentials or API call
        if ($username === '4210' && $credential === '4210') {
            echo "success";
        } else {
            echo "Invalid Proton credentials.";
        }
    } else {
        echo "Invalid login type.";
    }
}
?>
