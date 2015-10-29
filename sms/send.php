<?php
require 'class-Clockwork.php';

// lagre alle SMS sendinger og :)
// fins delivery response også...
$API_KEY = "HENT FRA LOCAL CONFIG!!!!"

try
{
    // Create a Clockwork object using your API key
    $clockwork = new Clockwork( $API_KEY );

    // Setup and send a message
    // Valider at nummer er korrekt (8 siffer), hvis ikke logg feil og ikke send
    $message = array( 'to' => '+4740550840', 'message' => 'This is a test!' );
    $result = $clockwork->send( $message );

    // Check if the send was successful
    if($result['success']) {
        echo 'Message sent - ID: ' . $result['id'];
    } else {
        echo 'Message failed - Error: ' . $result['error_message'];
    }
}
catch (ClockworkException $e)
{
    echo 'Exception sending SMS: ' . $e->getMessage();
}
?>