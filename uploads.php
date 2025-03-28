<?php
include 'db_connection.php';

$clubs = [
    1 => "img/marketingclub.png",   
    2 => "img/ClubLogo.png",    
    3 => "img/diu.png" ,  
    4 => "img/ftcclub.png",  
    5 => "img/aiclub.png",   
    6 => "img/buisnessclub.png" , 
    7 => "img/ptc.png", 
    8 => "img/cyberclub.png"
];

$events = [
    1 => "img/data-jam.png",   
    2 => "img/data-jam.png",    
    3 => "img/kanaba.png" ,  
    4 => "img/EidLogo.png",  
    5 => "img/ftc_talks.png"
];

// Define the upload directory
$targetDirectory = "uploads/"; 

// Process each club and store their images
foreach ($clubs as $clubID => $originalImagePath) {
    // Generate a unique filename
    $uniqueFileName = uniqid("club_Logo_") . ".png";
    $targetPath = $targetDirectory . $uniqueFileName;

    // Copy the image to the target folder
    if (copy($originalImagePath, $targetPath)) {
        // Update the image filename for adminuser table
        $sqlAdmin = "UPDATE adminuser SET image = '$uniqueFileName' WHERE clubID = $clubID";

        // Execute SQL for updating the adminuser table
        if ($conn->query($sqlAdmin) === TRUE) {
            echo "Image stored successfully for adminuser clubID: $clubID as $uniqueFileName<br>";
        } else {
            echo "Failed to update the image filename for adminuser clubID: $clubID. Error: " . $conn->error . "<br>";
        }
    } else {
        echo "Failed to copy the image for clubID: $clubID<br>";
    }
}

// Process each event and store their images
foreach ($events as $eventID => $originalImagePath) {
    // Generate a unique filename
    $uniqueFileName = uniqid("event_Logo_") . ".png";
    $targetPath = $targetDirectory . $uniqueFileName;

    // Copy the image to the target folder
    if (copy($originalImagePath, $targetPath)) {
        // Update the image filename for events table
        $sqlEvent = "UPDATE event SET image = '$uniqueFileName' WHERE eventID = $eventID";

        // Execute SQL for updating the events table
        if ($conn->query($sqlEvent) === TRUE) {
            echo "Image stored successfully for eventID: $eventID as $uniqueFileName<br>";
        } else {
            echo "Failed to update the image filename for eventID: $eventID. Error: " . $conn->error . "<br>";
        }
    } else {
        echo "Failed to copy the image for eventID: $eventID<br>";
    }
}

// Close the database connection
$conn->close();
?>
