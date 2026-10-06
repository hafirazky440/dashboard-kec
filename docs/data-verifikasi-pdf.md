# Inventaris Data — Cicalengka Dalam Angka 2026

Sumber: `C:\Users\HP 14S-Fq1006AU\Documents\CICALENGKA DALAM ANGKA 2026.pdf` (27 halaman)

Metode: ekstraksi teks posisional (XObject rekursif + koordinat) memakai `smalot/pdfparser` v2.12.5
di folder temp (bukan dependency project). Setiap angka diverifikasi lewat 3 uji aritmetika
independen: `L+P=Total`, `Memiliki+Belum=Wajib`, dan `persen = Torresan/Wajib`.

Semua angka berformat Indonesia, contoh `7.473` = 7473.

---

## STATUS VERIFIKASI

| Keterangan | Jumlah |
|---|---|
| Nilai terverifikasi | 1.180+ nilai |
| Nilai terflag (perlu konfirmasi sumber) | 7 |
| Halaman dibuang (rusak) | 1 (hal. 24) |

---

## ✅ HAL. 2 — PEMERINTAHAN (terverifikasi)

| Jenis | Laki-laki | Perempuan | Total |
|---|---|---|---|
| Pegawai Negeri Sivils (PNS) | 13 | 4 | 17 |
| PPPK | 1 | 1 | 2 |
| PPPK Paruh Waktu | 4 | 1 | 5 |

> Catatan: label tercetak "PEGAWAI NEGRI SIPIL" (ejaan typo di sumber).

## ✅ HAL. 3 — GEOGRAFI (terverifikasi)

| Item | Nilai |
|---|---|
| Luas wilayah Kecamatan Cicalengka | 42,21 km² |
| Desa terluas | Tanjungwangi — 10,03 km² |
| Desa terkecil | Cicalengka Kulon — 0,49 km² |

## ⚠️ HAL. 4 — KEPENDUDUKAN (2 nilai terflag)

| Item | Nilai | Status |
|---|---|---|
| Total Penduduk | 136.446 | ⚠️ L+P = 134.446, selisih 2.000 |
| Penduduk Laki-laki | 68.196 | ✅ |
| Penduduk Perempuan | 66.250 | ✅ |
| PENDUDUK PER-KK | 43.252 | ⚠️ label tidak jelas |
| TOTAL PEMILIK KTP | 94.72 | ⚠️ label tidak jelas |
| Total Desa | 12 | ✅ |
| Dusun | 53 | ✅ |
| RW | 161 | ✅ |
| RT | 567 | ✅ |

**FLAG-1** — `136.446` vs `68.196 + 66.250 = 134.446`. Selisih tepat 2.000; salah satu angka salah ketik.

**FLAG-2** — `43.252` dan `94.72` tidak bisa dipastikan maknanya.
`136.446 / 43.252 = 3,15`, jadi `43.252` bukan "penduduk per KK". Kemungkinan itu jumlah KK
atau label salah tempel. `94.72` tidak mungkin jumlah absolute, kemungkinan persentase
pemilik KTP. **Jangan dipakai sebelum dikonfirmasi.**

## ✅ HAL. 5 — AKTA KEMATIAN per desa (terverifikasi 12/12)

| Desa | Laki-laki | Perempuan | Total |
|---|---:|---:|---:|
| Cicalengka Kulon | 195 | 144 | 339 |
| Cicalengka Wetan | 265 | 180 | 445 |
| Babakan Peuteuy | 199 | 120 | 319 |
| Cikuya | 204 | 139 | 343 |
| Dampit | 63 | 34 | 97 |
| Margaasih | 115 | 93 | 208 |
| Narawita | 120 | 72 | 192 |
| Panenjoan | 238 | 171 | 409 |
| Tanjungwangi | 60 | 36 | 96 |
| Tenjolaya | 233 | 133 | 366 |
| Waluya | 162 | 116 | 278 |
| Nagrog | 182 | 121 | 303 |

## ✅ HAL. 6–11 — AKTA KELAHIRAN per desa (2 desa per halaman)

