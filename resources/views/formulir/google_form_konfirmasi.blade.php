<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanggapan Telah Dicatat — Formulir KAK</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Product+Sans:wght@400;700&family=Roboto:wght@300;400;500;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Unified Master Stylesheet (Single CSS) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="form-page">

    <div class="form-shell">
        
        <!-- Standard White Form Card (24px Border Radius) -->
        <div class="form-card">
            
            <!-- Top Utility Bar: Badge & Language Selector -->
            <div class="form-top-bar">
                <div class="form-badge-system">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span>Status: Terverifikasi SHA-256</span>
                </div>

                <div class="lang-selector-box">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                    </svg>
                    <select id="langSwitcher" class="lang-dropdown" aria-label="Pilih Bahasa">
                        <option value="id">🇮🇩 Bahasa Indonesia</option>
                        <option value="en">🇬🇧 English</option>
                        <option value="su">🇮🇩 Basa Sunda</option>
                        <option value="jv">🇮🇩 Basa Jawa</option>
                    </select>
                </div>
            </div>

            <div class="form-header">
                <h1 class="form-title" data-i18n="confirm_title">Tanggapan Telah Dicatat</h1>
                <p class="form-subtitle" data-i18n="confirm_subtitle">Pengajuan berkas Kerangka Acuan Kerja (KAK) telah berhasil diregistrasi.</p>
            </div>

            <!-- Success Notice Box -->
            <div class="form-alert-success" style="margin-bottom: 24px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span data-i18n="confirm_success_msg">Dokumen KAK berhasil disimpan dengan sertifikasi kriptografi Dual-Hash SHA-256.</span>
            </div>

            @if($kak)
                <!-- Detail Card -->
                <div style="border: 1px solid var(--form-border); border-radius: 12px; padding: 22px; background: #ffffff; margin-bottom: 24px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <div>
                            <span style="font-size: 11px; font-weight: 700; color: var(--form-blue-primary); text-transform: uppercase;" data-i18n="confirm_reg_no">Nomor Registrasi KAK</span>
                            <div style="font-family: var(--form-font-mono); font-size: 17px; font-weight: 700; color: #1f1f1f; margin-top: 3px;">
                                {{ $kak->nomor_registrasi }}
                            </div>
                        </div>
                        <span class="form-badge-ext">
                            {{ $kak->klasifikasi_dokumen }}
                        </span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 13px; margin-bottom: 16px;">
                        <div>
                            <span style="color: var(--form-text-muted); font-size: 12px;" data-i18n="confirm_lbl_title">Judul Kegiatan:</span>
                            <div style="font-weight: 700; color: #1f1f1f; margin-top: 2px;">{{ $kak->judul_kak }}</div>
                        </div>
                        <div>
                            <span style="color: var(--form-text-muted); font-size: 12px;" data-i18n="confirm_lbl_proposer">Pengusul / PPTK:</span>
                            <div style="font-weight: 700; color: #1f1f1f; margin-top: 2px;">{{ $kak->penanggung_jawab }} ({{ $kak->unit_kerja }})</div>
                        </div>
                        <div>
                            <span style="color: var(--form-text-muted); font-size: 12px;" data-i18n="confirm_lbl_file">Berkas Terunggah:</span>
                            <div style="font-weight: 600; color: #1f1f1f; margin-top: 2px;"><i class="fi fi-rr-clip"></i> {{ $kak->nama_file_asli }} ({{ $kak->ukuran_file }})</div>
                        </div>
                        <div>
                            <span style="color: var(--form-text-muted); font-size: 12px;" data-i18n="confirm_lbl_date">Tanggal Registrasi:</span>
                            <div style="font-weight: 600; color: #1f1f1f; margin-top: 2px;">{{ date('d F Y', strtotime($kak->tanggal_pengajuan)) }}</div>
                        </div>
                    </div>

                    <!-- Hash Box -->
                    <div style="background: #f8fafd; border: 1px solid #d3e3fd; border-radius: 8px; padding: 12px 14px;">
                        <div style="font-size: 11px; font-weight: 700; color: var(--form-blue-primary); margin-bottom: 4px; text-transform: uppercase;" data-i18n="confirm_lbl_hash">
                            <i class="fi fi-rr-shield-check"></i> Nilai Kriptografi SHA-256 (Integritas Berkas)
                        </div>
                        <div style="font-family: var(--form-font-mono); font-size: 12px; color: #1f1f1f; word-break: break-all; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                            <span id="gHashCode">{{ $kak->hash_sha256 }}</span>
                            <button type="button" class="form-link" onclick="copyHash()" style="background: none; border: none; font-weight: 700;" data-i18n="confirm_btn_copy">
                                Salin
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Bar -->
                <div class="form-actions-bar">
                    <div class="form-left-actions">
                        <a href="{{ route('formulir.index') }}" class="form-link" data-i18n="confirm_link_other">
                            ← Kirim Formulir Lain
                        </a>
                    </div>

                    <a href="{{ route('kak.cetak', $kak->id) }}" target="_blank" class="form-btn-submit" style="text-decoration: none;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 6 2 18 2 18 9"></polyline>
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                            <rect x="6" y="14" width="12" height="8"></rect>
                        </svg>
                        <span data-i18n="confirm_btn_print">Cetak Lembar Pengesahan (PRINT)</span>
                    </a>
                </div>
            @endif

        </div>

    </div>

    <!-- Unified Master JavaScript (Single JS) -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
