@extends('layouts.admin')

@section('title', 'Detail Progress Tiket #' . $tiket->nomor_tiket . ' — Admin Walidata')

@section('content')
<div style="display: flex; flex-direction: column; gap: 20px;">

    <!-- Breadcrumb Navigasi Admin -->
    <nav class="app-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fi fi-rr-apps"></i> Dashboard Admin</a>
        <span class="separator">/</span>
        <a href="{{ route('admin.tiket.index') }}"><i class="fi fi-rr-ticket"></i> Daftar Tiket Support</a>
        <span class="separator">/</span>
        <span class="current">#{{ $tiket->nomor_tiket }}</span>
    </nav>

    <!-- Header Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <a href="{{ route('admin.tiket.index') }}" class="btn-pill-outline" style="font-size: 13px; padding: 7px 16px; background: #ffffff;">
            <i class="fi fi-rr-arrow-left"></i> Kembali ke Daftar Tiket
        </a>

        <div style="font-size: 12.5px; color: #64748b; display: flex; align-items: center; gap: 8px;">
            <i class="fi fi-rr-clock"></i>
            <span>Terakhir diperbarui: <strong>{{ $tiket->updated_at ? $tiket->updated_at->format('d/m/Y H:i') . ' WIB' : '-' }}</strong></span>
        </div>
    </div>

    <!-- 1. Card Visual Progress 5 Tahapan DTSEN -->
    <div class="progress-card">
        <div class="progress-top-info">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px; flex-wrap: wrap;">
                    <span class="ticket-tag-chip">#{{ $tiket->nomor_tiket }}</span>
                    @if($tiket->status === 'PERLU_PERBAIKAN')
                        <span class="badge-status pending" style="font-size: 12px; background: #fef3c7; color: #92400e; border: 1px solid #fde68a;"><i class="fi fi-rr-exclamation"></i> STATUS: PERLU PERBAIKAN OPD</span>
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
                    Instansi: <strong style="color: #1f2937;">{{ $tiket->nama_instansi }}</strong> | No SE: <strong>{{ $tiket->no_surat_edaran }}</strong> | Waktu Buat: {{ $tiket->created_at->format('d/m/Y H:i') }} WIB
                </div>
            </div>

            <div style="text-align: right;">
                <span style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Status Terkini:</span>
                <div style="font-size: 14px; font-weight: 700; color: {{ ($tiket->status === 'PERLU_PERBAIKAN') ? '#d97706' : (($tiket->step5_done) ? '#166534' : '#0b57d0') }}; margin-top: 2px;">
                    {{ $tiket->current_status_label }}
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

        <div style="margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border-divider); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <div>
                <span style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Catatan Verifikator:</span>
                <div style="font-size: 13.5px; color: #1f2937; margin-top: 2px;">
                    {{ $tiket->catatan_admin ?: 'Belum ada catatan verifikator.' }}
                </div>
            </div>

            <!-- Tombol Aksi Verifikasi Step Cepat -->
            @if(!$tiket->step5_done)
                @php
                    $nextStep = 1;
                    for ($s = 1; $s <= 5; $s++) {
                        if (!$tiket->isStepDone($s)) { $nextStep = $s; break; }
                    }
                @endphp
                <form action="{{ route('admin.tiket.step-ok', $tiket->id) }}" method="POST" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="step" value="{{ $nextStep }}">
                    <button type="submit" class="btn-pill-primary" style="background: #166534; border-color: #166534; font-size: 13px; padding: 8px 18px;">
                        <i class="fi fi-rr-check"></i> Setujui Progress {{ $nextStep }} Selesai
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- 2. Kontrol Panel Verifikasi Admin (Catatan & Status Variabel) -->
    <div class="form-card" style="padding: 24px 28px; border-radius: 20px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
            <i class="fi fi-rr-settings" style="color: #0b57d0;"></i>
            <span>Kelola Status Verifikasi & Catatan Verifikator</span>
        </h3>

        <form action="{{ route('admin.tiket.verifikasi-variabel', $tiket->id) }}" method="POST">
            @csrf
            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 16px; align-items: flex-start;">
                <div>
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">Keputusan Verifikasi Variabel:</label>
                    <select name="status_verifikasi" class="form-select" style="width: 100%; height: 40px; font-size: 13px;" required>
                        <option value="SESUAI" {{ $tiket->status !== 'PERLU_PERBAIKAN' ? 'selected' : '' }}>✓ Sesuai Klausul Surat Edaran</option>
                        <option value="PERLU_PERBAIKAN" {{ $tiket->status === 'PERLU_PERBAIKAN' ? 'selected' : '' }}>⚠️ Belum Sesuai (Minta Perbaikan OPD)</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 12.5px; font-weight: 700; color: #334155; margin-bottom: 6px;">Catatan Hasil Pemeriksaan Walidata:</label>
                    <div style="display: flex; gap: 10px;">
                        <input type="text" name="catatan_admin" value="{{ $tiket->catatan_admin }}" class="form-input" style="flex: 1; height: 40px; font-size: 13px;" placeholder="Isi catatan verifikasi atau alasan perlunya perbaikan variabel...">
                        <button type="submit" class="btn-pill-primary" style="font-size: 13px; padding: 8px 18px; white-space: nowrap;">
                            <i class="fi fi-rr-disk"></i> Simpan Catatan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- 3. Rincian Berkas & Variabel Permohonan -->
    <div class="form-card" style="padding: 28px 32px; border-radius: 20px;">
        <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 18px; display: flex; align-items: center; gap: 8px;">
            <i class="fi fi-rr-document" style="color: #0b57d0;"></i>
            <span>Rincian Berkas Permohonan Bagi Pakai DTSEN</span>
        </h3>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 18px; margin-bottom: 24px;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px;">
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Instansi Pemohon:</div>
                <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 3px;">{{ $tiket->nama_instansi }}</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 2px;">PIC: {{ $tiket->nama_pemohon }} ({{ $tiket->kontak_pemohon }} / {{ $tiket->email_pemohon }})</div>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px;">
                <div style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Surat Edaran Resmi:</div>
                <div style="font-size: 14px; font-weight: 700; color: #0f172a; margin-top: 3px;">{{ $tiket->no_surat_edaran }}</div>
                @if($tiket->file_surat)
                    <div style="margin-top: 6px;">
                        <a href="{{ route('admin.riwayat.download', $tiket->id) }}" class="btn-pill-outline" style="font-size: 11.5px; padding: 4px 12px; background: #fff;">
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
