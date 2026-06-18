<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\TransaksiAlkah;

class PengajuanAlkahDiterimaNotification extends Notification
{
    use Queueable;

    protected $transaksi;

    public function __construct(TransaksiAlkah $transaksi)
    {
        $this->transaksi = $transaksi;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Pengajuan Alkah Disetujui')
            ->greeting('Halo '.$notifiable->nama)
            ->line('Pengajuan alkah '. $this->transaksi->alkah->kode_alkah .' anda telah disetujui.')
            ->line('Silakan melakukan pembayaran.');
            ->salutation('Masjid Al-Anshor Banjarmasin');
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipe' => 'Pengajuan Alkah',
            'judul' => 'Pengajuan Disetujui',
            'pesan' => 'Pengajuan alkah '. $this->transaksi->alkah->kode_alkah .' anda telah disetujui',
            'transaksi_id' => $this->transaksi->kode_transaksi,
        ];
    }
}
