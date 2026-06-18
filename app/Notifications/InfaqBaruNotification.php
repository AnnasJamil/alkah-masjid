<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Infaq;

class InfaqBaruNotification extends Notification
{
    use Queueable;

    protected $infaq;

    public function __construct(Infaq $infaq)
    {
        $this->infaq = $infaq;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $tujuan = $this->infaq->target_infaq_id
            ? $this->infaq->targetInfaq->nama_target
            : $this->infaq->tujuan_infaq;

        return (new MailMessage)
            ->subject('Infaq Baru Masuk')
            ->greeting('Halo Bendahara')
            ->line('Ada infaq baru yang menunggu verifikasi.')
            ->line('Nama Penginfaq: '.$this->infaq->nama_penginfaq)
            ->line('Tujuan: '.$tujuan)
            ->line('Nominal: Rp '.number_format($this->infaq->nominal,0,',','.'))
            ->line('Silakan lakukan verifikasi melalui sistem.')
            ->salutation('Masjid Al-Anshor Banjarmasin');
    }

    public function toDatabase($notifiable)
    {
        $tujuan = $this->infaq->target_infaq_id
            ? $this->infaq->targetInfaq->nama_target
            : $this->infaq->tujuan_infaq;

        return [
            'tipe' => 'Infaq Baru',
            'judul' => 'Infaq Baru',
            'pesan' =>
            'Infaq Rp '.number_format($this->infaq->nominal,0,',','.')
            .' dari '.$this->infaq->nama_penginfaq,
            'tujuan' => $tujuan
        ];
    }
}
