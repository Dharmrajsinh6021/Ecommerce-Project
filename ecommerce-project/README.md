# ElectroTech - Dynamic PHP & MySQL E-Commerce Platform

A complete, full-featured dynamic e-commerce web application developed for **Silver Oak University - College of Technology (B.Tech IT, Semester V)**.

## Project Structure

```
ecommerce-project/
├── config/
│   └── db.php                  # Database connection (PDO), constants & helper functions
├── includes/
│   ├── header.php              # Global navigation bar, search box, category dropdown, cart badge
│   └── footer.php              # Global footer with links, copyright, and payment badges
├── admin/                      # Backend Admin Panel
│   ├── includes/
│   │   ├── auth_check.php      # Admin route guard
│   │   ├── header.php          # Admin dark sidebar & topbar
│   │   └── footer.php          # Admin layout footer
│   ├── index.php               # Admin Dashboard with live KPI counters
│   ├── login.php               # Admin Portal Login (admin@electrotech.com / admin123)
│   ├── logout.php              # Admin Logout
│   ├── categories.php          # Category CRUD (Add, Edit, View, Delete)
│   ├── products.php            # Product CRUD (Inventory, Pricing, Discount, Specs)
│   ├── orders.php              # Order Summary & Status Update (Pending/Delivered/etc.)
│   └── users.php               # Customer Accounts Listing, Search, Status Toggle
├── database.sql                # Complete MySQL Schema & Initial Mock Data
├── index.php                   # 1. Home Page (Carousel, Featured, Categories, Offers)
├── categories.php              # 2. Category Listing Page
├── products.php                # 3. Product Listing & Filter/Sort/Search Page
├── product-detail.php          # 4. Product Detail Page (Specs, Stock, Add to Cart, Buy Now)
├── cart.php                    # 5. Shopping Cart Page (Session-based, Quantity +/-)
├── checkout.php                # 6. Checkout Page (Customer Info, Address, Summary)
├── payment.php                 # 7. Payment Simulation Page (COD, UPI QR, Card)
├── order-success.php           # 8. Order Success / Invoice Receipt Page
├── profile.php                 # 9. My Profile Page (Update profile & password)
├── orders.php                  # 10. Order History Page (Past orders & invoice breakdown)
├── about.php                   # 11. About Us Page (Vision, Mission, Story)
├── contact.php                 # 12. Contact Us Page (Form submissions saved to MySQL)
├── login.php                   # 13. Customer Login Page (Session auth)
├── register.php                # 14. Customer Register Page (password_hash)
└── logout.php                  # Customer Logout
```

## Default Login Credentials

### 1. Store Administrator
- **URL:** `http://localhost/ecommerce-project/admin/login.php`
- **Email:** `admin@electrotech.com`
- **Password:** `admin123`

### 2. Demo Customer
- **URL:** `http://localhost/ecommerce-project/login.php`
- **Email:** `customer@gmail.com`
- **Password:** `customer123`
*(Or click "Register Account" to create a new user profile)*
