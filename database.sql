-- Golden Crust Bakery — database schema + seed data
-- Import with: mysql -u root golden_crust_bakery < database.sql

CREATE DATABASE IF NOT EXISTS golden_crust_bakery
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE golden_crust_bakery;

-- ---------------------------------------------------------------
-- Tables
-- ---------------------------------------------------------------

CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    display_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    image_path VARCHAR(255),
    is_available TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    role VARCHAR(150) NOT NULL,
    bio TEXT,
    photo_path VARCHAR(255),
    display_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(30),
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------
-- Seed data
-- ---------------------------------------------------------------

-- Default admin login: username "admin", password "bakery123"
-- (hash generated with PHP password_hash('bakery123', PASSWORD_DEFAULT))
INSERT INTO admins (username, password_hash) VALUES
('admin', '$2y$12$sKK4D4fjpPdgMy0/o.x3r.82FRWslYCK0fB/phbW/r/n3XlCg6M8e');

INSERT INTO categories (name, display_order) VALUES
('Breads', 1),
('Cakes', 2),
('Pastries', 3),
('Cookies & Sweets', 4);

INSERT INTO products (category_id, name, description, price, image_path, is_available) VALUES
(1, 'Classic Sourdough Loaf', 'Naturally leavened sourdough with a crisp crust and tangy crumb, baked fresh every morning.', 6.50, 'assets/uploads/products/sourdough.svg', 1),
(1, 'Whole Wheat Bread', 'Hearty stone-ground whole wheat loaf, soft inside with a wholesome nutty flavor.', 5.00, 'assets/uploads/products/wholewheat.svg', 1),
(1, 'French Baguette', 'Traditional thin baguette with a golden, crackling crust.', 4.00, 'assets/uploads/products/baguette.svg', 1),
(2, 'Chocolate Fudge Cake', 'Rich layered chocolate cake with silky fudge frosting.', 28.00, 'assets/uploads/products/chocolate-cake.svg', 1),
(2, 'Red Velvet Cake', 'Classic red velvet with cream cheese frosting, baked to order.', 30.00, 'assets/uploads/products/red-velvet.svg', 1),
(2, 'Carrot Walnut Cake', 'Moist carrot cake studded with walnuts and warm spices.', 26.00, 'assets/uploads/products/carrot-cake.svg', 1),
(3, 'Butter Croissant', 'Flaky, buttery, laminated dough croissant baked fresh daily.', 3.50, 'assets/uploads/products/croissant.svg', 1),
(3, 'Cinnamon Roll', 'Soft roll swirled with cinnamon sugar, topped with cream cheese glaze.', 4.50, 'assets/uploads/products/cinnamon-roll.svg', 1),
(4, 'Chocolate Chip Cookies', 'Classic chewy cookies loaded with chocolate chips.', 2.50, 'assets/uploads/products/choc-chip-cookies.svg', 1),
(4, 'Almond Macarons', 'Delicate almond meringue shells with a smooth ganache filling.', 3.00, 'assets/uploads/products/macarons.svg', 1);

INSERT INTO team_members (name, role, bio, photo_path, display_order) VALUES
('Amara Khan', 'Founder & Head Baker', 'Amara opened Golden Crust Bakery in 2015 after years of training in artisan bread-making. She still shapes the sourdough by hand every morning.', 'assets/uploads/team/amara.svg', 1),
('Daniyal Farooq', 'Pastry Chef', 'Daniyal leads our pastry kitchen, bringing classic French technique to every croissant and Danish we bake.', 'assets/uploads/team/daniyal.svg', 2),
('Sara Malik', 'Cake Decorator', 'Sara designs and decorates every custom cake, turning simple sponge into edible art.', 'assets/uploads/team/sara.svg', 3),
('Bilal Ahmed', 'Store Manager', 'Bilal keeps the bakery running smoothly and makes sure every customer leaves with a smile.', 'assets/uploads/team/bilal.svg', 4);

INSERT INTO gallery (image_path, caption) VALUES
('assets/uploads/gallery/interior.svg', 'Our cozy bakery storefront'),
('assets/uploads/gallery/display-case.svg', 'Fresh pastries in our display case'),
('assets/uploads/gallery/baking.svg', 'Morning bread baking in progress'),
('assets/uploads/gallery/cake-order.svg', 'A custom celebration cake');
