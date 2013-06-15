
<?php
// This file will contain code responsible for sending files over email.
require '../lib/SwiftMail/swift_required.php';
require '../config.local.php';

function sendEmailWithGrades($emailAdressesTo,$emailBody,$withAttachment = FALSE,$attachmentPath){

    $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', '465','ssl')
            ->setUsername($CONF['smtpUsername'])
            ->setPassword($CONF['smtpPassword'])
    ;

    $mailer = Swift_Mailer::newInstance($transport);


// Create the message
    $message = Swift_Message::newInstance()

            // Give the message a subject
            ->setSubject('Oceny z DziennikLogin - ' . date('Y - m - d'))

            // Set the From address with an associative array
            ->setFrom(array('dzienniklogin@gmail.com' => 'DziennikLogin'))

            // Set the To addresses with an associative array
            ->setTo($emailAdressesTo)

            // Give it a body
            ->setBody($emailBody);
    if ($withAttachment) {
        // Optionally add any attachments
        $message->attach(Swift_Attachment::fromPath($attachmentPath));
    }
    $result = $mailer->send($message);
    return $result;
}
?>