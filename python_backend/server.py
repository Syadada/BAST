"""
Python FastAPI Backend Server - Layanan Bagi Pakai DTSEN (BAST) 2026
Menyediakan seluruh REST API operasi database, bisnis logika verifikasi 5 tahapan,
audit logging forensik, manajemen pengguna, dan penerbitan nomor BAST.
"""

import os
import sys
import json
import sqlite3
import hashlib
from datetime import datetime
from typing import List, Optional, Dict, Any

from fastapi import FastAPI, HTTPException, Query, Request, Body, UploadFile, File, Form
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse, StreamingResponse
from pydantic import BaseModel
import io
import csv

# Direktori Basis
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DB_PATH = os.path.join(BASE_DIR, "database", "database.sqlite")

app = FastAPI(
    title="BAST DTSEN Python Backend",
    version="2.0.0",
    description="Python Backend API untuk Layanan Bagi Pakai DTSEN & BAST"
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

def get_db():
    conn = sqlite3.connect(DB_PATH, timeout=15.0)
    conn.row_factory = sqlite3.Row
    conn.execute("PRAGMA journal_mode=WAL;")
    conn.execute("PRAGMA busy_timeout=5000;")
    return conn

# --------------------------------------------------------------------------
# 1. HEALTH CHECK & STATUS
# --------------------------------------------------------------------------
@app.get("/api/health")
def health_check():
    db_exists = os.path.exists(DB_PATH)
    total_tikets = 0
    total_users = 0
    if db_exists:
        try:
            with get_db() as conn:
                cursor = conn.cursor()
                cursor.execute("SELECT COUNT(*) FROM tiket_dtsens")
                total_tikets = cursor.fetchone()[0]
                cursor.execute("SELECT COUNT(*) FROM users")
                total_users = cursor.fetchone()[0]
        except Exception:
            pass

    return {
        "status": "healthy",
        "backend": "Python FastAPI v2.0 (Python 3.11)",
        "database": DB_PATH,
        "database_connected": db_exists,
        "metrics": {
            "total_tikets": total_tikets,
            "total_users": total_users,
        },
        "timestamp": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }

# --------------------------------------------------------------------------
# 2. AUDIT LOGGING HELPER
# --------------------------------------------------------------------------
def record_audit(action: str, description: str, user_id: Optional[int] = None,
                 user_name: Optional[str] = "System", user_role: Optional[str] = "system",
                 ip_address: str = "127.0.0.1", user_agent: str = "Python-Backend",
                 status: str = "SUCCESS", metadata: Optional[Dict] = None):
    try:
        with get_db() as conn:
            cursor = conn.cursor()
            meta_json = json.dumps(metadata or {})
            now_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
            cursor.execute("""
                INSERT INTO audit_logs (user_id, user_name, user_role, action, description, ip_address, user_agent, status, metadata, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            """, (user_id, user_name, user_role, action, description, ip_address, user_agent, status, meta_json, now_str))
            conn.commit()
    except Exception as e:
        print(f"[AUDIT ERROR] {e}", file=sys.stderr)

# --------------------------------------------------------------------------
# 3. TIKET DTSEN ENDPOINTS
# --------------------------------------------------------------------------
@app.get("/api/tikets")
def list_tikets(search: Optional[str] = None, page: int = 1, limit: int = 15):
    with get_db() as conn:
        cursor = conn.cursor()
        query = "SELECT * FROM tiket_dtsens"
        params = []
        if search:
            query += " WHERE nomor_tiket LIKE ? OR nama_instansi LIKE ? OR no_surat_edaran LIKE ? OR nama_pemohon LIKE ?"
            p = f"%{search.strip()}%"
            params.extend([p, p, p, p])
        
        query += " ORDER BY id DESC"
        
        cursor.execute(query, params)
        rows = cursor.fetchall()

        # Hitung statistik
        cursor.execute("SELECT COUNT(*) FROM tiket_dtsens")
        total_tiket = cursor.fetchone()[0]
        cursor.execute("SELECT COUNT(*) FROM tiket_dtsens WHERE step5_done = 1")
        total_selesai = cursor.fetchone()[0]
        total_proses = total_tiket - total_selesai

        # Pagination sederhana
        offset = (page - 1) * limit
        paginated_rows = rows[offset:offset + limit]

        results = []
        for r in paginated_rows:
            d = dict(r)
            # Parse variabel_dtsen JSON
            try:
                if d.get("variabel_dtsen"):
                    d["variabel_dtsen"] = json.loads(d["variabel_dtsen"])
                else:
                    d["variabel_dtsen"] = []
            except Exception:
                d["variabel_dtsen"] = []
            results.append(d)

        return {
            "tikets": results,
            "totalTiket": total_tiket,
            "totalSelesai": total_selesai,
            "totalProses": total_proses,
            "page": page,
            "limit": limit
        }

@app.get("/api/tikets/{tiket_id}")
def get_tiket_detail(tiket_id: int):
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM tiket_dtsens WHERE id = ?", (tiket_id,))
        row = cursor.fetchone()
        if not row:
            raise HTTPException(status_code=404, detail="Tiket tidak ditemukan.")
        d = dict(row)
        try:
            d["variabel_dtsen"] = json.loads(d.get("variabel_dtsen") or "[]")
        except Exception:
            d["variabel_dtsen"] = []
        return d

@app.post("/api/tikets")
def create_tiket(data: Dict[str, Any] = Body(...)):
    nama_instansi = data.get("nama_instansi", "").strip()
    no_surat = data.get("no_surat_edaran", "").strip()
    nama_pemohon = data.get("nama_pemohon", "PIC Instansi Pemohon").strip()
    kontak = data.get("kontak_pemohon", "").strip()
    email = data.get("email_pemohon", "").strip()
    tujuan = data.get("tujuan_pemanfaatan", "").strip()
    variabel = data.get("variabel_dtsen", [])

    if not nama_instansi or not no_surat:
        raise HTTPException(status_code=400, detail="Nama instansi dan Nomor Surat Edaran wajib diisi.")

    if isinstance(variabel, str):
        try:
            variabel = json.loads(variabel)
        except Exception:
            variabel = [v.strip() for v in variabel.split(",") if v.strip()]

    variabel = [v.strip() for v in variabel if str(v).strip()]
    if not variabel:
        raise HTTPException(status_code=400, detail="Minimal satu variabel DTSEN harus diisi.")

    # Generate Nomor Tiket Unik
    today_str = datetime.now().strftime("%Y%m%d")
    unique_suffix = hashlib.md5(f"{nama_instansi}_{datetime.now().timestamp()}".encode()).hexdigest()[:4].upper()
    nomor_tiket = f"TIK-DTSEN-{today_str}-{unique_suffix}"
    now_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")

    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("""
            INSERT INTO tiket_dtsens (
                nomor_tiket, nama_instansi, no_surat_edaran, nama_pemohon, kontak_pemohon, email_pemohon,
                tujuan_pemanfaatan, variabel_dtsen, current_step, step1_done, step1_at,
                step2_done, step3_done, step4_done, step5_done, status, catatan_admin,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 2, 1, ?, 0, 0, 0, 0, 'DIPROSES', ?, ?, ?)
        """, (
            nomor_tiket, nama_instansi, no_surat, nama_pemohon, kontak, email,
            tujuan, json.dumps(variabel), now_str,
            "Tahap 1 pengajuan tiket telah terkirim ke sistem Walidata.",
            now_str, now_str
        ))
        conn.commit()
        tiket_id = cursor.lastrowid

    record_audit(
        action="TIKET_CREATED",
        description=f"Tiket baru {nomor_tiket} diajukan oleh {nama_instansi}.",
        user_name=nama_pemohon,
        user_role="perangkat_daerah",
        metadata={"tiket_id": tiket_id, "nomor_tiket": nomor_tiket}
    )

    return {
        "status": "success",
        "message": "Tiket permohonan DTSEN berhasil dibuat.",
        "tiket_id": tiket_id,
        "nomor_tiket": nomor_tiket
    }

@app.post("/api/tikets/{tiket_id}/step-ok")
def step_ok(tiket_id: int, payload: Dict[str, Any] = Body(default={})):
    """
    Manipulasi persetujuan alur oleh Administrator (Klik OK -> Centang Hijau).
    """
    now_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM tiket_dtsens WHERE id = ?", (tiket_id,))
        row = cursor.fetchone()
        if not row:
            raise HTTPException(status_code=404, detail="Tiket tidak ditemukan.")

        d = dict(row)
        curr = d.get("current_step", 1)

        catatan = payload.get("catatan")

        if curr == 1:
            cursor.execute("""
                UPDATE tiket_dtsens SET
                    step1_done = 1, step1_at = ?, current_step = 2,
                    catatan_admin = COALESCE(?, 'Tahap 1 Selesai: Pengajuan Tiket Permohonan telah divalidasi.'),
                    updated_at = ?
                WHERE id = ?
            """, (now_str, catatan, now_str, tiket_id))
            step_name = "Tahap 1 (Pengajuan Tiket)"
        elif curr == 2:
            cursor.execute("""
                UPDATE tiket_dtsens SET
                    step2_done = 1, step2_at = ?, current_step = 3,
                    catatan_admin = COALESCE(?, 'Tahap 2 Selesai: Verifikasi Variabel Data telah disetujui Walidata.'),
                    updated_at = ?
                WHERE id = ?
            """, (now_str, catatan, now_str, tiket_id))
            step_name = "Tahap 2 (Verifikasi Variabel Data)"
        elif curr == 3:
            cursor.execute("""
                UPDATE tiket_dtsens SET
                    step3_done = 1, step3_at = ?, current_step = 4,
                    catatan_admin = COALESCE(?, 'Tahap 3 Selesai: Ekstraksi & Pemrosesan Data DTSEN selesai.'),
                    updated_at = ?
                WHERE id = ?
            """, (now_str, catatan, now_str, tiket_id))
            step_name = "Tahap 3 (Pemrosesan Data DTSEN)"
        elif curr == 4:
            cursor.execute("""
                UPDATE tiket_dtsens SET
                    step4_done = 1, step4_at = ?, current_step = 5,
                    catatan_admin = COALESCE(?, 'Tahap 4 Selesai: Dokumen BAST telah ditandatangani kedua pihak.'),
                    updated_at = ?
                WHERE id = ?
            """, (now_str, catatan, now_str, tiket_id))
            step_name = "Tahap 4 (Penandatanganan BAST)"
        elif curr == 5:
            cursor.execute("""
                UPDATE tiket_dtsens SET
                    step5_done = 1, step5_at = ?, status = 'SELESAI',
                    catatan_admin = COALESCE(?, 'Tahap 5 Selesai: Seluruh data telah diserahkan & BAST resmi disahkan.'),
                    updated_at = ?
                WHERE id = ?
            """, (now_str, catatan, now_str, tiket_id))
            step_name = "Tahap 5 (Penyerahan Data & BAST Sah)"
        else:
            return {"status": "ok", "message": "Semua tahapan telah rampung.", "current_step": 5}

        conn.commit()

    record_audit(
        action="STEP_APPROVED",
        description=f"Admin menyetujui {step_name} untuk tiket {d['nomor_tiket']}.",
        user_name=payload.get("admin_name", "Super Administrator"),
        user_role="superadmin",
        metadata={"tiket_id": tiket_id, "approved_step": curr}
    )

    return {
        "status": "success",
        "message": f"Berhasil memajukan ke tahap berikutnya ({step_name} selesai disetujui).",
        "tiket_id": tiket_id,
        "next_step": curr + 1 if curr < 5 else 5
    }

@app.post("/api/tikets/{tiket_id}/update-variabel")
def update_variabel(tiket_id: int, payload: Dict[str, Any] = Body(...)):
    variabel = payload.get("variabel_dtsen", [])
    if isinstance(variabel, str):
        variabel = [v.strip() for v in variabel.split(",") if v.strip()]

    now_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("""
            UPDATE tiket_dtsens SET variabel_dtsen = ?, updated_at = ? WHERE id = ?
        """, (json.dumps(variabel), now_str, tiket_id))
        conn.commit()

    record_audit(
        action="VARIABEL_UPDATED",
        description=f"Variabel pada tiket #{tiket_id} diperbarui.",
        metadata={"tiket_id": tiket_id, "variabel_count": len(variabel)}
    )

    return {"status": "success", "message": "Variabel DTSEN berhasil diperbarui."}

@app.delete("/api/tikets/{tiket_id}")
def delete_tiket(tiket_id: int):
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("SELECT nomor_tiket FROM tiket_dtsens WHERE id = ?", (tiket_id,))
        row = cursor.fetchone()
        if not row:
            raise HTTPException(status_code=404, detail="Tiket tidak ditemukan.")
        nomor_tiket = row[0]
        cursor.execute("DELETE FROM tiket_dtsens WHERE id = ?", (tiket_id,))
        conn.commit()

    record_audit(
        action="TIKET_DELETED",
        description=f"Tiket {nomor_tiket} telah dihapus dari sistem.",
        user_name="Super Administrator",
        user_role="superadmin",
        metadata={"tiket_id": tiket_id, "nomor_tiket": nomor_tiket}
    )

    return {"status": "success", "message": f"Tiket {nomor_tiket} berhasil dihapus."}

# --------------------------------------------------------------------------
# 4. RIWAYAT BAGI PAKAI DATA DTSEN
# --------------------------------------------------------------------------
@app.get("/api/riwayat-bagi-data")
def list_riwayat_bagi_data():
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM riwayat_bagi_data_dtsens ORDER BY id DESC")
        rows = cursor.fetchall()
        return [dict(r) for r in rows]

@app.post("/api/riwayat-bagi-data")
def store_riwayat_bagi_data(data: Dict[str, Any] = Body(...)):
    now_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("""
            INSERT INTO riwayat_bagi_data_dtsens (
                tiket_dtsen_id, user_id, nama_instansi, no_surat_edaran, tanggal_tiket_dibuat,
                tanggal_bast_selesai, no_bast, status_bast, orang_hadir, ip_atau_nomor_laptop,
                catatan, created_by, created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        """, (
            data.get("tiket_dtsen_id"),
            data.get("user_id"),
            data.get("nama_instansi"),
            data.get("no_surat_edaran"),
            data.get("tanggal_tiket_dibuat") or now_str,
            data.get("tanggal_bast_selesai") or now_str,
            data.get("no_bast") or f"BAST/DTSEN/{datetime.now().strftime('%Y/%m')}/{hashlib.md5(now_str.encode()).hexdigest()[:4].upper()}",
            data.get("status_bast", "SELESAI DITANDATANGANI (BAST SAH)"),
            data.get("orang_hadir", "-"),
            data.get("ip_atau_nomor_laptop", "127.0.0.1"),
            data.get("catatan", "-"),
            data.get("created_by", "Super Administrator"),
            now_str, now_str
        ))
        conn.commit()
        record_id = cursor.lastrowid

    record_audit(
        action="BAST_OFFICIAL_RECORDED",
        description=f"Riwayat BAST resmi untuk {data.get('nama_instansi')} berhasil dicatat.",
        user_name="Super Administrator",
        user_role="superadmin"
    )

    return {"status": "success", "message": "Riwayat bagi data DTSEN berhasil dicatat.", "id": record_id}

@app.delete("/api/riwayat-bagi-data/{record_id}")
def delete_riwayat_bagi_data(record_id: int):
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("DELETE FROM riwayat_bagi_data_dtsens WHERE id = ?", (record_id,))
        conn.commit()
    return {"status": "success", "message": "Riwayat bagi data berhasil dihapus."}

# --------------------------------------------------------------------------
# 5. USER MANAGEMENT ENDPOINTS
# --------------------------------------------------------------------------
@app.get("/api/users")
def list_users():
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("SELECT id, name, email, username, role, nama_instansi, nomor_kontak, last_login_at, last_login_ip, last_login_device FROM users ORDER BY id ASC")
        return [dict(r) for r in cursor.fetchall()]

@app.post("/api/users")
def store_user(data: Dict[str, Any] = Body(...)):
    username = data.get("username", "").strip()
    password = data.get("password", "").strip()
    name = data.get("name", "").strip()
    email = data.get("email", "").strip()
    role = data.get("role", "perangkat_daerah").strip()
    nama_instansi = data.get("nama_instansi", name).strip()
    kontak = data.get("nomor_kontak", "").strip()

    if not username or not password or not name:
        raise HTTPException(status_code=400, detail="Nama, username, dan password wajib diisi.")

    now_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    # Buat hash bcrypt atau fallback aman
    try:
        import bcrypt
        pwd_hash = bcrypt.hashpw(password.encode("utf-8"), bcrypt.gensalt()).decode("utf-8")
    except Exception:
        pwd_hash = hashlib.sha256(password.encode("utf-8")).hexdigest()

    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("SELECT id FROM users WHERE username = ? OR email = ?", (username, email))
        if cursor.fetchone():
            raise HTTPException(status_code=400, detail="Username atau email sudah terdaftar.")

        cursor.execute("""
            INSERT INTO users (name, email, username, password, role, nama_instansi, nomor_kontak, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        """, (name, email, username, pwd_hash, role, nama_instansi, kontak, now_str, now_str))
        conn.commit()
        user_id = cursor.lastrowid

    record_audit(
        action="USER_CREATED",
        description=f"Pengguna baru {name} ({username}) berhasil ditambahkan.",
        user_name="Super Administrator",
        user_role="superadmin"
    )

    return {"status": "success", "message": "Pengguna berhasil dibuat.", "user_id": user_id}

@app.delete("/api/users/{user_id}")
def delete_user(user_id: int):
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("SELECT username, role FROM users WHERE id = ?", (user_id,))
        row = cursor.fetchone()
        if not row:
            raise HTTPException(status_code=404, detail="User tidak ditemukan.")
        if row["role"] == "superadmin":
            raise HTTPException(status_code=400, detail="Akun Super Administrator utama tidak dapat dihapus.")
        cursor.execute("DELETE FROM users WHERE id = ?", (user_id,))
        conn.commit()

    return {"status": "success", "message": f"User {row['username']} berhasil dihapus."}

# --------------------------------------------------------------------------
# 6. AUDIT LOGS & EXPORT
# --------------------------------------------------------------------------
@app.get("/api/audit-logs")
def list_audit_logs(limit: int = 50):
    with get_db() as conn:
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM audit_logs ORDER BY id DESC LIMIT ?", (limit,))
        return [dict(r) for r in cursor.fetchall()]

@app.get("/api/export-csv")
def export_csv(type: str = Query("tikets")):
    output = io.StringIO()
    writer = csv.writer(output)

    with get_db() as conn:
        cursor = conn.cursor()
        if type == "logs":
            cursor.execute("SELECT id, user_name, user_role, action, description, ip_address, created_at FROM audit_logs ORDER BY id DESC")
            writer.writerow(["ID", "Nama Pengguna", "Peran", "Aksi", "Deskripsi", "IP Address", "Waktu"])
            for r in cursor.fetchall():
                writer.writerow(list(r))
            filename = f"Audit_Logs_DTSEN_{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"
        else:
            cursor.execute("SELECT id, nomor_tiket, nama_instansi, no_surat_edaran, nama_pemohon, current_step, status, created_at FROM tiket_dtsens ORDER BY id DESC")
            writer.writerow(["ID", "Nomor Tiket", "Instansi Pemohon", "No. Surat Edaran", "Nama PIC", "Tahap Berjalan", "Status", "Tanggal Buat"])
            for r in cursor.fetchall():
                writer.writerow(list(r))
            filename = f"Riwayat_Tiket_DTSEN_{datetime.now().strftime('%Y%m%d_%H%M%S')}.csv"

    output.seek(0)
    return StreamingResponse(
        iter([output.getvalue()]),
        media_type="text/csv",
        headers={"Content-Disposition": f"attachment; filename={filename}"}
    )

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("python_backend.server:app", host="127.0.0.1", port=8000, reload=True)
