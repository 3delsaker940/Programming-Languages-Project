<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;

class NewReservationNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $reservationId;
    protected $renterName;
    public function __construct($reservationId, $renterName)
    {
        $this->reservationId = $reservationId;
        $this->renterName = $renterName;
    }
    public function via($notifiable)
    {
        return ['database'];
    }
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New booking',
            'body' => 'You have a new booking' . $this->renterName,
            'reservation_id' => $this->reservationId,
        ];
    }
    public function sendFCM($notifiable)
    {
        if (!$notifiable->fcm_token) {
            return;
        }
        Http::withHeaders([
            'Authorization' => 'key=' . env('FCM_SERVER_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $notifiable->fcm_token,
            'notification' => [
                'title' => 'New booking',
                'body' => 'You have a new booking' . $this->renterName,
                'sound' => 'default',
            ],
            'data' => [
                'reservation_id' => $this->reservationId,
                'type' => 'new_reservation'
            ]
        ]);
    }
}
