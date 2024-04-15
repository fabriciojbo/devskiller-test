<?php

namespace App\Listeners;

use App\Events\ProductReviewed;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ProductReviewed as ProductReviewedNotification;

class NotifyAboutProductReview
{
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    public function __construct(Dispatcher $channelManager)
    {
        $this->dispatcher = $channelManager;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ProductReviewed $event)
    {
        // Check if the user is an admin
        if ($event->user->is_admin) {
            // Send notification to the admin user
            $event->user->notify(new ProductReviewedNotification($event->product));
        }
    }
}
