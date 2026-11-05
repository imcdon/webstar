-- Contentless FTS5 (content='') cannot return product_id in SELECTs, which broke search.
-- Recreate as a regular FTS5 table; Catalog::rebuildFts() repopulates it on next sync/seed.
DROP TABLE IF EXISTS products_fts;
CREATE VIRTUAL TABLE products_fts USING fts5(
    product_id UNINDEXED,
    name,
    description,
    category
);
