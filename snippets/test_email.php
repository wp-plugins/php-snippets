<?php
/*
Description: Send a test email to the address indicated. Use this to test your server's email settings.
Shortcode: [test_email to="your@email.com" subject="This is a test" message="Only a test..."]
*/
if (!isset($subject) || empty($subject)) {
    $subject = 'Test email';
}
if (!isset($message) || empty($message)) {
    $message = 'Test message...';
}

if (!isset($to) || empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
    print 'The $to parameter is required and must be a valid email address.';
}

if(wp_mail($to,$subject,$message)) {
    print "An email was sent to $to, and it appears to have been successful.";
}
else {
    print "There seems to have been a problem sending an email to $to";
}