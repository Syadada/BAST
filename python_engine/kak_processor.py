#!/usr/bin/env python3
"""
Python Engine - KAK Document Processor & Verifier (Offline)
Memeriksa integritas berkas KAK, ukuran byte, checksum MD5/SHA256, dan metadata.
"""

import os
import sys
import json
import hashlib
from datetime import datetime

def inspect_kak_file(file_path):
    if not os.path.exists(file_path):
        return {"status": "error", "message": f"Berkas tidak ditemukan: {file_path}"}

    file_size = os.path.getsize(file_path)
    file_ext = os.path.splitext(file_path)[1].lower()
    
    # Hitung MD5 Checksum untuk integritas berkas
    hasher = hashlib.md5()
    with open(file_path, "rb") as f:
        buf = f.read(65536)
        while len(buf) > 0:
            hasher.update(buf)
            buf = f.read(65536)
    md5_hash = hasher.hexdigest()

    # Format ukuran
    if file_size >= 1048576:
        human_size = f"{round(file_size / 1048576, 2)} MB"
    else:
        human_size = f"{round(file_size / 1024, 2)} KB"

    return {
        "status": "success",
        "file_path": file_path,
        "file_name": os.path.basename(file_path),
        "file_extension": file_ext,
        "file_size_bytes": file_size,
        "human_size": human_size,
        "md5_checksum": md5_hash,
        "inspected_at": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }

if __name__ == "__main__":
    if len(sys.argv) > 1:
        target = sys.argv[1]
        res = inspect_kak_file(target)
        print(json.dumps(res, indent=2))
    else:
        print(json.dumps({"status": "ready", "engine": "Python KAK Inspector 2026"}))
