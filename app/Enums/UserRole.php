<?php

namespace App\Enums;

/**
 * Peran pengguna panel admin.
 *
 * Dipakai juga oleh User::canAccessPanel() supaya daftar hak akses hanya
 * ada di satu tempat. Menambah peran baru cukup menambahkan case di sini,
 * tidak perlu menyentuh migration atau seeder.
 */
enum UserRole: string
{
    /** Boleh melihat dan mengubah seluruh data. */
    case Admin = 'admin';

    /** Boleh melihat dan mengubah data, tetapi tidak bisa menghapus. */
    case Editor = 'editor';

    /** Hanya boleh membaca. Berguna untuk kepala bagian data. */
    case Viewer = 'viewer';

    /**
     * Label yang tampil di antarmuka.
     */
    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Editor => 'Editor',
            self::Viewer => 'Pengamat',
        };
    }

    /**
     * Ringkasan hak akses, dipakai di form profil.
     */
    public function description(): string
    {
        return match ($this) {
            self::Admin => 'Akses penuh, termasuk menghapus data.',
            self::Editor => 'Boleh menambah dan mengubah, tidak boleh menghapus.',
            self::Viewer => 'Hanya boleh melihat data.',
        };
    }

    /**
     * Daftar pasangan nilai dan label untuk dropdown.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
