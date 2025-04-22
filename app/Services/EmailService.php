<?php

namespace App\Services;

use App\Mail\NotificationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Log;


class EmailService
{
    /**
     * Send notification email.
     * [SPECGT3] - sending notification email
     * @param array $config - ['to', 'template', 'subject']
     * @param array $worksite - ['title', 'idaff', 'isFinal', 'invoiceNumber']
     */
    public function sendEmailNotification(array $config, array $worksite): void
    {
        $dataToSend = collect($worksite)->toArray();
        $to = $this->handleTo($config);

        if (empty($to)) {
            Log::error('EmailService: No valid recipient email address found.');
            return;
        }

        $subject = $config['subject'];

        try {
            $email = Mail::to($to);
            $email->send(new NotificationEmail($dataToSend, $subject, $config['template']));
        } catch (\Exception $e) {
            Log::error('Error sending email: ' . $e->getMessage());
        }
    }

    /**
     * Handle recipient email address based on config.
     * @param array $config - ['to']
     * @return array<string>
     */
    private function handleTo(array $config): ?array
    {
        $recipients = [];

        $types = is_array($config['to']) ? $config['to'] : [$config['to']];

        foreach ($types as $type) {
            $template = EmailTemplate::where('type', $type)->first();
            if ($template && $template->email) {
                $recipients[] = $template->email;
            }
        }

        Log::info('EmailService: Recipients', ['recipients' => $recipients]);

        return count($recipients) ? $recipients : [];
    }
}
