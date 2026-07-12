<?php

namespace App\Notifications;

use App\Models\Charge;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PendingChargeNotification extends Notification
{
    use Queueable;

    protected $charge;

    /**
     * Create a new notification instance.
     */
    public function __construct(Charge $charge)
    {
        $this->charge = $charge;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $res = $this->charge->resolucions->first();
        $label = $res ? "Resolución {$res->rd}" : "Cargo N° {$this->charge->n_charge}";

        return [
            'charge_id' => $this->charge->id,
            'n_charge' => $this->charge->n_charge,
            'asunto' => $this->charge->asunto,
            'label' => $label,
        ];
    }
}
