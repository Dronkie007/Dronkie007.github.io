<?php
// Replace 'YourPlainTextPassword' with the password you want to hash
$plainPassword = 'UserPass';

// Hash the password using BCRYPT
$hashedPassword = password_hash($plainPassword, PASSWORD_BCRYPT);

// Display the hashed password
echo "Plain Password: $plainPassword\n";
echo "Hashed Password: $hashedPassword\n";
?>

// how to use password Hash
//  php hash_password.php  (in command prompt //  where hash-passwords.php file is saved)
