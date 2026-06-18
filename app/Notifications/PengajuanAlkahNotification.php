<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\TransaksiAlkah;

class PengajuanAlkahNotification extends Notification
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
            ->subject('Pengajuan Alkah Baru')
            ->greeting('Halo Pengelola Alkah')
            ->line('Ada pengajuan pembelian alkah baru.')
            ->line('Kode Transaksi : '.$this->transaksi->kode_transaksi)
            ->line('Nama Jamaah : '.$this->transaksi->user->nama)
            ->line('Kode Alkah : '.$this->transaksi->alkah->kode_alkah)
            ->line('Total : Rp '.number_format($this->transaksi->total,0,',','.'));
            ->salutation('Masjid Al-Anshor Banjarmasin');
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipe' => 'Pengajuan Alkah',
            'judul' => 'Pengajuan Alkah Baru',
            'pesan' => 'Pengajuan alkah '.$this->transaksi->alkah->kode_alkah,
            'transaksi_id' => $this->transaksi->kode_transaksi,
        ];
    }
}
