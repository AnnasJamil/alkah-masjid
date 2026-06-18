<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\PembayaranAlkah;

class BuktiPembayaranDiuploadNotification extends Notification
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
        $jamaah = $this->pembayaran->transaksiAlkah->user;
        $alkah = $this->pembayaran->transaksiAlkah->alkah;

        return (new MailMessage)
            ->subject('Bukti Pembayaran Alkah')
            ->greeting('Halo Pengelola Alkah,')
            ->line('Ada bukti pembayaran baru yang perlu diverifikasi.')
            ->line('Nama Jamaah : '.$jamaah->nama)
            ->line('Kode Alkah : '.$alkah->kode_alkah)
            ->line('Total Bayar : Rp '.number_format(
                $this->pembayaran->total_bayar,
                0,
                ',',
                '.'
            ))
            ->salutation('Masjid Al-Anshor Banjarmasin');
    }

    public function toDatabase($notifiable)
    {
        return [
            'tipe' => 'Bukti Pembayaran Alkah',
            'judul' => 'Bukti Pembayaran Baru',
            'pesan' =>
                'Bukti pembayaran alkah ' .
                $this->pembayaran->transaksiAlkah->alkah->kode_alkah .
                ' perlu diverifikasi',
            'pembayaran_id' => $this->pembayaran->total_bayar,
        ];
    }
}
