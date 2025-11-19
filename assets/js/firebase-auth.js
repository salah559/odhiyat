// Firebase Authentication Configuration
// تحميل الإعدادات من PHP

let firebaseConfig = null;

// دالة لتحميل إعدادات Firebase من الخادم
async function loadFirebaseConfig() {
    try {
        const response = await fetch('/admin/firebase-config.php');
        const config = await response.json();
        return config;
    } catch (error) {
        console.error('خطأ في تحميل إعدادات Firebase:', error);
        return null;
    }
}

// Initialize Firebase
let auth = null;
let googleProvider = null;

// دالة التهيئة
async function initializeFirebase() {
    if (!firebaseConfig) {
        firebaseConfig = await loadFirebaseConfig();
        if (!firebaseConfig) {
            showError('فشل تحميل إعدادات Firebase');
            return false;
        }
    }
    
    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }
    
    auth = firebase.auth();
    googleProvider = new firebase.auth.GoogleAuthProvider();
    return true;
}

// Google Sign-In Function
async function signInWithGoogle() {
    const initialized = await initializeFirebase();
    if (!initialized) {
        return;
    }
    try {
        const result = await auth.signInWithPopup(googleProvider);
        const user = result.user;
        const idToken = await user.getIdToken();
        
        // إرسال token إلى PHP backend للتحقق
        await verifyWithBackend(idToken, user);
        
    } catch (error) {
        console.error('خطأ في تسجيل الدخول:', error);
        showError('فشل تسجيل الدخول عبر Google. يرجى المحاولة مرة أخرى.');
    }
}

// التحقق من Token مع PHP Backend
async function verifyWithBackend(idToken, user) {
    try {
        const response = await fetch('/admin/firebase-verify.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                idToken: idToken,
                email: user.email,
                name: user.displayName,
                photoURL: user.photoURL
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // تسجيل الدخول نجح، إعادة توجيه للوحة التحكم
            window.location.href = '/admin/index.php';
        } else {
            showError(data.message || 'فشل التحقق من الحساب');
        }
    } catch (error) {
        console.error('خطأ في الاتصال بالخادم:', error);
        showError('خطأ في الاتصال بالخادم');
    }
}

// عرض رسائل الخطأ
function showError(message) {
    const errorDiv = document.getElementById('error-message');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    } else {
        alert(message);
    }
}

// تسجيل الخروج من Firebase
async function signOutFromFirebase() {
    try {
        await auth.signOut();
        window.location.href = '/admin/login.php';
    } catch (error) {
        console.error('خطأ في تسجيل الخروج:', error);
    }
}

// مراقبة حالة المصادقة (سيتم تفعيلها بعد التهيئة)
function setupAuthStateListener() {
    if (auth) {
        auth.onAuthStateChanged((user) => {
            if (user) {
                console.log('المستخدم مسجل الدخول:', user.email);
            } else {
                console.log('لا يوجد مستخدم مسجل');
            }
        });
    }
}

// تهيئة Firebase عند تحميل الصفحة (اختياري - للصفحات التي تحتاج مراقبة الحالة)
document.addEventListener('DOMContentLoaded', async function() {
    // لا نقوم بالتهيئة التلقائية إلا إذا كانت الصفحة تحتاجها
    // التهيئة تتم عند الضغط على زر تسجيل الدخول
});
