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

/*
|--------------------------------------------------------------------------
| Grafik dashboard
|--------------------------------------------------------------------------
|
| Grafik sengaja digambar oleh JavaScript, bukan gambar statis, supaya
| angkanya selalu ikut berubah saat data di database diperbarui.
|
| Semua data sudah dikirim server lewat window.__dataDashboard, sehingga
| angka di grafik dan angka di teks berasal dari sumber yang sama.
|
*/

Chart.defaults.font.family = "'Instrument Sans', ui-sans-serif, system-ui, sans-serif";
Chart.defaults.color = '#64748b';

// Warna diambil dari palet yang sama dengan kelas warna dashboard.
// Kalau warnanya diganti di resources/css/app.css, ubah juga di sini.
const PALET = {
    emerald: '#10b981',
    emeraldGelap: '#059669',
    amber: '#f59e0b',
    biru: '#3b82f6',
    lime: '#84cc16',
    slate: '#cbd5e1',
};

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
                    labels: { boxWidth: 12, usePointStyle: true },
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
                    ticks: { autoSkip: false, maxRotation: 60, minRotation: 0 },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9' },
                    ticks: { precision: 0 },
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
            cutout: '58%',
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12, usePointStyle: true } },
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
                    {
                        label: 'Negeri',
                        data: nilaiNegeri,
                        backgroundColor: PALET.emerald,
                        borderRadius: 4,
                    },
                    {
                        label: 'Swasta',
                        data: nilaiSwasta,
                        backgroundColor: PALET.amber,
                        borderRadius: 4,
                    },
                ],
            },
        });
    }

    // Murid per jenjang.
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
                        backgroundColor: PALET.biru,
                        borderRadius: 4,
                    },
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
            colors: [PALET.emerald, PALET.amber],
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
                        backgroundColor: [PALET.emeraldGelap, PALET.amber],
                        borderRadius: 4,
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
                        backgroundColor: PALET.lime,
                        borderRadius: 4,
                    },
                ],
            },
        });
    }
}

document.addEventListener('DOMContentLoaded', gambarGrafik);