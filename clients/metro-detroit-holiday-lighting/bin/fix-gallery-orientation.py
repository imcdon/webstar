"""Bake correct orientation into gallery image pixels (no EXIF reliance)."""
from pathlib import Path

from PIL import Image

GALLERY = Path(__file__).resolve().parents[1] / "assets" / "img" / "gallery"

# Degrees clockwise to bring content upright
FIXES = {
    "commercial-02-blue-tree.jpg": 90,
    "commercial-03-building-entrance-wreath.jpg": 90,
    "commercial-08-lions-tree.jpg": 90,
    "residential-01-birchwood-all-white-house-tree.jpg": 90,
    "residential-02-birchwood-blue-tree.jpg": 90,
    "residential-03-birchwood-multicolor-tree.jpg": 90,
    "residential-04-birchwood-red-tree-only.jpg": 90,
    "residential-07-birchwood-white-tree-snow.jpg": 90,
    "residential-08-clark-white-tree.jpg": 90,
    "residential-10-daytime-stake-lights-and-roof.jpg": 180,
    "residential-16-small-house-roof-wreath-bush.jpg": 90,
    "residential-19-white-bushes-and-roof.jpg": 90,
}


def bake_rotate(path: Path, degrees_cw: int) -> None:
    im = Image.open(path)
    if degrees_cw == 90:
        fixed = im.transpose(Image.Transpose.ROTATE_270)  # 90 CW
    elif degrees_cw == 180:
        fixed = im.transpose(Image.Transpose.ROTATE_180)
    elif degrees_cw == 270:
        fixed = im.transpose(Image.Transpose.ROTATE_90)  # 270 CW / 90 CCW
    else:
        raise ValueError(f"unsupported rotation {degrees_cw}")

    if path.suffix.lower() in {".jpg", ".jpeg"}:
        fixed = fixed.convert("RGB")
        fixed.save(path, "JPEG", quality=92, optimize=True, exif=b"")
    else:
        fixed.save(path)
    print(f"fixed {path.name}: {degrees_cw} CW -> {fixed.size[0]}x{fixed.size[1]}")


def main() -> None:
    for name, deg in sorted(FIXES.items()):
        path = GALLERY / name
        if not path.is_file():
            print(f"missing {name}")
            continue
        bake_rotate(path, deg)


if __name__ == "__main__":
    main()
