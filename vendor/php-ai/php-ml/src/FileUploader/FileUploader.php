<?php

namespace Phpml\FileUploader;

class FileUploader {
    private $uploadDir;
    
    public function __construct($uploadDir) {
        $this->uploadDir = $uploadDir;
    }
    
    public function uploadFile($file) {
        $targetDir = $this->uploadDir;
        $targetFile = $targetDir . basename($file['name']);
        $uploadOk = true;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
        // Check if file already exists
        if (file_exists($targetFile)) {
            echo "Sorry, file already exists.";
            $uploadOk = false;
        }
        
        // Allow only txt files
        if ($fileType !== 'csv') {
            echo "Sorry, only CSV files are allowed.";
            $uploadOk = false;
        }
        
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk === false) {
            echo "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                echo "The file " . basename($file['name']) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    public function deleteFile($file){
        if (file_exists($file)) {
            if (unlink($file)) {
                echo basename($file) . ' has been deleted';
            } else {
                echo 'Unable to delete the file ' . basename($file);
            }
        } else {
            echo 'File ' . basename($file) . ' does not exist.';
        }
    }
}

?>
