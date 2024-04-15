<?php

namespace Tests\Feature;

use App\Events\ProductReviewed;
use App\Notifications\ProductReviewed as ProductReviewedNotification;
use App\Product;
use App\ProductReview;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;


class ProductReviewNotificationsTest extends TestCase
{
    /**
     * @var ProductReview
     */
    private $review;
    /**
     * @var User
     */
    private $admin;
    /**
     * @var User
     */
    private $regular;

    protected function setUp(): void
    {
        parent::setUp();

        $author = factory(User::class)->create();
        $product = factory(Product::class)->create();
        $this->review = factory(ProductReview::class)->make();
        $this->review->user()->associate($author);
        $this->review->product()->associate($product);
        $this->review->save();

        $this->admin = factory(User::class)->state('admin')->create();
        $this->regular = factory(User::class)->create();
    }

    public function testSendsSmsNotification()
    {
        Notification::fake();

        $review = $this->review;
        $admin = $this->admin;

        event(new ProductReviewed($review));

        Notification::assertSentTo(
            $this->admin,
            ProductReviewedNotification::class,
            function (ProductReviewedNotification $notification, $channels) use ($review) {
                return $notification->review->id === $review->id;
            }
        );

        Notification::assertSentTo(
            $this->admin,
            ProductReviewedNotification::class,
            function (ProductReviewedNotification $notification, $channels) {
                return in_array('nexmo', $channels);
            }
        );

        Notification::assertSentTo(
            $this->admin,
            ProductReviewedNotification::class,
            function (ProductReviewedNotification $notification, $channels, $notifiable) use ($review) {
                return $notification->toNexmo($notifiable)->content === 'New review of product #' . $review->product->id;
            }
        );

        Notification::assertSentTo(
            $this->admin,
            ProductReviewedNotification::class,
            function (ProductReviewedNotification $notification, $channels, $notifiable) use ($admin) {
                return $notifiable->routeNotificationFor('nexmo') === $admin->phone_number;
            }
        );
    }

    public function testStoresDbNotifications()
    {
        Notification::fake();

        $review = $this->review;
        $admin = $this->admin;

        event(new ProductReviewed($review));

        Notification::assertSentTo(
            $admin,
            ProductReviewedNotification::class,
            function (ProductReviewedNotification $notification) use ($review) {
                return $notification->review->id === $review->id;
            }
        );

        Notification::assertSentTo(
            $admin,
            ProductReviewedNotification::class,
            function (ProductReviewedNotification $notification, $channels) use ($review) {
                return in_array('database', $channels);
            }
        );

        Notification::assertSentTo(
            $admin,
            ProductReviewedNotification::class,
            function (ProductReviewedNotification $notification, $channels, $notifiable) use ($review) {
                return $notification->toDatabase($notifiable)['product_id'] === $review->product->id;
            }
        );
    }

    public function testNotifiesOnlyAdminUsers()
    {
        Notification::fake();

        $review = $this->review;
        $admin = $this->admin;
        $regular = $this->regular;

        event(new ProductReviewed($review));

        Notification::assertSentTo($admin, ProductReviewedNotification::class);
        Notification::assertNotSentTo($regular, ProductReviewedNotification::class);
    }
}
