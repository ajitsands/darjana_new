# Dar Jana Fashion - PHP MySQL MVC Application

Luxury E-Shopping Website for Abayas, Couture Dresses, Sets, and Blazers. Built with PHP, MySQL/SQLite, and Custom MVC Architecture.

---

## 🚀 How to Run the Application

### Option 1: Built-in PHP Development Server (Quickest - Zero Config)

1. Open your terminal / command prompt.
2. Navigate to the project root directory:
   ```bash
   cd e:\darjanafashon_new
   ```
3. Run the built-in PHP development server targeting the `public` directory:
   ```bash
   php -S localhost:8000 -t public
   ```
4. Open your web browser and visit:
   - **Storefront**: [http://localhost:8000](http://localhost:8000)
   - **Admin Panel**: [http://localhost:8000/admin](http://localhost:8000/admin)

*(Note: On first run, SQLite fallback database `database/darjanafashon.sqlite` is automatically generated and populated with sample luxury dresses and categories.)*

---

### Option 2: Apache / XAMPP / WAMP Setup

1. Copy or move the project folder `darjanafashon_new` into your web server document root:
   - XAMPP: `C:\xampp\htdocs\darjanafashon_new`
   - WAMP: `C:\wamp64\www\darjanafashon_new`
2. Start **Apache** and **MySQL** services in XAMPP / WAMP Control Panel.
3. Import the database schema into phpMyAdmin or MySQL CLI:
   - Create a database named `darjanafashon`.
   - Import the file [`database/schema.sql`](file:///e:/darjanafashon_new/database/schema.sql).
4. Update database credentials in [`config/database.php`](file:///e:/darjanafashon_new/config/database.php):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'darjanafashon');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('BASE_URL', 'http://localhost/darjanafashon_new/public');
   ```
5. Ensure `mod_rewrite` is enabled in Apache.
6. Open your browser and visit:
   - [http://localhost/darjanafashon_new/public](http://localhost/darjanafashon_new/public)
   - [http://localhost/darjanafashon_new/public/admin](http://localhost/darjanafashon_new/public/admin)

---

## 🗺️ Complete Routing Table

All incoming requests pass through the Front Controller [`public/index.php`](file:///e:/darjanafashon_new/public/index.php) and are routed via [`core/Router.php`](file:///e:/darjanafashon_new/core/Router.php).

| HTTP Method | Route URL Pattern | Target Controller & Action | Description / Purpose |
| :--- | :--- | :--- | :--- |
| `GET` | `/` | `HomeController@index` | Storefront Home page (Hero slider, Category cards, Featured collection) |
| `GET` | `/collections/{slug}` | `ProductController@index` | Category product listing (e.g. `/collections/black-abaya`, `/collections/set`) |
| `GET` | `/product/{slug}` | `ProductController@detail` | Product detail page with size chips, gallery, and Add to Cart / Buy Now |
| `GET` | `/search` | `ProductController@search` | Search results page & live AJAX search API |
| `GET` | `/cart` | `CartController@index` | Full shopping cart page view |
| `POST` | `/cart/add` | `CartController@add` | AJAX / Form handler to add product to cart (or Buy Now redirect) |
| `POST` | `/cart/update` | `CartController@update` | AJAX endpoint to update item quantity in cart drawer |
| `POST` | `/cart/remove` | `CartController@remove` | AJAX endpoint to remove item from cart |
| `GET` | `/cart/get-json` | `CartController@getJson` | AJAX endpoint returning current cart JSON data for slide-over drawer |
| `GET` | `/checkout` | `CheckoutController@index` | Express checkout page with customer address form & order summary |
| `POST` | `/checkout/process` | `CheckoutController@process` | AJAX form handler to save order to database and clear cart session |
| `POST` | `/newsletter/subscribe` | `HomeController@subscribe` | AJAX newsletter email subscription handler |
| `GET` | `/admin` | `AdminController@index` | Admin Dashboard (Sales metrics, customer order list, publish product form) |
| `POST` | `/admin/product/add` | `AdminController@addProduct` | Form handler to create and publish a new dress/abaya product |
| `GET` | `/admin/order/delete/{id}` | `AdminController@deleteOrder` | Deletes a customer order from database |

---

## 🛠️ Key File Locations

- **Front Controller**: [`public/index.php`](file:///e:/darjanafashon_new/public/index.php)
- **URL Router**: [`core/Router.php`](file:///e:/darjanafashon_new/core/Router.php)
- **Database Config**: [`config/database.php`](file:///e:/darjanafashon_new/config/database.php)
- **Database Schema & Seeds**: [`database/schema.sql`](file:///e:/darjanafashon_new/database/schema.sql)
- **CSS Tokens & Styles**: [`public/assets/css/style.css`](file:///e:/darjanafashon_new/public/assets/css/style.css)
- **JavaScript & AJAX Cart Drawer**: [`public/assets/js/main.js`](file:///e:/darjanafashon_new/public/assets/js/main.js)
