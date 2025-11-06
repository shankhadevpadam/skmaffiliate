<?php

use App\Jobs\SendCampaignJob;
use App\Mail\CampaignMail;
use App\Models\Campaign;
use App\Models\Subscriber;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('send campaign job dispatches and sends emails to all subscribers', function () {
    Mail::fake();

    // Create a user
    $user = User::factory()->create();

    // Create a template with {{ content }} placeholder
    $template = Template::factory()->create([
        'name' => 'Newsletter Template',
        'content' => '<html><body><h1>Newsletter</h1><div>{{ content }}</div></body></html>',
    ]);

    // Create a campaign
    $campaign = Campaign::factory()->create([
        'user_id' => $user->id,
        'template_id' => $template->id,
        'subject' => 'Test Campaign',
        'content' => '<p>This is the campaign content.</p>',
        'status' => 'draft',
    ]);

    // Create subscribers for the user
    $subscribers = Subscriber::factory()->count(3)->create([
        'user_id' => $user->id,
        'unsubscribed_at' => null,
    ]);

    // Create an unsubscribed subscriber (should not receive email)
    Subscriber::factory()->create([
        'user_id' => $user->id,
        'unsubscribed_at' => now(),
    ]);

    // Execute the job
    $job = new SendCampaignJob($campaign);
    $job->handle();

    // Assert emails were sent to all active subscribers
    Mail::assertSent(CampaignMail::class, 3);

    // Assert the campaign status was updated to sent
    expect($campaign->fresh()->status)->toBe('sent');
});

test('send campaign replaces template content placeholder', function () {
    Mail::fake();

    $user = User::factory()->create();

    $template = Template::factory()->create([
        'name' => 'Test Template',
        'content' => '<html><body>{{ content }}</body></html>',
    ]);

    $campaignContent = '<p>Custom campaign content</p>';
    $campaign = Campaign::factory()->create([
        'user_id' => $user->id,
        'template_id' => $template->id,
        'subject' => 'Test',
        'content' => $campaignContent,
        'status' => 'draft',
    ]);

    Subscriber::factory()->create([
        'user_id' => $user->id,
    ]);

    $job = new SendCampaignJob($campaign);
    $job->handle();

    Mail::assertSent(CampaignMail::class, function ($mail) use ($campaignContent) {
        return str_contains($mail->htmlContent, $campaignContent)
            && ! str_contains($mail->htmlContent, '{{ content }}');
    });
});

test('send campaign does not send to unsubscribed or deleted subscribers', function () {
    Mail::fake();

    $user = User::factory()->create();
    $template = Template::factory()->create();
    $campaign = Campaign::factory()->create([
        'user_id' => $user->id,
        'template_id' => $template->id,
    ]);

    // Active subscriber
    Subscriber::factory()->create(['user_id' => $user->id]);

    // Unsubscribed subscriber
    Subscriber::factory()->create([
        'user_id' => $user->id,
        'unsubscribed_at' => now(),
    ]);

    // Soft deleted subscriber
    Subscriber::factory()->create([
        'user_id' => $user->id,
        'deleted_at' => now(),
    ]);

    $job = new SendCampaignJob($campaign);
    $job->handle();

    // Only 1 email should be sent (to the active subscriber)
    Mail::assertSent(CampaignMail::class, 1);
});

test('send campaign only sends to subscribers of the campaign user', function () {
    Mail::fake();

    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $template = Template::factory()->create();
    $campaign = Campaign::factory()->create([
        'user_id' => $user1->id,
        'template_id' => $template->id,
    ]);

    // Create subscribers for both users
    Subscriber::factory()->create(['user_id' => $user1->id]);
    Subscriber::factory()->create(['user_id' => $user2->id]);

    $job = new SendCampaignJob($campaign);
    $job->handle();

    // Only 1 email should be sent (to user1's subscriber)
    Mail::assertSent(CampaignMail::class, 1);
});
