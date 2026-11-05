-- Product option groups (e.g. Size, Color) and per-variation option values.
ALTER TABLE products ADD COLUMN options_json TEXT NOT NULL DEFAULT '[]';
ALTER TABLE variations ADD COLUMN option_values_json TEXT NOT NULL DEFAULT '{}';
