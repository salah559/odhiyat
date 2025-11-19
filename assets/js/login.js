
// معالجة نموذج تسجيل الدخول التقليدي
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            const errorDiv = document.getElementById('error-message');
            const successDiv = document.getElementById('success-message');
            
            // إخفاء الرسائل السابقة
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';
            
            // التحقق من البيانات
            if (!email || !password) {
                errorDiv.textContent = 'الرجاء ملء جميع الحقول';
                errorDiv.style.display = 'block';
                return;
            }
            
            try {
                // إرسال البيانات إلى API للتحقق
                const response = await fetch('/api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    successDiv.textContent = 'تم تسجيل الدخول بنجاح';
                    successDiv.style.display = 'block';
                    
                    // إعادة التوجيه إلى لوحة التحكم
                    setTimeout(() => {
                        window.location.href = '/admin/';
                    }, 1000);
                } else {
                    errorDiv.textContent = result.message || 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
                    errorDiv.style.display = 'block';
                }
            } catch (error) {
                errorDiv.textContent = 'حدث خطأ أثناء تسجيل الدخول';
                errorDiv.style.display = 'block';
                console.error('Login error:', error);
            }
        });
    }
});
