# Odhiyaty - أضحيتي

### Overview
"Odhiyaty" is a professional e-commerce platform for selling sacrificial animals and sheep. It features a modern, responsive design and a robust backend for managing products, orders, and customer inquiries. The platform provides a seamless experience for customers browsing products.

### User Preferences
The user prefers clear and concise communication. They value an iterative development approach and want to be consulted before any major changes are implemented.

### System Architecture
The project utilizes a **separated Frontend/Backend architecture**:
-   **Frontend**: Built with static HTML5, CSS3, and Vanilla JavaScript for client-side rendering, communicating with the backend via REST APIs. All customer-facing pages are pure HTML files: `index.html`, `products.html`, `product-details.html`, and `contact.html`. These pages use JavaScript to fetch data from the API endpoints.
-   **Backend API**: Developed using PHP 8.2, providing REST API endpoints for data management (all files in `/api/` folder).

**Key Design Decisions & Features:**
-   **UI/UX**: Modern, luxurious design with a "Glassmorphism" aesthetic.
    -   **Color Scheme**: Primary gold (#C4A661), dark grey (#1F1F1F), warm background (#F8F5F0).
    -   **Visual Effects**: Glassmorphism, subtle gradients, golden glow effects, smooth animations, advanced hover effects on buttons, multi-layered golden-bordered product cards.
    -   **Responsiveness**: Fully responsive across all devices with dynamic text sizing and a flexible CSS grid.
-   **Technical Implementations**:
    -   **Client-Side Rendering**: All customer pages (`products.html`, `product-details.html`, `contact.html`) are static HTML files rendered client-side using JavaScript and Fetch API for data retrieval from PHP backend APIs.
    -   **Product Display**: Features advanced filtering by type, weight, and price.
    -   **Order System**: Customers can place orders for available animals with their contact information.
    -   **Security**: Comprehensive measures including PDO Prepared Statements for SQL Injection prevention, CSRF tokens for all forms and POST requests, `htmlspecialchars()` for XSS prevention, and input sanitization (`clean_input()`).
    -   **Database**: SQLite for development (easily convertible to MySQL/PostgreSQL due to PDO usage).
    -   **Image Placeholders**: Default `placeholder.svg` used for products.

**Database Schema:**
-   `products`: Animal details (title, type, weight, price, images, status).
-   `orders`: Customer orders.
-   `contacts`: Customer messages.
-   Relationship: `orders.product_id` → `products.id` (ON DELETE CASCADE).

**Product Statuses**: `available`, `reserved`, `sold`.
**Order Statuses**: `pending`, `confirmed`, `cancelled`.

### Recent Changes (Nov 22, 2025)
-   Added Firebase authentication system with Google OAuth support
-   Created API endpoint `/api/firebase-config.php` to read Firebase credentials from `.env` file
-   Updated `firebase-config.js` to dynamically load config from server
-   Fixed scrolling issue on authentication pages
-   Fixed logout function error handling

### Firebase Setup for cPanel/Production
When deploying to cPanel:
1. Add your Firebase credentials to `.env` file:
   ```
   FIREBASE_API_KEY=your_api_key
   FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
   FIREBASE_PROJECT_ID=your-project-id
   FIREBASE_STORAGE_BUCKET=your-project.appspot.com
   FIREBASE_MESSAGING_SENDER_ID=123456789
   FIREBASE_APP_ID=1:123456789:web:abc123
   ```
2. Make sure `.env` is in the root directory (same level as `index.html`)
3. The JavaScript automatically reads from `/api/firebase-config.php` endpoint
4. Never commit `.env` to Git (already in .gitignore)

### External Dependencies
-   **SQLite**: Database used in the Replit environment.
-   **cPanel Compatibility**: The codebase is designed for 100% compatibility with cPanel environments, utilizing `.htaccess` for Apache configurations.