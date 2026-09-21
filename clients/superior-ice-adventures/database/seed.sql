-- seed.sql - Admin user and placeholder article categories.
-- Default password: admin123 (change after first login)
USE superior_ice_adventures;

INSERT INTO users (username, password_hash, role) VALUES
('admin', '$2y$10$rYEVr/5Y0STL8EjRgaRcUO.VoVFyLK.EDsZlbnqRAh5wUwHIEvbri', 'admin');

-- Category names are structural placeholders — replace image paths when assets are ready.
-- Category display names left as TBD-style seeds you can rename in /admin.
INSERT INTO categories (type, slug, name, image_path, sort_order, is_featured) VALUES
('article', 'ice-fishing-tips', 'Ice Fishing Tips', '/assets/img/categories/ice-fishing-tips.webp', 1, 0),
('article', 'trip-reports', 'Trip Reports', '/assets/img/categories/trip-reports.webp', 2, 0),
('article', 'gear', 'Gear', '/assets/img/categories/gear.webp', 3, 0),
('article', 'stories', 'Stories', '/assets/img/categories/stories.webp', 4, 0);
