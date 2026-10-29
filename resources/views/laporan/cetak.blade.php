<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Monitoring Kegiatan & Anggaran TA {{ $pengaturan->tahun_anggaran_aktif ?? 2026 }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 15mm 15mm 15mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .header h2 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header h3 {
            font-size: 13px;
            margin: 4px 0 0;
            font-weight: normal;
        }

        .header p {
            font-size: 10px;
            margin: 4px 0 0;
            color: #555;
        }

        .meta-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        th, td {
            border: 1px solid #333;
            padding: 6px 8px;
            font-size: 10px;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 300px;
            text-align: center;
            font-size: 11px;
        }

        .signature-space {
            height: 65px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; padding: 12px; background: #e0f2fe; border: 1px solid #bae6fd; border-radius: 6px; display: flex; justify-content: space-between; align-items: center;">
        <span style="color: #0369a1; font-weight: bold;">Halaman Format Cetak Resmi (A4 Landscape)</span>
        <div>
            <button onclick="window.print()" style="padding: 8px 16px; background: #0284c7; color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Cetak Laporan / Simpan PDF</button>
            <button onclick="window.close()" style="padding: 8px 16px; background: #64748b; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-left: 8px;">Tutup</button>
        </div>
    </div>

    <div class="header">
        <h2>{{ $pengaturan->nama_instansi ?? 'PEMERINTAH DAERAH' }}</h2>
        <h3>{{ $pengaturan->nama_dinas ?? 'SATUAN KERJA PERANGKAT DAERAH' }}</h3>
        <p>LAPORAN HASIL MONITORING, EVALUASI, DAN CAPAIAN REALISASI KEGIATAN TAHUN ANGGARAN {{ $pengaturan->tahun_anggaran_aktif ?? 2026 }}</p>
    </div>

    <div class="meta-info">
        <div>
            <strong>Periode Laporan:</strong> Tahun Anggaran {{ $pengaturan->tahun_anggaran_aktif ?? 2026 }}
        </div>
        <div>
            <strong>Tanggal Cetak:</strong> {{ date('d F Y, H:i') }} WIB
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 80px;">Kode</th>
                <th>Program / Nama Kegiatan</th>
                <th>Unit Pelaksana</th>
                <th>PPTK</th>
                <th>Target TW</th>
                <th style="width: 95px;">Pagu (Rp)</th>
                <th style="width: 95px;">Realisasi (Rp)</th>
                <th style="width: 90px;">Sisa (Rp)</th>
                <th style="width: 45px;">% Keu</th>
                <th style="width: 45px;">% Fis</th>
                <th style="width: 75px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kegiatans as $idx => $k)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="text-center" style="font-family: monospace;">{{ $k->kode_kegiatan }}</td>
                <td>
                    <strong>{{ $k->nama_kegiatan }}</strong>
                    <div style="font-size: 9px; color: #555;">{{ $k->program->nama_program ?? '-' }}</div>
                </td>
                <td>{{ $k->unit_kerja }}</td>
                <td>{{ $k->penanggung_jawab }}</td>
                <td class="text-center">{{ $k->triwulan_target }}</td>
                <td class="text-right">{{ number_format($k->pagu_anggaran, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($k->total_realisasi, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($k->sisa_anggaran, 0, ',', '.') }}</td>
                <td class="text-center"><strong>{{ $k->persen_keuangan }}%</strong></td>
                <td class="text-center">{{ $k->progres_fisik_terakhir }}%</td>
                <td class="text-center">{{ $k->status_kegiatan }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f2f2f2; font-weight: bold;">
                <td colspan="6" class="text-right">TOTAL KESELURUHAN:</td>
                <td class="text-right">{{ number_format($totalPagu, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalRealisasi, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalSisa, 0, ',', '.') }}</td>
                <td class="text-center">{{ $persenKeuangan }}%</td>
                <td class="text-center">{{ $rataFisik }}%</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <!-- Lembar Pengesahan -->
    <div class="signature-container">
        <div class="signature-box">
            <div>Mengetahui,</div>
            <div>Pejabat Pembuat Komitmen (PPK)</div>
            <div class="signature-space"></div>
            <div class="signature-name">{{ $pengaturan->nama_ppk ?? 'Ir. Bambang Wijaya, S.T., M.T.' }}</div>
            <div>NIP. {{ $pengaturan->nip_ppk ?? '19820412 200801 1 015' }}</div>
        </div>

        <div class="signature-box">
            <div>Ditetapkan di Kota, {{ date('d F Y') }}</div>
            <div>Kepala Dinas / Pengguna Anggaran</div>
            <div class="signature-space"></div>
            <div class="signature-name">{{ $pengaturan->nama_pimpinan ?? 'Dr. H. Ahmad Sudrajat, M.Si.' }}</div>
            <div>NIP. {{ $pengaturan->nip_pimpinan ?? '19750815 199903 1 002' }}</div>
        </div>
    </div>

</body>
</html>
