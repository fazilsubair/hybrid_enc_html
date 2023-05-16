<?php
// RSA Encryption Function
function rsaEncrypt($message, $publicKey)
{
    openssl_public_encrypt($message, $encrypted, $publicKey);
    return base64_encode($encrypted);
}

// AES Encryption Function
function aesEncrypt($data, $encryptionKey)
{
    $iv = openssl_random_pseudo_bytes(16);
    $encryptedData = openssl_encrypt($data, 'AES-256-CBC', $encryptionKey, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encryptedData);
}

// Generate RSA Key Pair
$config = array(
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
);
$res = openssl_pkey_new($config);
openssl_pkey_export($res, $privateKey);
openssl_pkey_export($res, $privateKey);

// Get the public key from the private key
$publicKey = openssl_pkey_get_details($res)['key'];

// Check if the form is submitted and the file is uploaded successfully
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK) {
    // Get the uploaded image file
    $imageFile = $_FILES["image"]["tmp_name"];

    // Read the image file contents
    $imageData = file_get_contents($imageFile);

    // Generate a random encryption key
    $encryptionKey = openssl_random_pseudo_bytes(32);

    // Encrypt the image data using AES-256-CBC encryption
    $encryptedData = aesEncrypt($imageData, $encryptionKey);

    // Encrypt the encryption key using RSA encryption
    $encryptedKey = rsaEncrypt($encryptionKey, $publicKey);

    // Generate a unique filename for the encrypted image
    $encryptedFilename = uniqid() . '.enc';

    // Save the encrypted image file
    file_put_contents($encryptedFilename, $encryptedData);

    // // Return the encrypted image filename and encrypted encryption key to the user
    echo "Encrypted Image: <a href='$encryptedFilename'>$encryptedFilename</a><br>";
    // echo "Encrypted Key: " . $encryptedKey;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>AES + RSA Encryption Example</title>
</head>
<body>
    <h1>AES + RSA Encryption Example</h1>
    <h2>Encrypted Image:</h2>
    <pre><?php echo "<a href='$encryptedFilename'>$encryptedFilename</a><br>"; ?></pre>
    <br>
    <h2>Encrypted Key:</h2>
    <pre><?php echo  $encryptedKey; ?></pre>
    <br>
    <br>
    <h2>Public Key:</h2>
    <pre><?php echo htmlspecialchars($publicKey); ?></pre>
    <br>
    <h2>Private Key:</h2>
    <pre><?php echo htmlspecialchars($privateKey); ?></pre>
</body>
</html>
