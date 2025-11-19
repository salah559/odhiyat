// Firebase Authentication Configuration
// سيتم تحميل إعدادات Firebase من متغيرات البيئة أو ملف config

const firebaseConfig = {
    apiKey: "YOUR_API_KEY",
    authDomain: "YOUR_AUTH_DOMAIN",
    projectId: "YOUR_PROJECT_ID",
    storageBucket: "YOUR_STORAGE_BUCKET",
    messagingSenderId: "YOUR_MESSAGING_SENDER_ID",
    appId: "YOUR_APP_ID"
};

// Initialize Firebase
firebase.initializeApp(firebaseConfig);
const auth = firebase.auth();
const googleProvider = new firebase.auth.GoogleAuthProvider();

// Google Sign-In Function
async function signInWithGoogle() {
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

// مراقبة حالة المصادقة
auth.onAuthStateChanged((user) => {
    if (user) {
        console.log('المستخدم مسجل الدخول:', user.email);
    } else {
        console.log('لا يوجد مستخدم مسجل');
    }
});
