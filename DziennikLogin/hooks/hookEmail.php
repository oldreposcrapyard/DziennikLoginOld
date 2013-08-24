
<?php

// This file will contain code responsible for sending files over email.
require '../classes/SwiftMail/swift_required.php';
require '../config.local.php';

class emailSendHook {

    public $emailToArray;
    public $emailBody;
    private $mailer;
    private $transport;

    public function setData($emailToArray, $emailBody) {
        $this->emailToArray = $emailToArray;
        $this->emailBody = $emailBody;
    }

    public function __construct($smtpUsername, $smtpPassword) {
        try {
            $this->transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', '465', 'ssl')
                    ->setUsername($smtpUsername)
                    ->setPassword($smtpPassword)
            ;
        } catch (Swift_TransportException $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function createMailer() {
        try {
            $this->mailer = Swift_Mailer::newInstance($this->transport);
        } catch (Swift_SwiftException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function sendEmail() {
        $this->createMailer();
        
        try {
            $message = Swift_Message::newInstance()

                    // Give the message a subject
                    ->setSubject('Oceny z DziennikLogin - ' . date('Y - m - d H:i:s'))

                    // Set the From address with an associative array
                    ->setFrom(array('dzienniklogin@gmail.com' => 'DziennikLogin'))

                    // Set the To addresses with an associative array
                    ->setTo($this->emailToArray)

                    // Give it a body
                    ->setBody($this->emailBody);
            // if ($withAttachment) {
            // Optionally add any attachments
            //     $message->attach(Swift_Attachment::fromPath($attachmentPath));
            //  }
            $result = $this->mailer->send($message);
        } catch (Swift_SwiftException $e) {
            throw new Exception($e->getMessage());
        }
    }

}

?>