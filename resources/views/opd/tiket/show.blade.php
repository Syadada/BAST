@extends('layouts.opd')

@section('title', 'Detail Tiket #' . $tiket->nomor_tiket . ' — Layanan DTSEN')
@section('page_title', 'Detail Progress Tiket')
@section('page_subtitle', '#' . $tiket->nomor_tiket . ' — ' . $tiket->nama_instansi)

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Breadcrumb Navigasi (Memastikan Pengguna Selalu Tahu Di Mana Mereka Berada) -->
    <nav class="app-breadcrumb">
        <a href="{{ route('opd.dashboard') }}"><i class="fi fi-rr-home"></i> Dashboard</a>
        <span class="separator">/</span>
        <a href="{{ route('opd.tiket.index') }}"><i class="fi fi-rr-ticket"></i> Tiket Saya</a>
        <span class="separator">/</span>
        <span class="current">#{{ $tiket->nomor_tiket }}</span>
    </nav>

    <!-- Header Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('opd.tiket.index') }}" class="btn-pill-outline" style="font-size: 13px; padding: 7px 16px; background: #ffffff;">
            <i class="fi fi-rr-arrow-left"></i> Kembali ke Daftar Tiket
        </a>

        <div style="font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px;">
            <i class="fi fi-rr-clock"></i>
            <span>Terakhir diperbarui: <strong>{{ $tiket->updated_at ? $tiket->updated_at->format('d/m/Y H:i') . ' WIB' : '-' }}</strong></span>
        </div>
    </div>

    <!-- 1. Progress Status Permohonan Bagi Pakai DTSEN (Visual 5 Tahap) -->
    <div class="progress-card">
        @if($tiket->status === 'PERLU_PERBAIKAN')
            <!-- Warning Banner: Verifikasi Mengharuskan Perbaikan Variabel -->
            <div style="background: #fffbeb; border: 1px solid #fde68a; border-left: 5px solid #d97706; border-radius: 14px; padding: 18px 22px; margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 14px; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 260px;">
                        <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; color: #92400e; font-size: 15px;">
                            <span style="font-size: 18px;"><i class="fi fi-rr-exclamation"></i></span>
                            <span>PERBAIKAN VARIABEL DIPERLUKAN (HASIL VERIFIKASI ADMIN)</span>
                        </div>
                        <div style="font-size: 13px; color: #78350f; margin-top: 6px; line-height: 1.5;">
                            Variabel yang Anda ajukan pada tiket <strong>{{ $tiket->nomor_tiket }}</strong> belum sesuai dengan klausul <strong>Surat Edaran No. {{ $tiket->no_surat_edaran }}</strong>.
                            <div style="margin-top: 10px; background: #ffffff; border: 1px solid #fde68a; border-radius: 8px; padding: 12px 14px; font-size: 13px; color: #92400e;">
                                <strong>Catatan Hasil Pengecekan Admin:</strong><br>
                                {{ $tiket->catatan_admin }}
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="button" onclick="toggleRevisiForm()" class="btn-pill-primary" style="background: #d97706; border-color: #d97706; font-size: 13px; padding: 8px 18px; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(217, 119, 6, 0.3);">
                            <span><i class="fi fi-rr-edit"></i> Perbaiki Variabel Sekarang</span>
                        </button>
                    </div>
                </div>

                <!-- Collapsible Form Edit / Revisi Variabel -->
                <div id="boxRevisiVariabel" style="margin-top: 20px; padding-top: 18px; border-top: 1px dashed #fcd34d; display: none;">
                    <form action="{{ route('tiket.update-variabel', $tiket->id) }}" method="POST">
                        @csrf
                        <div style="font-size: 13px; font-weight: 700; color: #78350f; margin-bottom: 6px;">
                            Silakan sesuaikan daftar variabel DTSEN berikut sesuai catatan admin di atas:
                        </div>
                        <div style="font-size: 12px; color: #92400e; margin-bottom: 12px;">
                            Pastikan variabel yang Anda inputkan tercantum pada lampiran Surat Edaran resmi.
                        </div>

                        <div id="revisiVarContainer" style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 14px;">
                            @php
                                $curVars = $tiket->variabel_dtsen ?? [];
                                if (empty($curVars)) $curVars = [''];
                            @endphp
                            @foreach($curVars as $idx => $v)
                                <div class="revisi-row" style="display: flex; gap: 10px; align-items: center;">
                                    <span class="revisi-label" style="font-size: 12px; font-weight: 700; color: #b45309; min-width: 90px; font-family: var(--font-mono);">Variabel {{ $idx + 1 }}:</span>
                                    <input type="text" name="variabel_dtsen[]" value="{{ $v }}" class="form-input" style="flex: 1; height: 38px; font-size: 13px;" placeholder="Contoh: NIK, Nama, Alamat, Status Kesejahteraan..." required>
                                    <button type="button" onclick="hapusBarisRevisi(this)" class="btn-pill-outline" style="color: #dc2626; border-color: #fca5a5; padding: 4px 10px; font-size: 12px;" title="Hapus baris variabel ini">✕</button>
                                </div>
                            @endforeach
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <button type="button" onclick="tambahKolomRevisi()" class="btn-pill-outline" style="font-size: 12px; padding: 6px 14px; color: #b45309; border-color: #fcd34d; background: #fff;">
                                + Tambah Kolom Variabel
                            </button>
                            <div style="display: flex; gap: 8px;">
                                <button type="button" onclick="toggleRevisiForm()" class="btn-pill-outline" style="font-size: 12px; padding: 6px 14px; background: #fff;">
                                    Tutup
                                </button>
                                <button type="submit" class="btn-pill-primary" style="background: #166534; border-color: #166534; font-size: 12px; padding: 7px 18px;">
                                    <i class="fi fi-rr-disk"></i> Simpan & Ajukan Ulang ke Admin
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <div class="progress-top-info">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px; flex-wrap: wrap;">
                    <span class="ticket-tag-chip">#{{ $tiket->nomor_tiket }}</span>
                    @if($tiket->status === 'PERLU_PERBAIKAN')
                        <span class="badge-status pending" style="font-size: 12px; background: #fef3c7; color: #92400e; border: 1px solid #fde68a;"><i class="fi fi-rr-exclamation"></i> PERLU PERBAIKAN OPD</span>
                    @elseif($tiket->step5_done)
                        <span class="badge-status success" style="font-size: 12px;"><i class="fi fi-rr-check"></i> SELESAI (Semua Progress Selesai)</span>
                    @else
                        <span class="badge-status pending" style="font-size: 12px;">⏳ SEDANG DIPROSES</span>
                    @endif
                </div>
                <h2 style="font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">
                    Progress Status Permohonan Bagi Pakai DTSEN
                </h2>
                <div style="font-size: 13px; color: #64748b;">
                    Surat Edaran: <strong style="color: #1f2937;">{{ $tiket->no_surat_edaran }}</strong> | Waktu Buat: {{ $tiket->created_at->format('d/m/Y H:i') }} WIB
                </div>
            </div>

            <div style="text-align: right;">
                <span style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Status Terkini:</span>
                <div style="font-size: 14px; font-weight: 700; color: {{ ($tiket->status === 'PERLU_PERBAIKAN') ? '#d97706' : (($tiket->step5_done) ? '#166534' : '#0b57d0') }}; margin-top: 2px;">
                    @if($tiket->status === 'PERLU_PERBAIKAN')
                        Perlu Perbaikan Variabel vs SE
                    @else
                        {{ $tiket->current_status_label }}
                    @endif
                </div>
            </div>
        </div>

        @php
            $pct = $tiket->progress_percent;
        @endphp

        <!-- Overall Progress Bar Track -->
        <div class="progress-gauge-box">
            <div class="progress-gauge-labels">
                <span style="color: #475569; font-weight: 600;">Progress Alur Tahapan Layanan DTSEN</span>
                <span style="color: {{ $tiket->step5_done ? '#166534' : '#0b57d0' }}; font-weight: 700;">
                    {{ $pct }}% Selesai
                </span>
            </div>
            <div class="progress-track">
                <div class="progress-fill" style="width: {{ $pct }}%;"></div>
            </div>
        </div>

        <!-- 5-Step Process Flow: Progress 1, Progress 2, Progress 3, dst. -->
        <div class="step-flow">
            @foreach(\App\Models\TiketDtsen::$stepsDefinition as $stepNum => $stepDef)
                @php
                    $isDone = $tiket->isStepDone($stepNum);
                    $isActive = $tiket->isStepActive($stepNum);
                    $nodeClass = $isDone ? 'is-done' : ($isActive ? 'is-active' : 'is-pending');
                    $stepColAt = "step{$stepNum}_at";
                    $timeDone = $tiket->$stepColAt;
                @endphp
                
                <div class="step-node {{ $nodeClass }}">
                    <div class="step-node-icon">
                        @if($isDone)
                            <!-- Centang Hijau (✓) -->
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        @else
                            <span>{{ $stepNum }}</span>
                        @endif
                    </div>

                    <div class="step-node-content">
                        <div class="step-node-title" style="font-size: 14px; font-weight: 700; color: #0f172a;">
                            Progress {{ $stepNum }}
                        </div>

                        @if($isDone)
                            <span class="step-badge-status" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;">
                                ✓ Selesai {{ $timeDone ? '(' . $timeDone->format('d/m H:i') . ')' : '' }}
                            </span>
                        @elseif($stepNum === 1 && $tiket->status === 'PERLU_PERBAIKAN')
                            <span class="step-badge-status" style="background: #fffbeb; color: #92400e; border: 1px solid #fde68a;">
                                ⚠️ Perlu Perbaikan
                            </span>
                        @elseif($isActive)
                            <span class="step-badge-status" style="background: #eef2fa; color: #0b57d0; border: 1px solid #d2e3fc;">
                                ● Sedang Berjalan
                            </span>
                        @else
                            <span class="step-badge-status" style="background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;">
                                Menunggu
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border-divider);">
            <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Catatan Verifikator:</span>
            <div style="font-size: 13.5px; color: #1f2937; margin-top: 4px;">
                {{ $tiket->catatan_admin ?: 'Tiket permohonan berada dalam antrean proses resmi Walidata.' }}
            </div>
        </div>
    </div>

    <!-- 2. Rincian Data Surat Edaran & Variabel Permohonan -->
    <div class="form-card" style="padding: 28px 32px; border-radius: 20px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
            <i class="fi fi-rr-document" style="color: #0b57d0;"></i>
            <span>Rincian Berkas Permohonan Bagi Pakai DTSEN</span>
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; margin-bottom: 24px;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px;">
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Instansi Pemohon:</div>
                <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 3px;">{{ $tiket->nama_instansi }}</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">PIC: {{ $tiket->nama_pemohon }} ({{ $tiket->kontak_pemohon }})</div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px;">
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Surat Edaran Resmi:</div>
                <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 3px;">{{ $tiket->no_surat_edaran }}</div>
                @if($tiket->file_surat)
                    <div style="margin-top: 6px;">
                        <a href="{{ route('tiket.download-surat', $tiket->id) }}" class="btn-pill-outline" style="font-size: 11.5px; padding: 4px 12px; background: #fff;">
                            <i class="fi fi-rr-download"></i> Unduh File Surat ({{ $tiket->nama_file_asli ?: 'PDF' }})
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 8px;">
                Daftar Variabel DTSEN yang Diajukan:
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @foreach(($tiket->variabel_dtsen ?? []) as $v)
                    <span style="background: #f1f5f9; border: 1px solid #cbd5e1; color: #334155; font-size: 12.5px; font-weight: 600; padding: 4px 12px; border-radius: 8px;">
                        {{ $v }}
                    </span>
                @endforeach
            </div>
        </div>

        @if($tiket->tujuan_pemanfaatan)
            <div style="margin-top: 20px; border-top: 1px dashed #e2e8f0; padding-top: 14px;">
                <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 4px;">
                    Tujuan Pemanfaatan Data:
                </div>
                <div style="font-size: 13px; color: #334155; line-height: 1.5;">
                    {{ $tiket->tujuan_pemanfaatan }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection

@section('scripts')
<script>
    function toggleRevisiForm() {
        const box = document.getElementById('boxRevisiVariabel');
        if (!box) return;
        if (box.style.display === 'none' || box.style.display === '') {
            box.style.display = 'block';
            box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            box.style.display = 'none';
        }
    }

    function tambahKolomRevisi() {
        const container = document.getElementById('revisiVarContainer');
        if (!container) return;
        const rows = container.querySelectorAll('.revisi-row');
        const nextIdx = rows.length + 1;

        const div = document.createElement('div');
        div.className = 'revisi-row';
        div.style.cssText = 'display: flex; gap: 10px; align-items: center;';
        div.innerHTML = `
            <span class="revisi-label" style="font-size: 12px; font-weight: 700; color: #b45309; min-width: 90px; font-family: var(--font-mono);">Variabel ${nextIdx}:</span>
            <input type="text" name="variabel_dtsen[]" value="" class="form-input" style="flex: 1; height: 38px; font-size: 13px;" placeholder="Isi variabel DTSEN yang dibutuhkan..." required>
            <button type="button" onclick="hapusBarisRevisi(this)" class="btn-pill-outline" style="color: #dc2626; border-color: #fca5a5; padding: 4px 10px; font-size: 12px;" title="Hapus baris variabel ini">✕</button>
        `;
        container.appendChild(div);
        div.querySelector('input').focus();
    }

    function hapusBarisRevisi(btn) {
        const row = btn.closest('.revisi-row');
        const container = document.getElementById('revisiVarContainer');
        if (container && container.querySelectorAll('.revisi-row').length <= 1) {
            alert('Setidaknya minimal 1 kolom variabel harus tetap ada.');
            return;
        }
        row.remove();
        renumberRevisiVars();
    }

    function renumberRevisiVars() {
        const container = document.getElementById('revisiVarContainer');
        if (!container) return;
        const rows = container.querySelectorAll('.revisi-row');
        rows.forEach((row, i) => {
            const label = row.querySelector('.revisi-label');
            if (label) label.textContent = `Variabel ${i + 1}:`;
        });
    }
</script>
@endsection
