<?php
include 'db_connection.php';

// Define images for both adminuser and events
$clubs = [
    1 => "img/marketingclub.png",   // Example for adminuser
    2 => "img/ClubLogo.png",     // Example for adminuser
    3 => "img/diu.png" ,   // Example for events
    4 => "img/ftcclub.png",   // Example for adminuser
    5 => "img/aiclub.png",     // Example for adminuser
    6 => "img/buisnessclub.png" ,   // Example for events
    7 => "img/ptc.png",     // Example for adminuser
    8 => "img/cyberclub.png"    // Example for events
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

        // Update the image filename for events table
       // $sqlEvent = "UPDATE events SET image = '$uniqueFileName' WHERE eventID = $clubID";

        // Execute SQL for updating the adminuser table
        if ($conn->query($sqlAdmin) === TRUE) {
            echo "Image stored successfully for adminuser clubID: $clubID as $uniqueFileName<br>";
        } else {
            echo "Failed to update the image filename for adminuser clubID: $clubID. Error: " . $conn->error . "<br>";
        }

        // Execute SQL for updating the events table
       // if ($conn->query($sqlEvent) === TRUE) {
         //   echo "Image stored successfully for event clubID: $clubID as $uniqueFileName<br>";
        //} else {
          //  echo "Failed to update the image filename for event clubID: $clubID. Error: " . $conn->error . "<br>";
        //}
    
   } 
    else {
        echo "Failed to copy the image for clubID: $clubID<br>";
    }
}

// Close the database connection
$conn->close();
?>
     