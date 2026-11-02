# Inventory and Sales System

A small learning project made with PHP and MySQL. It has no framework.

## Features

- Add products
- Restock products
- Register a sale and reduce stock
- Show the product list
- Use a database transaction when registering a sale

## Requirements

- PHP 8.1 or later
- Composer
- MySQL 8.4
- PHP extensions `pdo_mysql` and `mbstring`

## Run the project

1. Install the Composer autoloader:

   ```sh
   composer install
   ```

2. Create the database by running `schema.sql` in HeidiSQL or the MySQL client.
3. Copy `config/database.example.php` to `config/database.php` and enter your local MySQL settings in the copied file. The local file is ignored by Git.
4. Start the PHP server from the project folder:

   ```sh
   php -S 127.0.0.1:8000 -t public
   ```

5. Open <http://127.0.0.1:8000>.

## Folders

- `public/` contains the page users open and its CSS
- `src/` contains the controller, database connection, and repositories
- `views/` contains the HTML template
- `config/` contains the example and local database settings
- `schema.sql` creates the database and its tables

## License

MIT. See [LICENSE](LICENSE).
