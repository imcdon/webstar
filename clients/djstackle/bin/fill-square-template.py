#!/usr/bin/env python3
"""
Fill Square's blank Item Library .xlsx template from our import CSV.

Usage:
  python bin/fill-square-template.py template.xlsx source.csv output.xlsx

Preserves rows 1-6 (instructions + headers). Data starts row 7.
"""

from __future__ import annotations

import csv
import sys
from pathlib import Path

try:
    import openpyxl
except ImportError:
    print("Install openpyxl: pip install openpyxl", file=sys.stderr)
    raise

HEADER_ROW = 6
DATA_START = 7


def col_map(ws) -> dict[str, int]:
    out: dict[str, int] = {}
    for c in range(1, ws.max_column + 1):
        name = ws.cell(HEADER_ROW, c).value
        if name:
            out[str(name).strip()] = c
    return out


def set_cell(ws, row: int, cols: dict[str, int], header: str, value) -> None:
    if header not in cols:
        return
    ws.cell(row, cols[header], value)


def main() -> int:
    if len(sys.argv) < 4:
        print(
            "Usage: fill-square-template.py <square-template.xlsx> <import.csv> <output.xlsx>",
            file=sys.stderr,
        )
        return 1

    template_path = Path(sys.argv[1])
    csv_path = Path(sys.argv[2])
    out_path = Path(sys.argv[3])

    wb = openpyxl.load_workbook(template_path)
    ws = wb["Items"] if "Items" in wb.sheetnames else wb.active
    cols = col_map(ws)

    # Remove any sample/data rows below header
    if ws.max_row >= DATA_START:
        ws.delete_rows(DATA_START, ws.max_row - DATA_START + 1)

    new_qty_col = next(
        (k for k in cols if k.startswith("New Quantity ")),
        "New Quantity",
    )
    current_qty_col = next(
        (k for k in cols if k.startswith("Current Quantity ")),
        None,
    )
    alert_enabled = next(
        (k for k in cols if k.startswith("Stock Alert Enabled ")),
        None,
    )
    alert_count = next(
        (k for k in cols if k.startswith("Stock Alert Count ")),
        None,
    )

    row_num = DATA_START
    with csv_path.open(newline="", encoding="utf-8-sig") as fh:
        reader = csv.DictReader(fh)
        for src in reader:
            qty = src.get(new_qty_col) or src.get("New Quantity") or "25"
            set_cell(ws, row_num, cols, "Token", src.get("Token", ""))
            set_cell(ws, row_num, cols, "Item Name", src.get("Item Name", ""))
            set_cell(ws, row_num, cols, "Customer-facing Name", src.get("Item Name", ""))
            set_cell(ws, row_num, cols, "Variation Name", src.get("Variation Name", ""))
            set_cell(ws, row_num, cols, "SKU", src.get("SKU", ""))
            set_cell(ws, row_num, cols, "Description", src.get("Description", ""))
            set_cell(ws, row_num, cols, "Categories", src.get("Category", ""))
            set_cell(ws, row_num, cols, "Item Type", src.get("Item Type", "") or "Physical")
            price = src.get("Price", "")
            set_cell(ws, row_num, cols, "Price", price)
            set_cell(ws, row_num, cols, "Sellable", src.get("Sellable", "Y"))
            set_cell(ws, row_num, cols, "Stockable", src.get("Stockable", "Y"))
            set_cell(ws, row_num, cols, "Option Name 1", src.get("Option Name 1", ""))
            set_cell(ws, row_num, cols, "Option Value 1", src.get("Option Value 1", ""))
            if current_qty_col:
                set_cell(ws, row_num, cols, current_qty_col, "")
            set_cell(ws, row_num, cols, new_qty_col, qty)
            if alert_enabled:
                set_cell(ws, row_num, cols, alert_enabled, "Y")
            if alert_count:
                set_cell(ws, row_num, cols, alert_count, "5")
            row_num += 1

    wb.save(out_path)
    print(f"Wrote {row_num - DATA_START} rows to {out_path}")
    print(f"  Quantity column: {new_qty_col}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())