-- BREWING CAFE ORDERING SYSTEM DATABASE


CREATE DATABASE IF NOT EXISTS cafe_ordering_system;

USE cafe_ordering_system;


-- TABLE 1: MENU ITEMS
-- Stores the drinks sold by the cafe

CREATE TABLE menu_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(6,2) NOT NULL,
    availability TINYINT(1) NOT NULL DEFAULT 1
);


-- TABLE 2: ORDERS
-- Stores the main details of each customer order

CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(80) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    order_type VARCHAR(20) NOT NULL,
    order_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    total DECIMAL(8,2) NOT NULL
);


-- TABLE 3: ORDER ITEMS
-- Stores each individual drink inside an order

CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    item_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(6,2) NOT NULL,

    FOREIGN KEY (order_id)
        REFERENCES orders(order_id)
        ON DELETE CASCADE,

    FOREIGN KEY (item_id)
        REFERENCES menu_items(item_id)
        ON DELETE RESTRICT
);


-- TABLE 4: ADMINS
-- Used for administrator authentication

CREATE TABLE admins (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL
);


-- MENU ITEMS

INSERT INTO menu_items
    (name, category, price, availability)
VALUES
    ('Espresso', 'Coffee', 5.00, 1),
    ('Caramel Macchiato', 'Coffee', 5.00, 1),
    ('Vanilla Latte', 'Coffee', 4.50, 1),
    ('Americano', 'Coffee', 4.00, 1),

    ('Strawberry Matcha', 'Non-Coffee', 5.50, 1),
    ('Strawberry Milk', 'Non-Coffee', 4.00, 1),
    ('Ice Chocolate Cream', 'Non-Coffee', 4.00, 1),
    ('Ice Lemonade Soda', 'Non-Coffee', 3.50, 1),

    ('Green Tea', 'Tea', 3.50, 1),
    ('Strawberry Tea', 'Tea', 3.00, 1),
    ('Milk Tea', 'Tea', 3.00, 1),
    ('Peach Tea', 'Tea', 3.00, 1);



-- INITIAL ADMIN ACCOUNT

INSERT INTO admins (username, password_hash)
VALUES (
    'cafe',
    '$2y$10$ICbzFwtRkUA7qX2Yb6ggLOxojAEjP1r5XNgkDJbGLOHN7/kEf1D7K'
);