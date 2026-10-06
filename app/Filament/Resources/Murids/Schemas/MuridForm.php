<?php

namespace App\Filament\Resources\Murids\Schemas;

use App\Models\Tahun;
use App\Rules\UnikDalamLingkup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

/**
 * Formulir input jumlah murid per jenjang per tahun.
 *
 * Jenjang memakai label dari sumber, misalnya "SD dan sederajat", bukan
 * kode seperti SD. Alasannya, sumber memang mengelompokkan beberapa jenjang
 * sekolah dalam satu angka, jadi pemecahan angka itu akan mengubah
 * makna data.
 */
class MuridForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tahun_id')
                    ->label('Tahun')
                    ->options(fn () => Tahun::orderByDesc('tahun')->pluck('tahun', 'id'))
                    ->searchable()
                    ->required(),

                Select::make('jenjang')
                    ->label('Jenjang')
                    ->options([
                        'TK dan sederajat' => 'TK dan sederajat',
                        'SD dan sederajat' => 'SD dan sederajat',
                        'SMP dan sederajat' => 'SMP dan sederajat',
                        'SMA dan sederajat' => 'SMA dan sederajat',
                        'Perguruan Tinggi dan sederajat' => 'Perguruan Tinggi dan sederajat',
                    ])
                    ->searchable()
                    ->required()
                    ->scopedUnique(modifyQueryUsing: UnikDalamLingkup::dalam('tahun_id'))
                    ->validationMessages(['unique' => UnikDalamLingkup::pesan()])
                    ->helperText('Pilih dari daftar atau ketik jenjang lain bila muncul di sumber.'),

                TextInput::make('jumlah')
                    ->label('Jumlah Murid')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
            ]);
    }
}
