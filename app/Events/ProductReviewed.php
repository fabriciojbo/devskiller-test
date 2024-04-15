<?php

namespace App\Events;

use App\ProductReview;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductReviewed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var ProductReview
     */
    public $review;

    public function __construct(ProductReview $review)
    {
        $this->review = $review;
    }
}