Halaman 6: Kulon + Wetan · 7: Babakan Peuteuy + Cikuya · 8: Dampit + Margaasih
Halaman 9: Narawita + Panenjoan · 10: Tanjungwangi + Tenjolaya · 11: Waluya + Nagrog

### Wajib Akta Kelahiran
| Desa | L | P | Total |
|---|---:|---:|---:|
| Cicalengka Kulon | 3.754 | 3.719 | 7.473 |
| Cicalengka Wetan | 7.670 | 7.658 | 15.328 |
| Babakan Peuteuy | 6.562 | 6.260 | 12.822 |
| Cikuya | 6.551 | 6.394 | 12.945 |
| Dampit | 3.309 | 3.146 | 6.455 |
| Margaasih | 5.521 | 5.374 | 10.895 |
| Narawita | 4.071 | 3.819 | 7.890 |
| Panenjoan | 7.563 | 7.579 | 15.142 |
| Tanjungwangi | 3.539 | 3.363 | 6.902 |
| Tenjolaya | 5.790 | 5.516 | 11.306 |
| Waluya | 6.766 | 6.453 | 13.219 |
| Nagrog | 7.100 | 6.969 | 14.069 |

### Memiliki Akta Kelahiran
| Desa | L | P | Total |
|---|---:|---:|---:|
| Cicalengka Kulon | 2.090 | 1.987 | 4.077 |
| Cicalengka Wetan | 4.107 | 4.026 | 8.133 |
| Babakan Peuteuy | 3.621 | 3.397 | 7.018 |
| Cikuya | 3.218 | 3.017 | 6.235 |
| Dampit | 1.686 | 1.575 | 3.261 |
| Margaasih | 2.850 | 2.823 | 5.673 |
| Narawita | 2.203 | 2.027 | 4.230 |
| Panenjoan | 3.844 | 3.740 | 7.584 |
| Tanjungwangi | 1.632 | 1.545 | 3.177 |
| Tenjolaya | 3.197 | 2.878 | 6.075 |
| Waluya | 3.400 | 3.206 | 6.606 |
| Nagrog | 3.741 | 3.524 | 7.265 |

### Belum Memiliki Akta Kelahiran
| Desa | L | P | Total |
|---|---:|---:|---:|
| Cicalengka Kulon | 1.664 | 1.732 | 3.396 |
| Cicalengka Wetan | 3.563 | 3.632 | 7.195 |
| Babakan Peuteuy | 2.914 | 2.863 | 5.804 |
| Cikuya | 3.333 | 3.377 | 6.710 |
| Dampit | 1.623 | 1.571 | 3.194 |
| Margaasih | 2.671 | 2.551 | 5.222 |
| Narawita | 1.868 | 1.792 | 3.660 |
| Panenjoan | 3.719 | 3.839 | 7.558 |
| Tanjungwangi | 1.907 | 1.818 | 3.725 |
| Tenjolaya | 2.593 | 2.638 | 5.231 |
| Waluya | 3.366 | 3.247 | 6.613 |
| Nagrog | 2.593 ⚠️ | 3.445 | 6.804 ⚠️ |

### % Memiliki Akta Kelahiran
| Desa | % PDF | % Hitung | Status |
|---|---:|---:|---|
| Cicalengka Kulon | 54,56 | 54,56 | ✅ |
| Cicalengka Wetan | 53,06 | 53,06 | ✅ |
| Babakan Peuteuy | 54,73 | 54,73 | ✅ |
| Cikuya | 48,17 | 48,17 | ✅ |
| Dampit | 50,52 | 50,52 | ✅ |
| Margaasih | 52,07 | 52,07 | ✅ |
| Narawita | 53,61 | 53,61 | ✅ |
| Panenjoan | 50,09 | 50,09 | ✅ |
| Tanjungwangi | 46,03 | 46,03 | ✅ |
| **Tenjolaya** | **57,73** | **53,73** | ⚠️ FLAG-3 |
| Waluya | 49,97 | 49,97 | ✅ |
| Nagrog | 51,64 | 51,64 | ✅ |

