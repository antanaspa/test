<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WorkDeviceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $deviceId, $address;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($deviceId, $address)
    {
        $this->deviceId = $deviceId;
        $this->address = $address;
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Device notification';
        $from = 'noreply@gpswox.dev';
        return $this->view('device.workNotification')->subject($subject)->from($from)->with(
            [
                'deviceId' => $this->deviceId,
                'address'  => $this->address
            ]
        );
    }
}
