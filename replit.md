# Odhiyaty - أضحيتي

### Overview
"Odhiyaty" is a professional e-commerce platform for selling sacrificial animals and sheep. It features a modern, responsive design and a robust backend for managing products, orders, and customer inquiries. The platform aims to provide a seamless experience for both customers browsing products and administrators managing the business.

### User Preferences
The user prefers clear and concise communication. They value an iterative development approach and want to be consulted before any major changes are implemented. The user appreciates detailed explanations of technical decisions and prefers a functional programming paradigm where applicable. They explicitly state that no changes should be made to the folder `Z` or the file `Y`.

### System Architecture
The project utilizes a **separated Frontend/Backend architecture**:
-   **Frontend**: Built with static HTML5, CSS3, and Vanilla JavaScript for client-side rendering, communicating with the backend via REST APIs. Key pages like `products.php`, `product-details.php`, and `contact.php` use JavaScript to fetch data. The `index.html` is a static HTML page with a luxurious design, featuring sample products, a hero section, and an embedded contact section.
-   **Backend API**: Developed using PHP 8.2, providing REST API endpoints for data management.
-   **Admin Panel**: A PHP-based control panel for administrators, featuring server-side rendering.

**Key Design Decisions & Features:**
-   **UI/UX**: Modern, luxurious design with a "Glassmorphism" aesthetic.
    -   **Color Scheme**: Primary gold (#C4A661), dark grey (#1F1F1F), warm background (#F8F5F0).
    -   **Visual Effects**: Glassmorphism, subtle gradients, golden glow effects, smooth animations, advanced hover effects on buttons, multi-layered golden-bordered product cards.
    -   **Responsiveness**: Fully responsive across all devices with dynamic text sizing and a flexible CSS grid.
-   **Technical Implementations**:
    -   **Client-Side Rendering**: `products.php`, `product-details.php`, `contact.php` are rendered client-side using JavaScript and Fetch API for data retrieval.
    -   **Product Management**: Features advanced filtering by type, weight, and price. Includes multi-image uploads for products.
    -   **Order System**: Ensures each animal can be ordered once, with protection against concurrent orders using `SELECT ... FOR UPDATE` to prevent double-booking. Orders can be confirmed (marking product as "sold") or cancelled (returning product to "available").
    -   **Admin Dashboard**: Provides real-time statistics, product management (add, edit, delete), order management, admin user management, and customer message viewing.
    -   **Security**: Comprehensive measures including PDO Prepared Statements for SQL Injection prevention, CSRF tokens for all forms and POST requests, `htmlspecialchars()` for XSS prevention, and input sanitization (`clean_input()`).
    -   **Database**: SQLite for development (easily convertible to MySQL/PostgreSQL due to PDO usage).
    -   **File Uploads**: Product images are uploaded to `uploads/products/`, with automatic renaming and support for JPG, PNG, WEBP up to 5MB per image.
    -   **Image Placeholders**: Default `placeholder.svg` used for products.

**Database Schema:**
-   `admins`: Administrator information and permissions.
-   `products`: Animal details (title, type, weight, price, images, status).
-   `orders`: Customer orders.
-   `contacts`: Customer messages.
-   Relationship: `orders.product_id` → `products.id` (ON DELETE CASCADE).

**Product Statuses**: `available`, `reserved`, `sold`.
**Order Statuses**: `pending`, `confirmed`, `cancelled`.

### External Dependencies
-   **Firebase Authentication**: Used for Google Sign-In in the Admin Panel, requiring `FIREBASE_API_KEY`, `FIREBASE_AUTH_DOMAIN`, `FIREBASE_PROJECT_ID`, etc., stored securely in Replit Secrets.
-   **SQLite**: Database used in the Replit environment.
-   **cPanel Compatibility**: The codebase is designed for 100% compatibility with cPanel environments, utilizing `.htaccess` for Apache configurations.