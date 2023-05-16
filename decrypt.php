<?php
// RSA Decryption Function
function rsaDecrypt($encryptedData, $privateKey)
{
    $encrypted = base64_decode($encryptedData);
    openssl_private_decrypt($encrypted, $decrypted, $privateKey);
    return $decrypted;
}

// AES Decryption Function
function aesDecrypt($encryptedData, $encryptionKey)
{
    $encryptedData = base64_decode($encryptedData);
    $iv = substr($encryptedData, 0, 16);
    $encryptedData = substr($encryptedData, 16);
    $decryptedData = openssl_decrypt($encryptedData, 'AES-256-CBC', $encryptionKey, OPENSSL_RAW_DATA, $iv);
    return $decryptedData;
}

// Check if the form is submitted and the file is uploaded successfully
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["encrypted_image"]) && $_FILES["encrypted_image"]["error"] == UPLOAD_ERR_OK) {
    // Get the uploaded encrypted image file
    $encryptedImageFile = $_FILES["encrypted_image"]["tmp_name"];

    // Read the encrypted image file contents
    $encryptedImageData = file_get_contents($encryptedImageFile);

    // Get the encryption key and private key from the form data
    $encryptionKey = $_POST["encryption_key"];
    $privateKey = $_POST["private_key"];

    // Decrypt the encryption key using RSA decryption
    $decryptedKey = rsaDecrypt($encryptionKey, $privateKey);

    // Decrypt the image data using AES decryption
    $decryptedImageData = aesDecrypt($encryptedImageData, $decryptedKey);

    // Generate a unique filename for the decrypted image
    $decryptedFilename = uniqid() . '.jpg';

    // Save the decrypted image file
    file_put_contents($decryptedFilename, $decryptedImageData);

    // Display the decrypted image to the user
    // echo "Decrypted Image: <br>";
    // echo "<img src='$decryptedFilename'>";
    // <a href='$decryptedFilename'>$decryptedFilename</a> 
    echo "Decrypted Image: <a href='$decryptedFilename'>$decryptedFilename</a><br>";
}
?>
