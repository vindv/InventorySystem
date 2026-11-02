CREATE DATABASE IF NOT EXISTS inventory_sales
    CHARACTER SET utf8mb4;

USE inventory_sales;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    CONSTRAINT products_name_unique UNIQUE (name),
    CONSTRAINT products_price_check CHECK (price >= 0),
    CONSTRAINT products_stock_check CHECK (stock >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(20,2) NOT NULL DEFAULT 0.00,
    CONSTRAINT sales_total_check CHECK (total >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS sale_details (
    sale_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (sale_id, product_id),
    KEY product_id (product_id),
    CONSTRAINT sale_details_sale_fk FOREIGN KEY (sale_id) REFERENCES sales(id),
    CONSTRAINT sale_details_product_fk FOREIGN KEY (product_id) REFERENCES products(id),
    CONSTRAINT sale_details_quantity_check CHECK (quantity > 0),
    CONSTRAINT sale_details_unit_price_check CHECK (unit_price >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
