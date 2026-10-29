#!/usr/bin/env python3
"""
SIMONEV Python Analytics Engine
Menganalisis serapan anggaran, deviasi fisik, indeks kinerja anggaran (IKA), dan anomali kegiatan offline.
"""

import os
import sys
import json
import sqlite3
from datetime import datetime

def get_db_path():
    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    return os.path.join(base_dir, "database", "database.sqlite")

def analyze_budget():
    db_path = get_db_path()
    if not os.path.exists(db_path):
        return {"status": "error", "message": f"Database SQLite tidak ditemukan di {db_path}"}

    conn = sqlite3.connect(db_path)
    conn.row_factory = sqlite3.Row
    cursor = conn.cursor()

    # 1. Total Pagu dari Master Kegiatan
    cursor.execute("SELECT COUNT(*) as total_kegiatan, COALESCE(SUM(pagu_anggaran), 0) as total_pagu FROM kegiatans")
    row_kegiatan = cursor.fetchone()
    total_kegiatan = row_kegiatan["total_kegiatan"]
    total_pagu = float(row_kegiatan["total_pagu"])

    # 2. Total Realisasi Keuangan dari Transaksi
    cursor.execute("SELECT COUNT(*) as total_transaksi, COALESCE(SUM(nominal_realisasi), 0) as total_realisasi FROM realisasis")
    row_realisasi = cursor.fetchone()
    total_transaksi = row_realisasi["total_transaksi"]
    total_realisasi = float(row_realisasi["total_realisasi"])

    sisa_anggaran = total_pagu - total_realisasi
    persen_keuangan = round((total_realisasi / total_pagu * 100), 2) if total_pagu > 0 else 0.0

    # 3. Rincian per Kegiatan & Progres Fisik
    cursor.execute("""
        SELECT 
            k.id, k.kode_kegiatan, k.nama_kegiatan, k.unit_kerja, k.penanggung_jawab,
            k.pagu_anggaran, k.target_fisik, k.status_kegiatan, k.triwulan_target,
            COALESCE(SUM(r.nominal_realisasi), 0) as total_realisasi,
            COALESCE(MAX(r.progres_fisik_persen), 0) as progres_fisik_terakhir
        FROM kegiatans k
        LEFT JOIN realisasis r ON k.id = r.kegiatan_id
        GROUP BY k.id
        ORDER BY k.id ASC
    """)
    kegiatan_list = []
    total_fisik_accum = 0.0
    kritis_count = 0
    sesuai_count = 0

    for row in cursor.fetchall():
        pagu = float(row["pagu_anggaran"])
        real = float(row["total_realisasi"])
        sisa = pagu - real
        p_keu = round((real / pagu * 100), 2) if pagu > 0 else 0.0
        p_fis = float(row["progres_fisik_terakhir"])
        total_fisik_accum += p_fis

        # Status Deviasi
        deviasi = round(p_fis - float(row["target_fisik"]), 2)
        if p_keu < 30.0 and row["triwulan_target"] in ["TW1", "TW2"]:
            indikator = "Kritis (Underspending)"
            kritis_count += 1
        elif p_keu >= 75.0 or p_fis >= 75.0:
            indikator = "Sangat Baik"
            sesuai_count += 1
        else:
            indikator = "Dalam Progres"
            sesuai_count += 1

        kegiatan_list.append({
            "id": row["id"],
            "kode": row["kode_kegiatan"],
            "nama": row["nama_kegiatan"],
            "unit": row["unit_kerja"],
            "penanggung_jawab": row["penanggung_jawab"],
            "pagu": pagu,
            "realisasi": real,
            "sisa": sisa,
            "persen_keuangan": p_keu,
            "progres_fisik": p_fis,
            "target_fisik": float(row["target_fisik"]),
            "deviasi_fisik": deviasi,
            "status_kegiatan": row["status_kegiatan"],
            "indikator": indikator
        })

    rata_fisik = round((total_fisik_accum / total_kegiatan), 2) if total_kegiatan > 0 else 0.0

    # 4. Agregasi per Unit Kerja
    cursor.execute("""
        SELECT 
            k.unit_kerja,
            COUNT(k.id) as jumlah_kegiatan,
            COALESCE(SUM(k.pagu_anggaran), 0) as pagu_unit,
            COALESCE(SUM(r.nominal_realisasi), 0) as realisasi_unit
        FROM kegiatans k
        LEFT JOIN realisasis r ON k.id = r.kegiatan_id
        GROUP BY k.unit_kerja
        ORDER BY pagu_unit DESC
    """)
    unit_stats = []
    for u in cursor.fetchall():
        p_unit = float(u["pagu_unit"])
        r_unit = float(u["realisasi_unit"])
        pct = round((r_unit / p_unit * 100), 2) if p_unit > 0 else 0.0
        unit_stats.append({
            "unit_kerja": u["unit_kerja"],
            "jumlah_kegiatan": u["jumlah_kegiatan"],
            "pagu": p_unit,
            "realisasi": r_unit,
            "sisa": p_unit - r_unit,
            "persen_serapan": pct
        })

    # 5. Nilai Indeks Kinerja Anggaran (IKA) - Bobot: 60% Keuangan + 40% Fisik
    skor_ika = round((persen_keuangan * 0.6) + (rata_fisik * 0.4), 2)
    if skor_ika >= 85:
        predikat_ika = "Sangat Memuaskan (A)"
    elif skor_ika >= 70:
        predikat_ika = "Baik (B)"
    elif skor_ika >= 55:
        predikat_ika = "Cukup (C)"
    else:
        predikat_ika = "Kurang / Perlu Evaluasi Khusus (D)"

    conn.close()

    result = {
        "status": "success",
        "waktu_analisis": datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
        "ringkasan": {
            "total_kegiatan": total_kegiatan,
            "total_transaksi": total_transaksi,
            "total_pagu": total_pagu,
            "total_realisasi": total_realisasi,
            "sisa_anggaran": sisa_anggaran,
            "persen_keuangan": persen_keuangan,
            "rata_fisik": rata_fisik,
            "skor_ika": skor_ika,
            "predikat_ika": predikat_ika,
            "kegiatan_kritis": kritis_count,
            "kegiatan_sesuai": sesuai_count
        },
        "unit_kerja_analisis": unit_stats,
        "daftar_kegiatan": kegiatan_list
    }

    return result

if __name__ == "__main__":
    data = analyze_budget()
    # Jika dipanggil dengan flag --json, cetak JSON murni
    if "--json" in sys.argv or True:
        print(json.dumps(data, ensure_ascii=False, indent=2))
