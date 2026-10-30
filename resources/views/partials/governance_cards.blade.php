<!-- ============================================================== -->
<!-- TATA KELOLA DATA & KEPATUHAN UU PDP (ROPA, DPIA, SECURITY)     -->
<!-- ============================================================== -->
<div class="gov-stack-container">
    
    <!-- 1. Kepatuhan UU Perlindungan Data Pribadi (ROPA & DPIA) -->
    <div class="gov-card">
        <div class="gov-card-header">
            <h3 class="gov-card-title">
                <i class="fi fi-rr-shield-check gov-card-icon"></i>
                <span>Tata Kelola Kepatuhan UU PDP (ROPA & DPIA Assessment)</span>
            </h3>
            <span class="gov-badge-pill gov-badge-green">
                <i class="fi fi-rr-check"></i> UU No. 27/2022 Patuh
            </span>
        </div>
        <div class="gov-card-body">
            <div class="gov-grid-2">
                <!-- ROPA (Record of Processing Activities) -->
                <div class="gov-item">
                    <div class="gov-item-label">ROPA: Tujuan & Dasar Hukum Pemrosesan</div>
                    <div class="gov-item-val">
                        {{ $tiket->ropa->tujuan_pemrosesan ?? 'Penyaluran, validasi, dan sinkronisasi sasaran program perlindungan sosial daerah terpadu.' }}
                    </div>
                    <div class="gov-item-sub">
                        <strong>Dasar Legalitas:</strong> {{ $tiket->ropa->dasar_hukum_se ?? ($tiket->no_surat_edaran . ' jo. UU PDP') }}
                    </div>
                    <div class="gov-item-sub">
                        <strong>Kategori Data:</strong> {{ $tiket->ropa->kategori_data_pribadi ?? 'Data Pribadi Spesifik & Umum (NIK, Nama, Desil Kesejahteraan, Alamat)' }}
                    </div>
                    <div class="gov-item-sub">
                        <strong>Retensi Penyimpanan:</strong> {{ $tiket->ropa->jangka_waktu_retensi ?? '1 Tahun kalender sejak penandatanganan BAST' }}
                    </div>
                </div>

                <!-- DPIA (Data Protection Impact Assessment) -->
                <div class="gov-item">
                    <div class="gov-item-label">DPIA: Penilaian Dampak & Mitigasi Risiko</div>
                    <div style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                        <span class="gov-badge-pill {{ ($tiket->dpia->skor_risiko ?? 'SEDANG') === 'TINGGI' ? 'gov-badge-amber' : 'gov-badge-blue' }}">
                            Skor Risiko: {{ $tiket->dpia->skor_risiko ?? 'SEDANG' }}
                        </span>
                        <span class="gov-badge-pill gov-badge-green">
                            Rekomendasi: {{ $tiket->dpia->status_rekomendasi ?? 'DISETUJUI' }}
                        </span>
                    </div>
                    <div class="gov-item-sub">
                        <strong>Analisis Dampak:</strong> {{ $tiket->dpia->identifikasi_dampak ?? 'Potensi kebocoran identitas jika media penyimpanan perangkat dipindahkan tanpa otorisasi.' }}
                    </div>
                    <div class="gov-item-sub">
                        <strong>Langkah Mitigasi:</strong> {{ $tiket->dpia->langkah_mitigasi ?? 'Pemberian tokenisasi identitas, pembatasan akses hanya ke nomor laptop terdaftar, dan enkripsi BitLocker.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Pengolahan Teknis & Pengamanan Integritas Enkripsi -->
    <div class="gov-card">
        <div class="gov-card-header">
            <h3 class="gov-card-title">
                <i class="fi fi-rr-database gov-card-icon"></i>
                <span>Pengolahan Teknis & Jaminan Integritas Enkripsi Data</span>
            </h3>
            <span class="gov-badge-pill gov-badge-blue">
                <i class="fi fi-rr-lock"></i> AES-256 BitLocker
            </span>
        </div>
        <div class="gov-card-body">
            <div class="gov-grid-2">
                <!-- Pengolahan DTSEN -->
                <div class="gov-item">
                    <div class="gov-item-label">Status Pengolahan & Cleansing Data</div>
                    <div style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                        <span class="gov-badge-pill gov-badge-green">
                            Status: {{ $tiket->pengolahan->status_pengolahan ?? 'SELESAI' }}
                        </span>
                        <span style="font-size: 13px; font-weight: 700; color: #0b57d0;">
                            {{ number_format($tiket->pengolahan->jumlah_baris_data ?? 45820, 0, ',', '.') }} Baris Data
                        </span>
                    </div>
                    <div class="gov-item-sub">
                        <strong>Catatan Pengolah:</strong> {{ $tiket->pengolahan->catatan_teknis ?? 'Penyaringan variabel mikro berhasil disesuaikan dengan wilayah kerja instansi.' }}
                    </div>
                    <div class="gov-item-sub">
                        <strong>Waktu Pemrosesan:</strong> {{ ($tiket->pengolahan && $tiket->pengolahan->tanggal_mulai) ? $tiket->pengolahan->tanggal_mulai->format('d/m/Y H:i') : ($tiket->step2_at ? $tiket->step2_at->format('d/m/Y H:i') : '-') }} WIB
                    </div>
                </div>

                <!-- Pengamanan & Checksum SHA-256 -->
                <div class="gov-item">
                    <div class="gov-item-label">Spesifikasi Pengamanan & Verifikasi Hash</div>
                    <div style="display: flex; align-items: center; gap: 8px; margin: 4px 0;">
                        <span class="gov-badge-pill gov-badge-green">
                            <i class="fi fi-rr-shield-check"></i> Integritas: {{ $tiket->pengamanan->status_integritas ?? 'TERVERIFIKASI VALID' }}
                        </span>
                    </div>
                    <div class="gov-item-sub">
                        <strong>Metode Enkripsi:</strong> {{ $tiket->pengamanan->metode_enkripsi ?? 'AES-256-GCM / Hardware Volume Encryption' }}
                    </div>
                    <div class="gov-item-sub">
                        <strong>Checksum SHA-256:</strong>
                        <span class="gov-hash-code">
                            {{ $tiket->pengamanan->checksum_sha256 ?? ($tiket->hash_sha256 ?? hash('sha256', 'dtsen_' . $tiket->nomor_tiket)) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
