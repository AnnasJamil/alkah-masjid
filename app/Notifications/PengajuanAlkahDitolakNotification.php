<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\TransaksiAlkah;

class PengajuanAlkahDitolakNotification extends Notification
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
            ->subject('Pengajuan Alkah Ditolak')
            ->greeting('Halo '.$notifiable->nama)
            ->line('Pengajuan alkah '. $this->transaksi->alkah->kode_alkah .' anda ditolak.')
            ->line('Alasan: '.$this->transaksi->alasan_penolakan);
            ->salutation('Masjid Al-Anshor Banjarmasin');
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipe' => 'Pengajuan Alkah Ditolak',
            'judul' => 'Pengajuan Ditolak',
            'pesan' => 'Pengajuan alkah '. $this->transaksi->alkah->kode_alkah .' anda ditolak',
            'alasan' => $this->transaksi->alasan_penolakan,
            'transaksi_id' => $this->transaksi->kode_transaksi,
        ];
    }
}
