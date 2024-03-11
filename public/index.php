<?php

/**
 * Send an email with sendgrid
 */
require '../vendor/autoload.php';
use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load('../.env');

$allowedDomains = ['localhost', 'example.com'];
$currentDomain = $_SERVER['HTTP_HOST'];
$referrer = $_SERVER['REFERER'] ?? "http://{$currentDomain}/contact";
$sendgridApiKey = $_ENV['SENDGRID_API_KEY'] ?? '';


if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && in_array($currentDomain, $allowedDomains)
) {
    $recipientEmail = 'recipient@example.com';
    $senderEmail = 'sender@example.com';
    $subject = 'Contact form';
    $json = file_get_contents('php://input');

    $sendgridApiUrl = 'https://api.sendgrid.com/v3/mail/send';
    if (!empty($json)) {
        $data = json_decode($json, true);
        $message = $data['message'] ?? '';
        $email = $data['email'] ?? '';
        $name = $data['name'] ?? '';
        $content = "Email: {$email} \nName: {$name} \nMessage: {$message}";
    }

    if (!empty($message) && !empty($email) && !empty($name)) {
        $data = [
            'personalizations' => [
                [
                    'to' => [
                        ['email' => $recipientEmail]
                    ]
                ]
            ],
            'from' => [
                'email' => $senderEmail
            ],
            'subject' => $subject,
            'content' => [
                [
                    'type' => 'text/plain',
                    'value' => $content
                ]
            ]
        ];

        $dataString = json_encode($data);

        $ch = curl_init($sendgridApiUrl);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
            'Authorization: Bearer ' . $sendgridApiKey,
            'Content-Type: application/json',
        ]
        );

        $response = curl_exec($ch);
        curl_close($ch);
        
    }
} else {
    echo 'Not allowed';
}

header("Location: {$referrer}");
exit();
