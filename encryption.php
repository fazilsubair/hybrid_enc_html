<?php

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['image'];
    $fileType = $file['type'];

    // Function to encrypt the file using AES-CBC
    function encryptFile($sourceFile, $destinationFile, $file) {
        $keySize = 16; // 128-bit key
        $blockSize = 16; // 128-bit block size
        $key = openssl_random_pseudo_bytes($keySize);
        $iv = openssl_random_pseudo_bytes($blockSize);

        $inputContents = file_get_contents($sourceFile);

        // Set up encryption
        $cipher = "aes-128-cbc";
        $encryptedData = openssl_encrypt($inputContents, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        // Create the destination directory if it doesn't exist
        $destinationDir = dirname($destinationFile);
        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        // Write encrypted data to the destination file
        file_put_contents($destinationFile, $encryptedData);

        if ($encryptedData !== false) {
            echo "File uploaded and encrypted successfully!\n";
            echo "Encrypted file name: " . $file['name'] . "\n";
            echo "Encryption key: " . bin2hex($key) . "\n";

            // Generate a unique download link
            $downloadLink = 'download.php?file=' . urlencode($destinationFile);
            echo "Download link: <a href='$downloadLink'>Download Encrypted File</a>\n";
        } else {
            echo "Encryption failed.\n";
        }
    }

    // Specify the directory path to save the encrypted file
    $destinationPath = 'aesenc/';

    // Encrypt the uploaded file
    encryptFile($file['tmp_name'], $destinationPath . $file['name'], $file);
} else {
    echo 'No file submitted or an error occurred during upload.';
}

?>
