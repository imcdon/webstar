-- seed-cpanel.sql - Same seed as seed.sql without USE (select DB in phpMyAdmin first).
-- Default password: admin123 (change after first login)

INSERT INTO users (username, password_hash, role) VALUES
('admin', '$2y$10$rYEVr/5Y0STL8EjRgaRcUO.VoVFyLK.EDsZlbnqRAh5wUwHIEvbri', 'admin');

INSERT INTO categories (type, slug, name, image_path, sort_order, is_featured) VALUES
('article', 'ice-fishing-tips', 'Ice Fishing Tips', '/assets/img/categories/ice-fishing-tips.webp', 1, 0),
('article', 'trip-reports', 'Trip Reports', '/assets/img/categories/trip-reports.webp', 2, 0),
('article', 'gear', 'Gear', '/assets/img/categories/gear.webp', 3, 0),
('article', 'stories', 'Stories', '/assets/img/categories/stories.webp', 4, 0);
