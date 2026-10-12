# Letterhead — print & Google Docs

## Files

| File | Purpose |
|------|---------|
| [assets/letterhead-webstar.html](assets/letterhead-webstar.html) | Printable US Letter master (open in Chrome → Print → Save as PDF) |
| [assets/letterhead-webstar.pdf](assets/letterhead-webstar.pdf) | Generated blank letterhead PDF (regenerate if brand changes) |
| Star mark | Repo `assets/images/star-icon-gold.png` (referenced relatively from the HTML) |

Brand tokens: [BRAND.md](BRAND.md).

## Design

- **Header:** gold star + “Webstar Business Services” + thin gold rule  
- **Footer:** LLC name, service area, phone, email, website  
- **Body:** blank for agreement or correspondence  

## Regenerate PDF (Windows)

1. Open `docs/client-ops/assets/letterhead-webstar.html` in Chrome.  
2. Confirm the star image loads (path is relative to repo `assets/images/`).  
3. **Ctrl+P** → Destination: **Save as PDF** → Paper: Letter → Margins: **None** → Background graphics: **On**.  
4. Save over `docs/client-ops/assets/letterhead-webstar.pdf`.

Or from repo root (if Chrome is installed):

```powershell
$chrome = "${env:ProgramFiles}\Google\Chrome\Application\chrome.exe"
$html = (Resolve-Path "docs\client-ops\assets\letterhead-webstar.html").Path
$pdf  = (Resolve-Path "docs\client-ops\assets").Path + "\letterhead-webstar.pdf"
& $chrome --headless --disable-gpu --print-to-pdf="$pdf" "file:///$($html -replace '\\','/')"
```

## Google Docs setup

1. Upload `letterhead-webstar.pdf` to Drive → `Webstar / Client ops / Templates / Letterhead/`.  
2. For editable agreements, either:  
   - **A.** Create a Doc with Insert → Image (header): export a cropped header PNG from the PDF/HTML screenshot, set **Header** + **Footer** text matching BRAND footer; or  
   - **B.** Write the agreement in a normal Doc, then **File → Download → PDF** and merge onto letterhead only if you need a pretty attachment (optional).  
3. Prefer eSignature on the **Doc** (Tools → eSignature) for most clients; use letterhead PDF when you want a polished email attachment.

## Margins (Docs)

Suggested body margins so content clears the visual header/footer:

- Top: ~1.1 in  
- Bottom: ~0.9 in  
- Left / right: 0.75 in  

## Do not

- Change NAP text without updating [BRAND.md](BRAND.md) and the site.  
- Stretch the star logo.  
- Commit signed client PDFs into git.
