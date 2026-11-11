from pathlib import Path

from PIL import Image, ExifTags

gallery = Path(__file__).resolve().parents[1] / "assets" / "img" / "gallery"
orient_tag = next((k for k, v in ExifTags.TAGS.items() if v == "Orientation"), None)

print(f"{'file':55} {'w':>5} {'h':>5} {'shape':10} exif_orient")
total = 0
needs_fix = []
for p in sorted(gallery.iterdir()):
    if p.suffix.lower() not in {".jpg", ".jpeg", ".png", ".webp"}:
        continue
    im = Image.open(p)
    w, h = im.size
    orient = None
    try:
        exif = im.getexif()
        if exif and orient_tag in exif:
            orient = exif.get(orient_tag)
    except Exception as e:
        orient = f"err:{e}"
    shape = "portrait" if h > w else ("landscape" if w > h else "square")
    print(f"{p.name:55} {w:5} {h:5} {shape:10} {orient}")
    # EXIF 3/6/8 mean the file pixels need rotation for correct display
    if orient in (3, 6, 8):
        needs_fix.append((p.name, orient, shape, w, h))
    total += 1

print("TOTAL", total)
print("NEEDS_EXIF_FIX", len(needs_fix))
for item in needs_fix:
    print(" ", item)
