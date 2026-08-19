# 🚗 BuyCars — Car Buy & Sell Marketplace

A complete PHP + MySQL car marketplace web application.  
Built with **HTML5 · CSS3 · PHP 8 · MySQL · Swiper.js · AOS.js · Boxicons**

---

## ✨ Features

### User Side
| Feature | Details |
|---|---|
| Browse & Search | Filter by brand, fuel, transmission, max price; sort by date / price / views |
| Car Details | Full photo gallery with thumbnail preview, specs, seller info |
| Wishlist | Save favourite cars with AJAX toggle (no page reload) |
| Sell a Car | Multi-photo upload form, pending admin approval |
| Inquiry System | Message sellers directly from the car detail page |
| Dashboard | Stats overview — listings, inquiries, wishlist count |
| My Listings | View, edit, delete your own car posts |
| Profile | Update name / phone / change password |
| Reviews | Homepage testimonials pulled from the database |
| Newsletter | Email subscription form |
| Contact Form | General enquiry form with admin inbox |

### Admin Panel (`/admin/`)
| Feature | Details |
|---|---|
| Dashboard | Stats: total cars, pending, users, inquiries, live inventory value + bar chart |
| Manage Cars | Approve / Reject / Mark as Sold / Delete; filter by status; search |
| Manage Users | Block / Unblock / Delete users |
| View Inquiries | All buyer-seller messages |
| Contact Messages | Messages from the Contact Us form; auto-marks as read |
| Testimonials | Add / delete homepage reviews |

### Security
- Password hashing with `password_hash()` (bcrypt)
- CSRF tokens on every form
- Prepared statements (PDO) — no SQL injection
- Login required for Sell, Dashboard, Wishlist, Inquiries etc.
- Admin middleware (`require_admin()`) protects all `/admin/` pages
- Uploaded images validated by extension + MIME size (max 5 MB)

---

## 📁 Project Structure

```
car_marketplace/
├── index.php               ← Homepage
├── cars.php                ← Browse / search cars
├── car-details.php         ← Single car page
├── login.php               ← User login
├── register.php            ← User registration
├── logout.php
├── sell-car.php            ← Create listing
├── edit-car.php            ← Edit listing
├── delete-car.php          ← Delete listing
├── dashboard.php           ← User dashboard
├── my-listings.php         ← User's own listings
├── wishlist.php            ← Saved cars
├── inquiries.php           ← Sent & received messages
├── profile.php             ← Edit profile / change password
├── send-inquiry.php        ← Handle inquiry POST
├── toggle-wishlist.php     ← AJAX wishlist toggle
├── contact-submit.php      ← Handle contact form
├── newsletter-submit.php   ← Handle newsletter form
│
├── config/
│   ├── db.php              ← PDO connection (edit credentials here)
│   └── config.php          ← Session start, constants, includes
│
├── includes/
│   ├── functions.php       ← Helper functions
│   ├── header.php          ← Shared navbar
│   └── footer.php          ← Shared footer + scripts
│
├── assets/
│   ├── css/
│   │   ├── style.css       ← Original base theme (swiper, sections)
│   │   └── app.css         ← Extended UI (cards, modals, auth, admin)
│   ├── js/
│   │   ├── script.js       ← Original Swiper + header scroll
│   │   └── app.js          ← AOS, wishlist AJAX, toasts, image preview
│   └── image/              ← Original prototype images
│
├── uploads/
│   └── cars/               ← User-uploaded car photos (auto-created)
│
├── admin/
│   ├── index.php           ← Admin dashboard
│   ├── login.php           ← Admin login
│   ├── logout.php
│   ├── cars.php            ← Manage car listings
│   ├── car-action.php      ← Approve / reject / delete car
│   ├── users.php           ← Manage users
│   ├── user-action.php     ← Block / delete user
│   ├── inquiries.php       ← View all inquiries
│   ├── messages.php        ← Contact messages
│   ├── reviews.php         ← Manage testimonials
│   ├── assets/
│   │   ├── admin.css
│   │   └── admin.js
│   └── includes/
│       ├── header.php
│       ├── sidebar.php
│       └── footer.php
│
└── database/
    ├── car_marketplace.sql ← ← ← IMPORT THIS FIRST
    └── seed_passwords.php  ← Run once after import to set demo passwords
```

---

## 🛠 Installation (XAMPP / WAMP)

### Step 1 — Copy the project
Copy the entire `car_marketplace/` folder into:
- **XAMPP** → `C:\xampp\htdocs\car_marketplace\`
- **WAMP**  → `C:\wamp64\www\car_marketplace\`

### Step 2 — Import the database
1. Start **Apache** and **MySQL** in XAMPP/WAMP Control Panel.
2. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
3. Click **New** and create a database named `car_marketplace`.
4. Select that database → click the **Import** tab.
5. Click **Choose File** → select `database/car_marketplace.sql` → click **Go**.

### Step 3 — Configure database credentials (if needed)
Open `config/db.php` and update if your MySQL settings differ:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'car_marketplace');
define('DB_USER', 'root');
define('DB_PASS', '');   // default XAMPP has no password
```

### Step 4 — Seed demo passwords
Open your browser and go to:
```
http://localhost/car_marketplace/database/seed_passwords.php
```
This sets correct bcrypt hashes for the demo accounts.  
**Delete this file after running it.**

### Step 5 — Open the site
| URL | What it opens |
|---|---|
| `http://localhost/car_marketplace/` | Homepage (user site) |
| `http://localhost/car_marketplace/admin/login.php` | Admin panel |

---

## 🔑 Default Login Credentials

| Role | Email | Password |
|---|---|---|
| **Admin** | admin@buycars.com | Admin@123 |
| Demo User 1 | sagar@example.com | User@123 |
| Demo User 2 | nisha@example.com | User@123 |

> Passwords are only valid **after** running `database/seed_passwords.php` (Step 4 above).

---

## 🗃 Database Tables

| Table | Purpose |
|---|---|
| `users` | Stores both regular users and admins |
| `cars` | Car listings with status (pending / approved / rejected / sold) |
| `car_images` | Multiple images per listing |
| `wishlist` | User's saved / favourite cars |
| `inquiries` | Messages between buyers and sellers |
| `contact_messages` | General contact form submissions |
| `newsletter_subscribers` | Email subscriptions |
| `reviews` | Homepage testimonials |

---

## 🎨 UI Libraries Used

| Library | Purpose |
|---|---|
| Google Fonts — Poppins | Typography |
| Font Awesome 5 | Icons |
| Boxicons | Extended icon set |
| Swiper.js 7 | Touch sliders (vehicles, featured, reviews) |
| AOS.js | Scroll animations |

---

## ⚙️ PHP Requirements

- PHP **7.4+** (PHP 8.x recommended)
- **PDO** + **PDO_MySQL** extension (enabled by default in XAMPP)
- **GD** library (for image validation, usually enabled)
- `upload_max_filesize` ≥ 5M in `php.ini`

---

## 📝 Notes

- The `uploads/cars/` folder is created automatically on first upload.
- Car listings submitted by users start with `status = 'pending'` and only go live after admin approval.
- Editing a live listing resets it to `pending` so the admin can re-review changes.
- The admin panel is at `/admin/` — protected by the `require_admin()` middleware.

---

*Built for educational / project submission purposes.*
