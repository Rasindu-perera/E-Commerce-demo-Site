# KWRmart - Premium E-Commerce Demo Site

Welcome to **KWRmart**, a premium, beautifully designed e-commerce storefront demonstration!

This project is built using native **PHP** and **MySQL**, utilizing a modern **Front Controller pattern** with URL routing and a sleek **Tailwind CSS** user interface. It features glassmorphism aesthetics, dynamic product rendering, a fully functional shopping cart, and an intuitive Admin Panel.

> **Note:** This website is strictly for demonstration purposes. It does not include any real payment processing or delivery management systems.

---

## 🌟 Features

- **Modern Architecture**: Uses a single `index.php` Front Controller pattern with clean URL routing (`.htaccess`).
- **Premium UI**: Hand-crafted using Tailwind CSS featuring dark mode elements, glassmorphism (`backdrop-blur`), and smooth micro-animations.
- **Product Management**: Dynamic loading of products, categories, dynamic pricing, and special discount offers.
- **Reviews & Ratings**: Users can leave reviews and star ratings which dynamically calculate and update across the store.
- **Admin Dashboard**: Full CRUD (Create, Read, Update, Delete) capability for products, order management, and viewing revenue analytics.
- **Secure Authentication**: Includes secure login and registration flows with password hashing.

---

## 🔑 Demo Access

You can explore both the customer storefront and the secure admin dashboard. 

### Admin Login Credentials
To access the Admin Dashboard, navigate to the `/login` page and use the following credentials:

- **Email**: `admin@gmail.com`
- **Password**: `admin1234`

> ⚠️ **IMPORTANT:** Please do not change the admin password, as this is a shared demo environment!

---

## 🚀 Setup & Installation (Local Development)

To run this project on your local machine, follow these steps:

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/E-Commerce-demo-Site.git
   cd E-Commerce-demo-Site
   ```

2. **Configure Database**
   - Create a MySQL database (e.g., `ecommerce_db`).
   - Open `/config/db.php` and update the database credentials if necessary (default uses `root` and no password for XAMPP/WAMP).
   
3. **Run Migrations**
   - Execute the `setup_db.php` script. This single file will automatically create all the necessary database tables (including reviews) and seed the initial dummy data.

4. **Serve the Application**
   - If you are using Apache (XAMPP/WAMP), place the project folder in your `htdocs` or `www` directory and ensure `mod_rewrite` is enabled.
   - Alternatively, you can use PHP's built-in server (Note: `.htaccess` routing requires a routing script or Apache):
     ```bash
     php -S localhost:8000
     ```

5. **Access the Site**
   - Open your browser and navigate to `http://localhost/E-Commerce-demo-Site` (or your configured local host).

---

## 📂 Project Structure

```
E-Commerce-demo-Site/
│
├── index.php             # Front Controller (Handles routing for clean URLs)
├── .htaccess             # Apache URL rewriting rules
├── config/               # Database configuration and connection
├── views/
│   └── pages/            # Frontend pages (Home, Products, Login, Cart, etc.)
├── admin/                # Secure Admin Dashboard & Management scripts
├── api/                  # Backend endpoints (Add to Cart, Save Product, Reviews)
├── public/               # Static Assets
│   ├── css/              # Custom Stylesheets
│   ├── js/               # Frontend JavaScript logic (Cart, Tailwind config)
│   └── images/           # Uploaded product images
└── README.md             # Project documentation
```

---

## 🛠 Technologies Used
- **Backend:** PHP 8+
- **Database:** MySQL (PDO)
- **Frontend Framework:** Tailwind CSS via CDN
- **Icons:** FontAwesome 6

---

## 📄 License
This project is licensed under the **MIT License**. See the `LICENSE` file for details.

Enjoy exploring KWRmart!
