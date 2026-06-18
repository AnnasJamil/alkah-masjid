<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\PembayaranAlkah;

class BuktiPembayaranPerbaikiNotification extends Notification
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
            ->subject('Perbaiki Bukti Pembayaran')
            ->greeting('Halo '.$notifiable->nama)
            ->line('Bukti pembayaran Anda dialkah ' . $this->pembayaran->transaksiAlkah->alkah->kode_alkah . ' perlu diperbaiki.')
            ->line('Catatan revisi:')
            ->line($this->pembayaran->catatan)
            ->salutation('Masjid Al-Anshor Banjarmasin');
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipe' => 'Perbaiki Bukti Pembayaran',
            'judul' => 'Perbaiki Bukti Pembayaran Alkah ' . $this->pembayaran->transaksiAlkah->alkah->kode_alkah . ' anda',
            'pesan' => $this->pembayaran->catatan,
        ];
    }
}
