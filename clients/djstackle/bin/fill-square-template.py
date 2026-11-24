#!/usr/bin/env python3
"""
Fill Square's blank Item Library .xlsx template from our import CSV.

Usage:
  python bin/fill-square-template.py template.xlsx source.csv output.xlsx [--qty 9999]

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


def parse_args(argv: list[str]) -> tuple[Path, Path, Path, str | None]:
    positional: list[str] = []
    qty_override: str | None = None
    i = 0
    while i < len(argv):
        arg = argv[i]
        if arg == "--qty" and i + 1 < len(argv):
            qty_override = argv[i + 1]
            i += 2
            continue
        if arg.startswith("--qty="):
            qty_override = arg.split("=", 1)[1]
            i += 1
            continue
        positional.append(arg)
        i += 1

    if len(positional) < 3:
        print(
            "Usage: fill-square-template.py <square-template.xlsx> <import.csv> <output.xlsx> [--qty 9999]",
            file=sys.stderr,
        )
        raise SystemExit(1)

    return Path(positional[0]), Path(positional[1]), Path(positional[2]), qty_override


def main() -> int:
    template_path, csv_path, out_path, qty_override = parse_args(sys.argv[1:])

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
            qty = qty_override or src.get(new_qty_col) or src.get("New Quantity") or "25"
            set_cell(ws, row_num, cols, "Token", src.get("Token", ""))
            set_cell(ws, row_num, cols, "Item Name", src.get("Item Name", ""))
            set_cell(ws, row_num, cols, "Customer-facing Name", src.get("Item Name", ""))
            set_cell(ws, row_num, cols, "Variation Name", src.get("Variation Name", ""))
            set_cell(ws, row_num, cols, "SKU", src.get("SKU", ""))
            set_cell(ws, row_num, cols, "Description", src.get("Description", ""))
            set_cell(ws, row_num, cols, "Categories", src.get("Category", ""))
            set_cell(ws, row_num, cols, "Reporting Category", src.get("Category", ""))
            set_cell(ws, row_num, cols, "Item Type", src.get("Item Type", "") or "Physical")
            price = src.get("Price", "")
            set_cell(ws, row_num, cols, "Price", price)
            set_cell(ws, row_num, cols, "Sellable", src.get("Sellable", "Y"))
            set_cell(ws, row_num, cols, "Stockable", src.get("Stockable", "Y"))
            set_cell(ws, row_num, cols, "Option Name 1", src.get("Option Name 1", ""))
            set_cell(ws, row_num, cols, "Option Value 1", src.get("Option Value 1", ""))
            set_cell(ws, row_num, cols, "Option Name 2", src.get("Option Name 2", ""))
            set_cell(ws, row_num, cols, "Option Value 2", src.get("Option Value 2", ""))
            if current_qty_col:
                set_cell(ws, row_num, cols, current_qty_col, "")
            set_cell(ws, row_num, cols, new_qty_col, qty)
            if alert_enabled:
                set_cell(ws, row_num, cols, alert_enabled, "Y")
            if alert_count:
                set_cell(ws, row_num, cols, alert_count, "5")
            row_num += 1

    out_path.parent.mkdir(parents=True, exist_ok=True)
    wb.save(out_path)
    print(f"Wrote {row_num - DATA_START} rows to {out_path}")
    print(f"  Quantity column: {new_qty_col} = {qty_override or 'from CSV'}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
