"""
CLI Gateway untuk Python Backend
Memungkinkan pemanggilan langsung fungsi backend dari PHP/Laravel tanpa network dependency jika diperlukan.
"""

import os
import sys
import json

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
if BASE_DIR not in sys.path:
    sys.path.insert(0, BASE_DIR)

from python_backend.server import (
    list_tikets, get_tiket_detail, create_tiket, step_ok,
    update_variabel, delete_tiket, list_users, store_user,
    delete_user, list_riwayat_bagi_data, store_riwayat_bagi_data,
    delete_riwayat_bagi_data, list_audit_logs, health_check
)

def run_command(action, args):
    if action == "health":
        return health_check()
    elif action == "list_tikets":
        search = args.get("search")
        page = int(args.get("page", 1))
        return list_tikets(search=search, page=page)
    elif action == "get_tiket":
        return get_tiket_detail(int(args.get("id")))
    elif action == "create_tiket":
        return create_tiket(args)
    elif action == "step_ok":
        return step_ok(int(args.get("id")), args)
    elif action == "update_variabel":
        return update_variabel(int(args.get("id")), args)
    elif action == "delete_tiket":
        return delete_tiket(int(args.get("id")))
    elif action == "list_users":
        return list_users()
    elif action == "store_user":
        return store_user(args)
    elif action == "delete_user":
        return delete_user(int(args.get("id")))
    elif action == "list_riwayat_bagi_data":
        return list_riwayat_bagi_data()
    elif action == "store_riwayat_bagi_data":
        return store_riwayat_bagi_data(args)
    elif action == "delete_riwayat_bagi_data":
        return delete_riwayat_bagi_data(int(args.get("id")))
    elif action == "list_audit_logs":
        return list_audit_logs(int(args.get("limit", 50)))
    else:
        return {"status": "error", "message": f"Unknown action: {action}"}

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"status": "error", "message": "No action specified"}))
        sys.exit(1)

    act = sys.argv[1]
    raw_args = sys.argv[2] if len(sys.argv) > 2 else "{}"
    try:
        parsed_args = json.loads(raw_args)
    except Exception:
        parsed_args = {}

    try:
        result = run_command(act, parsed_args)
        print(json.dumps(result))
    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))
        sys.exit(1)
