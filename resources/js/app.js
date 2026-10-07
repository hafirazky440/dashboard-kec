import {
    Chart,
    BarController,
    BarElement,
    DoughnutController,
    ArcElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
} from 'chart.js';

// Impor di atas memakai tree-shaking (hanya mengambil bagian yang dipakai),
// jadi tiap bagian harus didaftarkan secara eksplisit. Tanpa baris ini,
// Chart.js tidak mengenal skala "category" maupun tipe "bar"/"doughnut".
Chart.register(BarController, BarElement, DoughnutController, ArcElement, CategoryScale, LinearScale, Tooltip, Legend);

/*
|--------------------------------------------------------------------------
| Grafik dashboard
|--------------------------------------------------------------------------
|
| Grafik digambar oleh JavaScript, bukan gambar statis, supaya angkanya
| selalu ikut berubah saat data di database diperbarui.
|
| Semua data sudah dikirim server lewat window.__dataDashboard, sehingga
| angka di grafik dan angka di teks berasal dari sumber yang sama.
|
*/

Chart.defaults.font.family = "'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif";
Chart.defaults.color = '#404943';

// Warna diambil dari token design system di resources/css/app.css.
// Kalau paletnya diganti di sana, ubah juga di sini agar tetap selaras.
const PALET = {
    hijauTua: '#00351f',
    hijauKontainer: '#0f4d32',
    emerald: '#006d3b',
    mintDim: '#62dcad',
    mint: '#80f9c8',
    merah: '#ba1a1a',
    outline: '#707972',
};

// Warna batang untuk grafik murid per jenjang, mengikuti desain.
const WARNA_JENJANG = [PALET.mintDim, PALET.hijauTua, PALET.emerald, PALET.hijauKontainer];

const sum = (values) => values.reduce((total, value) => total + value, 0);

const angka = (value) => new Intl.NumberFormat('id-ID').format(value);

/**
 * Menggambar grafik batang vertikal.
 */
function grafikBatang(element, config) {
    new Chart(element, {
        type: 'bar',
        data: config.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: Boolean(config.adaLegenda),
                    position: 'bottom',
                    labels: { boxWidth: 12, usePointStyle: true, color: '#404943' },
                },
                tooltip: {
                    callbacks: {
                        label: (item) => ` ${item.dataset.label}: ${angka(item.parsed.y)}`,
                    },
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { autoSkip: false, maxRotation: 60, minRotation: 0, color: '#404943' },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#dee8ff' },
                    ticks: { precision: 0, color: '#707972' },
                },
            },
        },
    });
}

/**
 * Menggambar grafik lingkaran.
 */
function grafikLingkaran(element, config) {
    new Chart(element, {
        type: 'doughnut',
        data: {
            labels: config.labels,
            datasets: [
                {
                    label: config.label,
                    data: config.values,
                    backgroundColor: config.colors,
                    borderColor: '#ffffff',
                    borderWidth: 2,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: config.cutout ?? '58%',
            plugins: {
                legend: {
                    display: config.adaLegenda ?? true,
                    position: 'bottom',
                    labels: { boxWidth: 12, usePointStyle: true, color: '#404943' },
                },
                tooltip: {
                    callbacks: {
                        label: (item) => {
                            const total = sum(item.dataset.data);

                            if (! total) {
                                return ` ${item.label}: ${angka(item.parsed)}`;
                            }

                            const persen = (item.parsed / total) * 100;

                            return ` ${item.label}: ${angka(item.parsed)} (${persen.toFixed(1).replace('.', ',')}%)`;
                        },
                    },
                },
            },
        },
    });
}

/**
 * Menjalankan semua grafik yang ada di halaman.
 */
function gambarGrafik() {
    const data = window.__dataDashboard;

    if (! data) {
        return;
    }

    // Murid per jenjang (grafik utama).
    const murid = document.querySelector('[data-grafik="murid"]');

    if (murid) {
        grafikBatang(murid, {
            adaLegenda: false,
            data: {
                labels: data.murid.jenjang,
                datasets: [
                    {
                        label: 'Murid',
                        data: data.murid.murid,
                        backgroundColor: data.murid.jenjang.map((_, i) => WARNA_JENJANG[i % WARNA_JENJANG.length]),
                        borderRadius: 8,
                        maxBarThickness: 64,
                    },
                ],
            },
        });
    }

    // Demografi penduduk (donut gender). Legenda digambar di HTML,
    // jadi di dalam kanvas legenda dimatikan.
    const gender = document.querySelector('[data-grafik="gender"]');

    if (gender) {
        grafikLingkaran(gender, {
            label: 'Penduduk',
            labels: data.gender.label,
            values: data.gender.total,
            colors: [PALET.emerald, PALET.mintDim],
            cutout: '72%',
            adaLegenda: false,
        });
    }

    // Sekolah per jenjang, dipisah antara negeri dan swasta.
    const sekolah = document.querySelector('[data-grafik="sekolah"]');

    if (sekolah) {
        const jenjang = data.sekolah.jenjang;
        const nilaiNegeri = jenjang.map((item) => data.sekolah.negeri[item] ?? 0);
        const nilaiSwasta = jenjang.map((item) => data.sekolah.swasta[item] ?? 0);

        grafikBatang(sekolah, {
            adaLegenda: true,
            data: {
                labels: jenjang,
                datasets: [
                    { label: 'Negeri', data: nilaiNegeri, backgroundColor: PALET.hijauTua, borderRadius: 6 },
                    { label: 'Swasta', data: nilaiSwasta, backgroundColor: PALET.mintDim, borderRadius: 6 },
                ],
            },
        });
    }

    // Guru per status sekolah.
    const guru = document.querySelector('[data-grafik="guru"]');

    if (guru) {
        grafikLingkaran(guru, {
            label: 'Guru',
            labels: data.guru.label,
            values: data.guru.total,
            colors: [PALET.hijauTua, PALET.mintDim],
        });
    }

    // Akta kelahiran: sudah memiliki versus belum.
    const akta = document.querySelector('[data-grafik="akta"]');

    if (akta) {
        grafikBatang(akta, {
            adaLegenda: false,
            data: {
                labels: data.akta.label,
                datasets: [
                    {
                        label: 'Kelahiran',
                        data: data.akta.total,
                        backgroundColor: [PALET.emerald, PALET.merah],
                        borderRadius: 6,
                    },
                ],
            },
        });
    }

    // Potensi desa.
    const potensi = document.querySelector('[data-grafik="potensi"]');

    if (potensi) {
        grafikBatang(potensi, {
            adaLegenda: false,
            data: {
                labels: data.potensi.label,
                datasets: [
                    {
                        label: 'Jumlah desa',
                        data: data.potensi.total,
                        backgroundColor: PALET.mintDim,
                        borderRadius: 6,
                    },
                ],
            },
        });
    }
}

document.addEventListener('DOMContentLoaded', gambarGrafik);
