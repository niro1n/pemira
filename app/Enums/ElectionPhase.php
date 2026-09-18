<?php

namespace App\Enums;

enum ElectionPhase: string
{
    case UPCOMING = 'upcoming';
    case REGISTRATION = 'registration';
    case VOTING = 'voting';
    case FINISHED = 'finished';

    public function label(): string
    {
        return match ($this) {
            self::UPCOMING => 'Pemilihan Belum Dimulai',
            self::REGISTRATION => 'Pendaftaran Sedang Berlangsung',
            self::VOTING => 'Voting Sedang Berlangsung',
            self::FINISHED => 'Pemilihan Selesai',
        };
    }

    public function badgeText(): string
    {
        return match ($this) {
            self::UPCOMING => 'BELUM DIMULAI',
            self::REGISTRATION => 'MASA PENDAFTARAN',
            self::VOTING => 'VOTING DIBUKA',
            self::FINISHED => 'PEMILIHAN SELESAI',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::UPCOMING => 'Tahap persiapan dan verifikasi data pemilih sebelum pembukaan pendaftaran.',
            self::REGISTRATION => 'Mahasiswa aktif yang eligible dapat melakukan aktivasi dan verifikasi akun.',
            self::VOTING => 'Bilik suara digital dibuka. Mahasiswa terdaftar dapat memberikan hak suara secara rahasia.',
            self::FINISHED => 'Pemungutan suara telah ditutup. Seluruh data suara terkunci dan siap diverifikasi.',
        };
    }

    public function isLive(): bool
    {
        return $this === self::VOTING;
    }
}
