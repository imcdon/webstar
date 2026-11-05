PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS schema_migrations (
    id TEXT PRIMARY KEY,
    applied_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS categories (
    id TEXT PRIMARY KEY,
    square_id TEXT UNIQUE,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    sort_order INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS products (
    id TEXT PRIMARY KEY,
    square_id TEXT UNIQUE,
    category_id TEXT REFERENCES categories(id) ON DELETE SET NULL,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT NOT NULL DEFAULT '',
    description_html TEXT NOT NULL DEFAULT '',
    image_url TEXT,
    local_image TEXT,
    is_active INTEGER NOT NULL DEFAULT 1,
    sort_order INTEGER NOT NULL DEFAULT 0,
    synced_at TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS variations (
    id TEXT PRIMARY KEY,
    product_id TEXT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    square_id TEXT UNIQUE,
    name TEXT NOT NULL DEFAULT 'Default',
    sku TEXT,
    price_cents INTEGER NOT NULL DEFAULT 0,
    track_inventory INTEGER NOT NULL DEFAULT 1,
    quantity INTEGER NOT NULL DEFAULT 0,
    weight_label TEXT,
    is_active INTEGER NOT NULL DEFAULT 1,
    sort_order INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS product_images (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id TEXT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    square_id TEXT,
    url TEXT NOT NULL,
    local_path TEXT,
    sort_order INTEGER NOT NULL DEFAULT 0
);

CREATE VIRTUAL TABLE IF NOT EXISTS products_fts USING fts5(
    product_id UNINDEXED,
    name,
    description,
    category,
    content=''
);

CREATE TABLE IF NOT EXISTS carts (
    id TEXT PRIMARY KEY,
    session_id TEXT,
    account_id TEXT,
    email TEXT,
    items_json TEXT NOT NULL DEFAULT '[]',
    shipping_method TEXT NOT NULL DEFAULT 'flat',
    restore_token TEXT UNIQUE,
    payment_link_id TEXT,
    payment_link_url TEXT,
    square_order_id TEXT,
    status TEXT NOT NULL DEFAULT 'open',
    reminder_2h_sent_at TEXT,
    reminder_24h_sent_at TEXT,
    unsubscribed_at TEXT,
    converted_order_id TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE INDEX IF NOT EXISTS idx_carts_session ON carts(session_id);
CREATE INDEX IF NOT EXISTS idx_carts_status ON carts(status);

CREATE TABLE IF NOT EXISTS orders (
    id TEXT PRIMARY KEY,
    square_order_id TEXT UNIQUE,
    square_payment_id TEXT,
    order_number TEXT NOT NULL UNIQUE,
    cart_id TEXT,
    account_id TEXT,
    email TEXT NOT NULL,
    phone TEXT,
    status TEXT NOT NULL DEFAULT 'pending',
    currency TEXT NOT NULL DEFAULT 'USD',
    subtotal_cents INTEGER NOT NULL DEFAULT 0,
    shipping_cents INTEGER NOT NULL DEFAULT 0,
    tax_cents INTEGER NOT NULL DEFAULT 0,
    discount_cents INTEGER NOT NULL DEFAULT 0,
    total_cents INTEGER NOT NULL DEFAULT 0,
    shipping_method TEXT,
    shipping_name TEXT,
    shipping_address_json TEXT,
    buyer_json TEXT,
    raw_json TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id TEXT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id TEXT,
    variation_id TEXT,
    square_variation_id TEXT,
    name TEXT NOT NULL,
    variation_name TEXT,
    quantity INTEGER NOT NULL,
    unit_price_cents INTEGER NOT NULL DEFAULT 0,
    line_total_cents INTEGER NOT NULL DEFAULT 0
);

CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id TEXT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    account_id TEXT,
    author_name TEXT NOT NULL,
    author_email TEXT,
    rating INTEGER NOT NULL CHECK (rating BETWEEN 1 AND 5),
    body TEXT NOT NULL,
    is_verified INTEGER NOT NULL DEFAULT 0,
    is_approved INTEGER NOT NULL DEFAULT 0,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE INDEX IF NOT EXISTS idx_reviews_product ON reviews(product_id, is_approved);

CREATE TABLE IF NOT EXISTS accounts (
    id TEXT PRIMARY KEY,
    email TEXT NOT NULL UNIQUE COLLATE NOCASE,
    name TEXT,
    phone TEXT,
    shipping_address_json TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    updated_at TEXT NOT NULL DEFAULT (datetime('now')),
    last_login_at TEXT
);

CREATE TABLE IF NOT EXISTS login_tokens (
    id TEXT PRIMARY KEY,
    account_id TEXT NOT NULL REFERENCES accounts(id) ON DELETE CASCADE,
    token_hash TEXT NOT NULL UNIQUE,
    expires_at TEXT NOT NULL,
    used_at TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS wishlists (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    account_id TEXT,
    session_id TEXT,
    product_id TEXT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    variation_id TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    UNIQUE(account_id, product_id, variation_id),
    UNIQUE(session_id, product_id, variation_id)
);

CREATE TABLE IF NOT EXISTS stock_alerts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    variation_id TEXT NOT NULL REFERENCES variations(id) ON DELETE CASCADE,
    email TEXT NOT NULL COLLATE NOCASE,
    notified_at TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now')),
    UNIQUE(variation_id, email)
);

CREATE TABLE IF NOT EXISTS webhook_events (
    id TEXT PRIMARY KEY,
    event_type TEXT NOT NULL,
    merchant_id TEXT,
    payload_json TEXT NOT NULL,
    processed_at TEXT,
    error TEXT,
    created_at TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS sync_meta (
    key TEXT PRIMARY KEY,
    value TEXT NOT NULL,
    updated_at TEXT NOT NULL DEFAULT (datetime('now'))
);