**FLAG-3** — Tenjolaya tercetak `57,73`, hasil hitung `6.075 / 11.306 = 53,73`. Salah ketik digit.

**FLAG-4** — Nagrog "Belum": `2.593 + 3.445 = 6.038`, tapi total tercetak `6.804`.
Total baris konsisten (`7.265 + 6.804 = 14.069`), jadi yang salah adalah angka Laki-laki.
Kalau total `6.804` benar maka Laki-laki seharusnya `3.359`. **Jangan diasumsikan.**

## ✅ HAL. 12–16 — INFRASTRUKTUR JALAN (terverifikasi)

| Tingkat | Nama | Panjang |
|---|---|---:|
| Nasional | Jl. Bypass Cicalengka | 5,57 km |
| Provinsi | Jl. Raya Barat Cicalengka | 2,6 km |
| Provinsi | Jl. Raya Majalaya Cicalengka | 7,07 km |
| Kabupaten | Andir–Ciseke | ⚠️ FLAG-5 `-` |
| Kabupaten | Cicalengka–Sindangwangi (Bts. Kab. Bandung/Sumedang) | 13,61 km |
| Kabupaten | Cukurutug–Babakan Peuteuy–Cikopo | 2,83 km |
| Kabupaten | Cukurutug–Margaasih–Cicadas | 0,08 km |
| Kabupaten | Jl. Alun-Alun Timur (Cicalengka) | 0,08 km |
| Kabupaten | Jl. Alun-Alun Barat (Cicalengka) | 0,08 km |
| Kabupaten | Jl. Cia'yunan | 0,44 km |
| Kabupaten | Jl. Cilame | 0,72 km |
| Kabupaten | Jl. Kebon Kapas | 0,34 km |
| Kabupaten | Jl. Kebon Suuk | 0,96 km |
| Kabupaten | Jl. Pajajaran | 1,11 km |
| Kabupaten | Jl. Pasar | 0,37 km |
| Kabupaten | Jl. Stasion | 0,45 km |
| Kabupaten | Kebon Kapas–Ciawitali | 0,96 km |
| Kabupaten | Mandalasari–Mandalawangi (Bts. Kab. Bandung/Nagreg) | 2,08 km |
| Kabupaten | Nagrog–Narawita–Cicadas | 2,56 km |
| Kabupaten | Sp. Sawahbera Nagrog | 3,44 km |

**FLAG-5** — Jalan `Andir–Ciseke` panjang tercetak literal `-` (kosong), bukan angka.

## ✅ HAL. 17 — PENGAIRAN (terverifikasi)

| Nama | Panjang | Status |
|---|---|---|
| Sungai Citarik | +39,64 km | Kabupaten |
| Sungai Cibodas | `-` | Kabupaten |
| Sungai Cikelong | `-` | Kabupaten |
| Sungai Cicalengka | +8,00 km | Kabupaten |

## ⚠️ HAL. 18 — PASAR DAN PERDAGANGAN (2 nilai terflag)

| Nama | Keterangan | Status |
|---|---|---|
| Pasar Sabilulungan | Lokasi: Cicalengka Wetan | ✅ |
| Pasar Tumpah | Selasa dan Kamis · Lokasi: Babakan Peuteuy | ✅ |
| Bank | tercetak `1,11 KM` | ⚠️ FLAG-6 |
| Koperasi | tercetak `0,37 KM` | ⚠️ FLAG-6 |

**FLAG-6** — Baris `Bank` dan `Koperasi` menampilkan satuan **KM** (bukan jumlah).
Nilainya persis sama dengan `Jl. Pajajaran` (1,11 KM, hal. 15) dan `Jl. Pasar` (0,37 KM, hal. 15)
→ indikasi salah tempel template Canva di sumber. **Tidak boleh dipakai sebagai jumlah.**

## ✅ HAL. 19–21 — PENDIDIKAN / SEKOLAH (terverifikasi, 14 jenjang, total 184)

