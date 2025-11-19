<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الأضاحي - Odhiyaty</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css?v=20251118">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">
                <img src="/assets/images/logos/logo.png" alt="Odhiyaty Logo">
                <span>أضحيتي</span>
            </a>
            <ul class="navbar-menu">
                <li><a href="/">الرئيسية</a></li>
                <li><a href="/products.php" class="active">الأضاحي</a></li>
                <li><a href="/contact.php">تواصل معنا</a></li>
                <li><a href="/admin/login.php" style="background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-light) 100%); padding: 0.7rem 1.5rem; border-radius: 30px;">تسجيل الدخول</a></li>
            </ul>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <h2 class="section-title">تصفح الأضاحي</h2>
            
            <div class="filter-bar">
                <form id="filterForm">
                    <div class="filter-group">
                        <label>النوع:</label>
                        <select name="type" id="type">
                            <option value="">الكل</option>
                            <option value="محلي">محلي</option>
                            <option value="إسباني">إسباني</option>
                            <option value="روماني">روماني</option>
                        </select>
                        
                        <label>الوزن الأدنى (كجم):</label>
                        <input type="number" name="min_weight" id="min_weight" step="0.1" placeholder="مثال: 20">
                        
                        <label>الوزن الأقصى (كجم):</label>
                        <input type="number" name="max_weight" id="max_weight" step="0.1" placeholder="مثال: 50">
                        
                        <label>السعر الأدنى (DH):</label>
                        <input type="number" name="min_price" id="min_price" step="0.01" placeholder="مثال: 1000">
                        
                        <label>السعر الأقصى (DH):</label>
                        <input type="number" name="max_price" id="max_price" step="0.01" placeholder="مثال: 5000">
                        
                        <button type="submit" class="btn">بحث</button>
                        <button type="button" class="btn btn-secondary" id="resetBtn">إعادة تعيين</button>
                    </div>
                </form>
            </div>
            
            <div id="productsContainer">
                <div style="text-align: center; padding: 3rem;">
                    <p style="color: var(--secondary-gray); font-size: 1.2rem;">جاري التحميل...</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; <span id="currentYear"></span> أضحيتي - Odhiyaty. جميع الحقوق محفوظة.</p>
            <p>منصة احترافية لبيع الأضاحي والأغنام</p>
        </div>
    </footer>

    <script src="/assets/js/api.js"></script>
    <script>
        document.getElementById('currentYear').textContent = new Date().getFullYear();

        async function loadProducts(filters = {}) {
            const params = new URLSearchParams();
            
            Object.keys(filters).forEach(key => {
                if (filters[key]) {
                    params.append(key, filters[key]);
                }
            });
            
            const queryString = params.toString();
            const endpoint = `/api/products.php${queryString ? '?' + queryString : ''}`;
            
            const result = await apiRequest(endpoint);
            
            const container = document.getElementById('productsContainer');
            
            if (result.success && result.data && result.data.length > 0) {
                const productsHTML = result.data.map(product => {
                    let images = [];
                    try {
                        images = JSON.parse(product.images);
                    } catch (e) {
                        images = [];
                    }
                    
                    const firstImage = images.length > 0 
                        ? `/uploads/products/${images[0]}` 
                        : '/assets/images/placeholder.svg';
                    
                    const div = document.createElement('div');
                    div.className = 'product-card';
                    
                    const img = document.createElement('img');
                    img.src = firstImage;
                    img.alt = product.title;
                    img.className = 'product-image';
                    
                    div.innerHTML = `
                        <div class="product-body">
                            <span class="product-type">${escapeHtml(product.type)}</span>
                            <h3 class="product-title">${escapeHtml(product.title)}</h3>
                            <div class="product-details">
                                <p>الوزن: ${escapeHtml(product.weight)} كجم</p>
                            </div>
                            <div class="product-price">${formatPrice(product.price)}</div>
                            <span class="product-status status-available">متوفرة</span>
                            <br><br>
                            <a href="/product-details.php?id=${product.id}" class="btn">عرض التفاصيل</a>
                        </div>
                    `;
                    div.insertBefore(img, div.firstChild);
                    return div.outerHTML;
                }).join('');
                
                container.innerHTML = `<div class="products-grid">${productsHTML}</div>`;
            } else {
                container.innerHTML = '<p style="text-align: center; color: var(--secondary-gray); font-size: 1.2rem;">لا توجد أضاحي متوفرة بهذه المعايير</p>';
            }
        }
        
        document.getElementById('filterForm').addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const filters = {};
            
            formData.forEach((value, key) => {
                if (value.trim()) {
                    filters[key] = value;
                }
            });
            
            loadProducts(filters);
        });
        
        document.getElementById('resetBtn').addEventListener('click', () => {
            document.getElementById('filterForm').reset();
            loadProducts();
        });
        
        loadProducts();
    </script>
</body>
</html>
