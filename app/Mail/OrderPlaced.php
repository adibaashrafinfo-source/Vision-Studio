<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('items', 'customer');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order #'.$this->order->order_no.' confirmed — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.order-placed');
    }
}
