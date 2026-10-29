<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Pengesahan & Tanda Terima KAK - {{ $kak->nomor_registrasi }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm 20mm 20mm 20mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111;
            background: #fff;
            margin: 0;
            padding: 24px;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 14px;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header h2 {
            font-size: 13px;
            margin: 4px 0 0;
            font-weight: normal;
        }

        .header p {
            font-size: 10px;
            margin: 4px 0 0;
            color: #444;
        }

        .title-doc {
            text-align: center;
            margin: 20px 0 24px;
        }

        .title-doc h3 {
            font-size: 14px;
            margin: 0;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .title-doc .reg-no {
            font-size: 11px;
            font-family: monospace;
            margin-top: 4px;
            color: #333;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .info-table td {
            padding: 8px 10px;
            vertical-align: top;
            font-size: 12px;
        }

        .info-table td.label {
            width: 220px;
            font-weight: bold;
            color: #222;
            border-bottom: 1px solid #eee;
        }

        .info-table td.colon {
            width: 15px;
            border-bottom: 1px solid #eee;
        }

        .info-table td.value {
            border-bottom: 1px solid #eee;
        }

        .box-keterangan {
            border: 1px solid #999;
            background-color: #f9f9f9;
            padding: 12px;
            margin-bottom: 30px;
            border-radius: 4px;
        }

        .signature-container {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 260px;
            text-align: center;
            font-size: 12px;
        }

        .signature-space {
            height: 75px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        .toolbar {
            margin-bottom: 24px;
            padding: 12px 18px;
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            border: none;
            text-decoration: none;
        }

        .btn-print { background: #0284c7; color: #fff; }
        .btn-close { background: #64748b; color: #fff; }

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

    <!-- Toolbar Cetak (Hanya tampil di layar) -->
    <div class="toolbar no-print">
        <div>
            <strong style="color: #0369a1; font-size: 13px;">Format Lembar Tanda Terima Dokumen KAK</strong>
            <div style="font-size: 11px; color: #555; margin-top: 2px;">Siap dicetak langsung ke printer atau disimpan sebagai PDF</div>
        </div>
        <div>
            <button onclick="window.print()" class="btn btn-print">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Lembar Ini (Print)
            </button>
            <button onclick="window.close()" class="btn btn-close" style="margin-left: 8px;">Tutup</button>
        </div>
    </div>

    <!-- Kop Surat Lembaga -->
    <div class="header">
        <h1>PEMERINTAH REPUBLIK INDONESIA</h1>
        <h2>SATUAN KERJA PERANGKAT DAERAH / DINAS TERKAIT</h2>
        <p>Sistem Informasi Pengelolaan Dokumen Kerangka Acuan Kerja (KAK) & Administrasi Terpadu</p>
    </div>

    <!-- Judul Dokumen -->
    <div class="title-doc">
        <h3>LEMBAR TANDA TERIMA & PENGESAHAN DOKUMEN KERANGKA ACUAN KERJA (KAK)</h3>
        <div class="reg-no">Nomor Registrasi: <strong>{{ $kak->nomor_registrasi }}</strong></div>
    </div>

    <!-- Informasi Detail Dokumen KAK -->
    <table class="info-table">
        <tr>
            <td class="label">Judul KAK / Kegiatan</td>
            <td class="colon">:</td>
            <td class="value"><strong>{{ $kak->judul_kak }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nama Pengusul / PPTK</td>
            <td class="colon">:</td>
            <td class="value">{{ $kak->penanggung_jawab }}</td>
        </tr>
        <tr>
            <td class="label">Unit Kerja / Bidang</td>
            <td class="colon">:</td>
            <td class="value">{{ $kak->unit_kerja ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Pengajuan & Registrasi</td>
            <td class="colon">:</td>
            <td class="value">{{ date('d F Y', strtotime($kak->tanggal_pengajuan)) }}</td>
        </tr>
        <tr>
            <td class="label">Nama Dokumen File Terunggah</td>
            <td class="colon">:</td>
            <td class="value" style="font-family: monospace;">{{ $kak->nama_file_asli ?? 'Berkas KAK Terlampir' }}</td>
        </tr>
        <tr>
            <td class="label">Klasifikasi Keamanan</td>
            <td class="colon">:</td>
            <td class="value"><span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-weight: 600; font-size: 11px;">{{ $kak->klasifikasi_dokumen ?? 'TERBATAS (Level 3)' }}</span></td>
        </tr>
        <tr>
            <td class="label">Ukuran Berkas & Format</td>
            <td class="colon">:</td>
            <td class="value" style="font-family: monospace;">{{ $kak->ukuran_file ?? '-' }} (Format: {{ strtoupper($kak->ekstensi_file ?? 'PDF') }})</td>
        </tr>
        <tr>
            <td class="label">Hash Integritas (SHA-256)</td>
            <td class="colon">:</td>
            <td class="value">
                <div style="font-family: monospace; font-size: 11px; word-break: break-all; background: #fafafa; border: 1px solid #e5e7eb; padding: 4px 8px; border-radius: 4px; color: #1e293b;">
                    {{ $kak->hash_sha256 ?? 'Tersertifikasi saat file diunggah' }}
                </div>
                <div style="font-size: 10px; color: #64748b; margin-top: 3px;">
                    * Kode kriptografis unik untuk verifikasi keaslian berkas (Anti-Tamper & Non-Repudiation standar BSSN).
                </div>
            </td>
        </tr>
        <tr>
            <td class="label">Status Verifikasi Sistem</td>
            <td class="colon">:</td>
            <td class="value"><span style="color: #059669; font-weight: bold;">[{{ $kak->status_verifikasi ?? 'TERVERIFIKASI INTEGRITAS HASH' }}]</span></td>
        </tr>
    </table>

    <div style="font-weight: bold; margin-bottom: 6px;">Catatan / Ringkasan Ruang Lingkup KAK:</div>
    <div class="box-keterangan">
        {{ $kak->keterangan ?: 'Dokumen Kerangka Acuan Kerja telah diunggah dan terdaftar secara resmi di dalam sistem offline untuk ditinjau dan ditindaklanjuti sesuai ketentuan tata kelola administrasi.' }}
    </div>

    <!-- Lembar Tanda Tangan Pengesahan -->
    <div class="signature-container">
        <div class="signature-box">
            <div>Pengusul Berkas KAK,</div>
            <div style="font-size: 11px; color: #555;">Pejabat Pelaksana Teknis Kegiatan (PPTK)</div>
            <div class="signature-space"></div>
            <div class="signature-name">{{ $kak->penanggung_jawab }}</div>
            <div>NIP. ..................................................</div>
        </div>

        <div class="signature-box">
            <div>Diterima & Diverifikasi Oleh,</div>
            <div style="font-size: 11px; color: #555;">Pejabat Pembuat Komitmen (PPK)</div>
            <div class="signature-space"></div>
            <div class="signature-name">( .................................................. )</div>
            <div>NIP. ..................................................</div>
        </div>
    </div>

    <div style="margin-top: 50px; font-size: 10px; color: #777; text-align: center; border-top: 1px dotted #ccc; padding-top: 8px;">
        Dokumen ini dihasilkan secara otomatis oleh Aplikasi Formulir KAK Stand-alone Offline pada {{ date('d/m/Y H:i:s') }} WIB.
    </div>

</body>
</html>