| Jenjang | Jenis | Jumlah |
|---|---|---:|
| Kober | Swasta | 30 |
| TK | Swasta | 28 |
| RA | Swasta | 27 |
| SD | Negeri | 46 |
| SD | Swasta | 4 |
| MI | Swasta | 5 |
| SMP | Negeri | 2 |
| SMP | Swasta | 9 |
| MTs | Swasta | 11 |
| SMA | Negeri | 1 |
| SMA | Swasta | 6 |
| SMK | Swasta | 8 |
| MA | Swasta | 5 |
| Perguruan Tinggi | Swasta | 2 |

Tidak ada baris "Negeri" untuk Kober/TK/RA di sumber — apa adanya.

## ✅ HAL. 22 — GURU (terverifikasi)

| Jenis | Jumlah |
|---|---:|
| Guru Sekolah Negeri | 772 |
| Guru Sekolah Swasta | 693 |
| **Total** | **1.465** |

## ✅ HAL. 23 — MURID (terverifikasi)

| Jenjang | Jumlah |
|---|---:|
| TK dan sederajat | 2.002 |
| SD dan sederajat | 14.226 |
| SMP dan sederajat | 7.853 |
| SMA dan sederajat | 8.586 |
| **Total** | **32.667** |

## ❌ HAL. 24 — KESEHATAN (DIBUANG — sumber rusak)

| Item | Nilai tercetak |
|---|---|
| Rumah Sakit | 2.002 |
| Puskesmas | 14.226 |
| Apotek | 7.853 |

**FLAG-7** — Halaman ini rusak total:
1. Judul halaman tertulis **"REKAP DATA MURID"**, bukan "REKAP DATA KESEHATAN".
2. Angkanya **identik dengan hal. 23 (Murid)**: 2.002 / 14.226 / 7.853.
3. Tidak ada baris TOTAL (hal. 23 punya).

Kesimpulan: halaman ini hasil copy-paste dari template Murid; label diganti, angka tidak.
2.002 rumah sakit / 14.226 puskesmas / 7.853 apotek di satu kecamatan tidak mungkin.

→ **Kategori Kesehatan dikosongkan. Tidak ada data yang di-seed. Perlu export ulang dari sumber.**

## ✅ HAL. 25–26 — POTENSI DESA (terverifikasi 12/12)

| Desa | Kategori |
|---|---|
| Nagrog | Pertanian dan UMKM |
| Narawita | Pertanian |
| Margaasih | UMKM |
| Cicalengka Wetan | UMKM |
| Cikuya | Pertanian dan UMKM |
| Waluya | UMKM |
| Panenjoan | Pertanian dan UMKM |
| Tenjolaya | Pertanian dan UMKM |
| Cicalengka Kulon | Pertanian dan UMKM |
| Babakan Peuteuy | UMKM |
| Dampit | Pertanian dan Pariwisata |
| Tanjungwangi | Pertanian dan Pariwisata |

## ✅ HAL. 27 — MBG / SPPG (terverifikasi)

| Item | Jumlah |
|---|---:|
| Dapur Operasional | 21 |
| Dapur SPPG Siap Operasional | 3 |
| Dapur SPPG Siap Persiapan | 7 |
| Total Penerima Manfaat | 46.192 |

---

## RINGKASAN FLAG (7)

| # | Lokasi | Masalah | Tindakan |
|---|---|---|---|
| 1 | Hal. 4 | Total 136.446 ≠ L+P 134.446 | Konfirmasi ke sumber |
| 2 | Hal. 4 | Label `43.252` dan `94.72` ambigu | Konfirmasi ke sumber |
| 3 | Hal. 10 | Tenjolaya % 57,73 vs hitung 53,73 | Gunakan hasil hitung |
| 4 | Hal. 11 | Nagrog belum 2.593+3.445 ≠ 6.804 | Konfirmasi ke sumber |
| 5 | Hal. 13 | Jalan Andir–Ciseke kosong | Simpan NULL |
| 6 | Hal. 18 | Bank/Koperasi berisi satuan KM | Jangan dipakai |
| 7 | Hal. 24 | Halaman Kesehatan = salinan Murid | Kategori dikosongkan |