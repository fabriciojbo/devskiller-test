<?php

namespace App\Notifications;

use App\ProductReview;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;

class ProductReviewed extends Notification
{
    use Queueable;

    /**
     * @var ProductReview
     */
    public $review;

    public function __construct(ProductReview $review)
    {
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'nexmo'];
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toNexmo($notifiable)
    {
        return 'New review for product #' . $this->product->id;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'product_id' => $this->product->id,
        ];
    }
}
