<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\Campaign;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SendCampaignJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Campaign $campaign
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get the campaign with its template
        $campaign = $this->campaign->load('template', 'user');

        // Get all subscribers for this user who haven't unsubscribed
        $subscribers = $campaign->user->subscribers()
            ->whereNull('unsubscribed_at')
            ->whereNull('deleted_at')
            ->get();

        // Replace {{ content }} in template with campaign content
        $emailContent = str_replace(
            '{{ content }}',
            $campaign->content,
            $campaign->template->content
        );

        $totalSent = 0;
        $invalidEmails = [];

        foreach ($subscribers as $subscriber) {
            $validator = Validator::make(
                ['email' => $subscriber->email],
                ['email' => 'required|email:rfc,dns']
            );

            if ($validator->fails()) {
                $invalidEmails[] = $subscriber->email;
                Log::warning("Invalid email address skipped for campaign {$campaign->id}: {$subscriber->email}");

                continue;
            }

            try {
                Mail::to($subscriber->email)->send(
                    new CampaignMail(
                        campaignSubject: $campaign->subject,
                        htmlContent: $emailContent
                    )
                );
                
                $totalSent++;
            } catch (\Exception $e) {
                Log::error("Failed to send email to {$subscriber->email} for campaign {$campaign->id}: {$e->getMessage()}");
            }
        }

        // Update campaign status
        $campaign->update(['status' => 'sent']);

        // Log summary
        Log::info("Campaign {$campaign->id} completed. Total emails sent: {$totalSent}, Invalid emails: " . count($invalidEmails));

        if (!empty($invalidEmails)) {
            Log::info("Invalid email addresses for campaign {$campaign->id}: " . implode(', ', $invalidEmails));
        }
    }
}
