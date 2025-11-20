<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الأضحية - Odhiyaty</title>
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
                <li><a href="/products.php">الأضاحي</a></li>
                <li><a href="/contact.php">تواصل معنا</a></li>
                <li><a href="/admin/login.php" style="background: linear-gradient(135deg, var(--primary-gold) 0%, var(--gold-light) 100%); padding: 0.7rem 1.5rem; border-radius: 30px;">تسجيل الدخول</a></li>
            </ul>
        </div>
    </nav>

    <section class="section">
        <div class="container">
            <div id="productContainer" style="max-width: 900px; margin: 0 auto;">
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

        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');

        if (!productId) {
            window.location.href = '/products.php';
        }

        async function loadProductDetails() {
            const result = await apiRequest(`/api/product-details.php?id=${productId}`);
            
            const container = document.getElementById('productContainer');
            
            if (!result.success || !result.data) {
                container.innerHTML = `
                    <div class="alert alert-error">المنتج غير موجود</div>
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="/products.php" class="btn btn-secondary">العودة لقائمة الأضاحي</a>
                    </div>
                `;
                return;
            }
            
            const product = result.data;
            let images = [];
            try {
                images = JSON.parse(product.images);
            } catch (e) {
                images = [];
            }
            
            let galleryHTML = '';
            if (images.length > 0) {
                galleryHTML = `
                    <div class="product-gallery">
                        ${images.map(img => `<img src="/uploads/products/${img}" alt="${product.title}">`).join('')}
                    </div>
                `;
            }
            
            const notesRow = product.notes ? `
                <tr>
                    <td style="padding: 1rem; font-weight: bold;">ملاحظات:</td>
                    <td style="padding: 1rem;">${product.notes.replace(/\n/g, '<br>')}</td>
                </tr>
            ` : '';
            
            const orderFormHTML = product.status === 'available' ? `
                <div class="contact-form">
                    <h3 style="text-align: center; margin-bottom: 1.5rem;">اطلب هذه الأضحية</h3>
                    
                    <div id="orderMessage"></div>
                    
                    <form id="orderForm">
                        <div class="form-group">
                            <label>الاسم الكامل *</label>
                            <input type="text" name="customer_name" id="customer_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label>رقم الهاتف *</label>
                            <input type="tel" name="customer_phone" id="customer_phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label>البريد الإلكتروني</label>
                            <input type="email" name="customer_email" id="customer_email">
                        </div>
                        
                        <div class="form-group">
                            <label>العنوان</label>
                            <textarea name="customer_address" id="customer_address" rows="3"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>ملاحظات إضافية</label>
                            <textarea name="notes" id="notes" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn" style="width: 100%;">إرسال الطلب</button>
                    </form>
                </div>
            ` : `
                <div class="alert alert-warning">
                    <strong>عذراً!</strong> هذه الأضحية غير متاحة للطلب حالياً.
                </div>
            `;
            
            container.innerHTML = `
                <h2 class="section-title">${product.title}</h2>
                
                <span class="product-type" style="font-size: 1.1rem; padding: 0.5rem 1.5rem;">${product.type}</span>
                ${getProductStatusBadge(product.status)}
                
                ${galleryHTML}
                
                <div style="background: white; padding: 2rem; border-radius: 10px; margin: 2rem 0;">
                    <h3 style="color: var(--dark-gray); margin-bottom: 1rem;">تفاصيل الأضحية</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr style="border-bottom: 1px solid var(--light-gray);">
                            <td style="padding: 1rem; font-weight: bold;">الوزن:</td>
                            <td style="padding: 1rem;">${product.weight} كجم</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--light-gray);">
                            <td style="padding: 1rem; font-weight: bold;">النوع:</td>
                            <td style="padding: 1rem;">${product.type}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--light-gray);">
                            <td style="padding: 1rem; font-weight: bold;">السعر:</td>
                            <td style="padding: 1rem; color: var(--primary-gold); font-size: 1.5rem; font-weight: bold;">${formatPrice(product.price)}</td>
                        </tr>
                        <tr style="border-bottom: 1px solid var(--light-gray);">
                            <td style="padding: 1rem; font-weight: bold;">الحالة:</td>
                            <td style="padding: 1rem;">${getProductStatusBadge(product.status)}</td>
                        </tr>
                        ${notesRow}
                    </table>
                </div>
                
                ${orderFormHTML}
                
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="/products.php" class="btn btn-secondary">العودة لقائمة الأضاحي</a>
                </div>
            `;
            
            if (product.status === 'available') {
                setupOrderForm();
            }
        }
        
        async function setupOrderForm() {
            const form = document.getElementById('orderForm');
            
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                const formData = new FormData(form);
                const orderData = {
                    product_id: productId,
                    customer_name: formData.get('customer_name'),
                    customer_phone: formData.get('customer_phone'),
                    customer_email: formData.get('customer_email'),
                    customer_address: formData.get('customer_address'),
                    notes: formData.get('notes'),
                    csrf_token: await getCSRFToken()
                };
                
                const messageDiv = document.getElementById('orderMessage');
                
                const result = await apiRequest('/api/order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });
                
                if (result.success) {
                    messageDiv.innerHTML = `<div class="alert alert-success">${result.message}</div>`;
                    form.reset();
                    setTimeout(() => {
                        loadProductDetails();
                    }, 2000);
                } else {
                    messageDiv.innerHTML = `<div class="alert alert-error">${result.message}</div>`;
                }
            });
        }
        
        loadProductDetails();
    </script>
</body>
</html>
