"""One-shot: key white backgrounds from brand JPGs into transparent PNGs."""

from __future__ import annotations

from pathlib import Path

from PIL import Image

SRC_DIR = Path(
    r"C:\Users\iantm\.cursor\projects\c-xampp-htdocs-webstar-business-solutions\assets"
)
OUT_DIR = Path(__file__).resolve().parents[1] / "assets" / "img"

MAPPING = {
    "djstackle-logo-nav-bar-36e2da6c-8324-48c4-9fcd-3dbc5293ce88.jpg": "logo-nav.png",
    "full-gettin-jiggy-with-it-47c0c298-f2e2-451a-9577-352f3b594932.jpg": "logo-full.png",
    "djstackle-favicon-00d1d82e-8b8a-45d3-98cd-dacaf1580722.jpg": "favicon.png",
}


def remove_white_bg(im: Image.Image, threshold: int = 245, soft: int = 12) -> Image.Image:
    im = im.convert("RGBA")
    pixels = im.load()
    assert pixels is not None
    w, h = im.size
    for y in range(h):
        for x in range(w):
            r, g, b, a = pixels[x, y]
            brightness = (r + g + b) / 3
            if brightness >= threshold:
                pixels[x, y] = (r, g, b, 0)
            elif brightness >= threshold - soft:
                t = (brightness - (threshold - soft)) / soft
                pixels[x, y] = (r, g, b, int(a * (1 - t)))
    return im


def trim_transparent(im: Image.Image, pad: int = 4) -> Image.Image:
    bbox = im.getbbox()
    if not bbox:
        return im
    left, top, right, bottom = bbox
    left = max(0, left - pad)
    top = max(0, top - pad)
    right = min(im.width, right + pad)
    bottom = min(im.height, bottom + pad)
    return im.crop((left, top, right, bottom))


def main() -> None:
    OUT_DIR.mkdir(parents=True, exist_ok=True)
    for src_name, out_name in MAPPING.items():
        src = SRC_DIR / src_name
        out = trim_transparent(remove_white_bg(Image.open(src)))
        out_path = OUT_DIR / out_name
        out.save(out_path, "PNG", optimize=True)
        print(f"{out_name}: {out.size} -> {out_path}")

    fav = Image.open(OUT_DIR / "favicon.png")
    for size, name in [(32, "favicon-32.png"), (180, "apple-touch-icon.png")]:
        canvas = Image.new("RGBA", (size, size), (0, 0, 0, 0))
        fav_c = fav.copy()
        fav_c.thumbnail((size - 4, size - 4), Image.Resampling.LANCZOS)
        x = (size - fav_c.width) // 2
        y = (size - fav_c.height) // 2
        canvas.paste(fav_c, (x, y), fav_c)
        canvas.save(OUT_DIR / name, "PNG", optimize=True)
        print(f"{name}: {canvas.size}")


if __name__ == "__main__":
    main()
