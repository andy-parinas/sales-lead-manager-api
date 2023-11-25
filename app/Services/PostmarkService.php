<?php


namespace App\Services;


use Postmark\Models\PostmarkAttachment;
use Postmark\PostmarkClient;

class PostmarkService implements Interfaces\EmailServiceInterface
{

    public function sendEmail($to, $from, $subject, $message, $attachmentFile = null, $filename = null)
    {
        $token = config('services.postmark.token');

        $attachment = null;

        if($attachmentFile){

            $attachment = [PostmarkAttachment::fromFile($attachmentFile,
                $filename,
                'application/pdf',
                'cid:',$filename )];

        }

        $client = new PostmarkClient($token);
        $fromEmail = $from;
        $toEmail = $to;
        $subject = $subject;
        $htmlBody = $message;
        $textBody = $message;
        $tag = env('APP_NAME') . "-message";
        $trackOpens = true;
        $trackLinks = "None";
        $messageStream = "outbound";

        // Send an email:
        $sendResult = $client->sendEmail(
            $fromEmail,
            $toEmail,
            $subject,
            $htmlBody,
            $textBody,
            $tag,
            $trackOpens,
            NULL, // Reply To
            NULL, // CC
            NULL, // BCC
            NULL, // Header array
            $attachment, // Attachment array
            $trackLinks,
            NULL, // Metadata array
            $messageStream
        );

        return $sendResult['errorcode'];
    }
}
