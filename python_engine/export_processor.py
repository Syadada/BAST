#!/usr/bin/env python3
"""
SIMONEV Python Export Processor
Mengekspor matriks monitoring realisasi anggaran & kinerja ke format CSV & JSON standar dinas/pemerintahan.
"""

import os
import sys
import csv
import json
import sqlite3
from datetime import datetime

def get_base_dir():
    return os.path.dirname(os.path.dirname(os.path.abspath(__file__)))

def export_matrix():
    base_dir = get_base_dir()
    db_path = os.path.join(base_dir, "database", "database.sqlite")
    reports_dir = os.path.join(base_dir, "storage", "reports")
    os.makedirs(reports_dir, exist_ok=True)

    timestamp_str = datetime.now().strftime("%Y%m%d_%H%M%S")
    csv_filename = f"Laporan_Monitoring_Anggaran_{timestamp_str}.csv"
    csv_path = os.path.join(reports_dir, csv_filename)

    conn = sqlite3.connect(db_path)
    conn.row_factory = sqlite3.Row
    cursor = conn.cursor()

    # Ambil metadata instansi
    cursor.execute("SELECT * FROM pengaturans LIMIT 1")
    instansi = cursor.fetchone()
    nama_instansi = instansi["nama_instansi"] if instansi else "Pemerintah Daerah"
    nama_dinas = instansi["nama_dinas"] if instansi else "Satuan Kerja"
    tahun_anggaran = instansi["tahun_anggaran_aktif"] if instansi else 2026

    # Ambil rincian kegiatan dan total realisasi
    cursor.execute("""
        SELECT 
            p.nama_program,
            k.kode_kegiatan,
            k.nama_kegiatan,
            k.unit_kerja,
            k.penanggung_jawab,
            k.kategori_belanja,
            k.triwulan_target,
            k.pagu_anggaran,
            k.target_fisik,
            k.status_kegiatan,
            COALESCE(SUM(r.nominal_realisasi), 0) as total_realisasi,
            COALESCE(MAX(r.progres_fisik_persen), 0) as progres_fisik_terakhir,
            COUNT(r.id) as jumlah_transaksi_spj
        FROM kegiatans k
        LEFT JOIN programs p ON k.program_id = p.id
        LEFT JOIN realisasis r ON k.id = r.kegiatan_id
        GROUP BY k.id
        ORDER BY p.id ASC, k.id ASC
    """)
    rows = cursor.fetchall()

    with open(csv_path, mode="w", newline="", encoding="utf-8-sig") as f:
        writer = csv.writer(f, delimiter=";")
        
        # Header Laporan Eksekutif
        writer.writerow(["LAPORAN HASIL MONITORING & EVALUASI REALISASI ANGGARAN"])
        writer.writerow(["Instansi:", nama_instansi])
        writer.writerow(["Satuan Kerja:", nama_dinas])
        writer.writerow(["Tahun Anggaran:", tahun_anggaran])
        writer.writerow(["Tanggal Ekspor:", datetime.now().strftime("%d/%m/%Y %H:%M:%S")])
        writer.writerow([]) # Baris kosong
        
        # Header Tabel
        writer.writerow([
            "No",
            "Program",
            "Kode Kegiatan",
            "Nama Kegiatan",
            "Unit / Bidang",
            "PPTK / Penanggung Jawab",
            "Kategori Belanja",
            "Target Triwulan",
            "Pagu Anggaran (Rp)",
            "Realisasi Keuangan (Rp)",
            "Sisa Anggaran (Rp)",
            "Capaian Keuangan (%)",
            "Target Fisik (%)",
            "Realisasi Fisik (%)",
            "Deviasi Fisik (%)",
            "Jml SP2D / SPJ",
            "Status Kegiatan",
            "Evaluasi Kinerja"
        ])

        total_pagu_all = 0.0
        total_real_all = 0.0
        no = 1

        for r in rows:
            pagu = float(r["pagu_anggaran"])
            real = float(r["total_realisasi"])
            sisa = pagu - real
            pct_keu = round((real / pagu * 100), 2) if pagu > 0 else 0.0
            fisik_target = float(r["target_fisik"])
            fisik_real = float(r["progres_fisik_terakhir"])
            deviasi_fis = round(fisik_real - fisik_target, 2)
            
            total_pagu_all += pagu
            total_real_all += real

            if pct_keu >= 80 and fisik_real >= 80:
                eval_kinerja = "Sangat Baik"
            elif pct_keu >= 50:
                eval_kinerja = "Sesuai Target"
            elif pct_keu >= 25:
                eval_kinerja = "Perlu Perhatian"
            else:
                eval_kinerja = "Kritis / Terlambat"

            writer.writerow([
                no,
                r["nama_program"] or "-",
                r["kode_kegiatan"],
                r["nama_kegiatan"],
                r["unit_kerja"],
                r["penanggung_jawab"],
                r["kategori_belanja"],
                r["triwulan_target"],
                f"{pagu:,.2f}".replace(",", "X").replace(".", ",").replace("X", "."),
                f"{real:,.2f}".replace(",", "X").replace(".", ",").replace("X", "."),
                f"{sisa:,.2f}".replace(",", "X").replace(".", ",").replace("X", "."),
                f"{pct_keu}%",
                f"{fisik_target}%",
                f"{fisik_real}%",
                f"{deviasi_fis}%",
                r["jumlah_transaksi_spj"],
                r["status_kegiatan"],
                eval_kinerja
            ])
            no += 1

        # Baris Rekapitulasi Akhir
        sisa_all = total_pagu_all - total_real_all
        pct_all = round((total_real_all / total_pagu_all * 100), 2) if total_pagu_all > 0 else 0.0
        writer.writerow([])
        writer.writerow([
            "", "TOTAL KESELURUHAN", "", "", "", "", "", "",
            f"{total_pagu_all:,.2f}".replace(",", "X").replace(".", ",").replace("X", "."),
            f"{total_real_all:,.2f}".replace(",", "X").replace(".", ",").replace("X", "."),
            f"{sisa_all:,.2f}".replace(",", "X").replace(".", ",").replace("X", "."),
            f"{pct_all}%", "", "", "", "", "", ""
        ])

    conn.close()

    output = {
        "status": "success",
        "file_name": csv_filename,
        "file_path": csv_path,
        "total_baris": len(rows),
        "total_pagu": total_pagu_all,
        "total_realisasi": total_real_all,
        "persen_keseluruhan": pct_all
    }
    return output

if __name__ == "__main__":
    res = export_matrix()
    print(json.dumps(res, ensure_ascii=False, indent=2))
