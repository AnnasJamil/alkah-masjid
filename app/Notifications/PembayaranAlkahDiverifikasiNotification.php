<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\PembayaranAlkah;

class PembayaranAlkahDiverifikasiNotification extends Notification
{
    use Queueable;

    protected $pembayaran;

    public function __construct(PembayaranAlkah $pembayaran)
    {
        $this->pembayaran = $pembayaran;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pembayaran Berhasil Diverifikasi')
            ->greeting('Halo '.$notifiable->nama)
            ->line('Pembayaran alkah Anda ' . $this->pembayaran->transaksiAlkah->alkah->kode_alkah . ' telah diverifikasi.')
            ->line('Status transaksi sekarang: Lunas')
            ->salutation('Masjid Al-Anshor Banjarmasin');
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipe' => 'Pembayaran Diverifikasi',
            'judul' => 'Pembayaran Alkah Diverifikasi',
            'pesan' => 'Pembayaran alkah Anda ' . $this->pembayaran->transaksiAlkah->alkah->kode_alkah . ' telah diverifikasi',
        ];
    }
}
