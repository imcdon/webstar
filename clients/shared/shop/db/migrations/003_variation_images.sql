-- Per-variation product photos (e.g. jig Color → local file / Square URL).
ALTER TABLE variations ADD COLUMN local_image TEXT;
ALTER TABLE variations ADD COLUMN image_url TEXT;
