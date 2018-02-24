<?php

function jsonResponse($code, $message)
{
   http_response_code($code);
   header('Content-Type: application/json');
   return json_encode(['message' => $message]);
}

$sessionToken = $_SESSION['token'];
$formToken = filter_input(INPUT_POST, 'csrf_token');

if ($sessionToken !== $formToken) {
   return jsonResponse(500, 'Token mismatch.');
}

$args = [
   'name'      => FILTER_SANITIZE_STRING,
   'email'     => FILTER_VALIDATE_EMAIL,
   'phone'     => FILTER_SANITIZE_STRING,
   'message'   => FILTER_SANITIZE_STRING,
];

$inputs = filter_input_array(INPUT_POST, $args);

$missingKeys = array_filter($inputs, function($key) {
   return strlen($key) === 0;
});

if (!empty($missingKeys)) {
   jsonResponse(400, 'Fail to validate form data, please, check if everything is set (not empty) and valid.');
}

/**
 * Create the email and send the message
 * 
 * Add your email address inbetween the '' replacing yourname@yourdomain.com
 */
$to = 'yourname@yourdomain.com';

$email_subject = "Website Contact Form:  {$inputs['name']}";

$email_body = <<<EOT
<h1>You have received a new message from your website contact form.</h1>
<h2>Here are the details:</h2>
Name:    {$inputs['email']}
Phone:   {$inputs['phone']}
<strong>Message:</strong>
<blockquote>
   {$inputs['message']}
</blockquote>
EOT;

/**
 * This is the email address the generated message will be from. 
 * We recommend using something like noreply@yourdomain.com.
 */
$headers = "From: noreply@yourdomain.com\n";
$headers .= "Reply-To: {$inputs['email']}";

mail($to, $email_subject, $email_body, $headers);
return true;
