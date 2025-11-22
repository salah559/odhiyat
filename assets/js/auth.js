// Auth State Management
let currentUser = null;
let authToken = localStorage.getItem('authToken');
if (authToken) {
    try {
        currentUser = JSON.parse(authToken);
    } catch (e) {
        localStorage.removeItem('authToken');
    }
}

// Google Sign-In Handler
async function handleGoogleSignIn() {
    const messageDiv = document.getElementById('authMessage');
    
    // Wait for Firebase
    let attempts = 0;
    while (!window.firebaseReady && attempts < 100) {
        await new Promise(r => setTimeout(r, 50));
        attempts++;
    }
    
    if (!window.firebaseReady || !firebaseApp) {
        messageDiv.innerHTML = `<div class="error-message">Firebase غير جاهز</div>`;
        return;
    }
    
    try {
        const provider = new firebase.auth.GoogleAuthProvider();
        const result = await firebase.auth(firebaseApp).signInWithPopup(provider);
        const user = result.user;
        
        // Send token to backend
        const token = await user.getIdToken();
        const formData = new FormData();
        formData.append('action', 'firebase-signin');
        formData.append('token', token);
        formData.append('email', user.email);
        formData.append('name', user.displayName || 'مستخدم');
        formData.append('uid', user.uid);
        
        const accountType = document.querySelector('input[name="accountType"]');
        if (accountType) {
            formData.append('accountType', accountType.value);
        }
        
        const response = await fetch('/api/auth.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            localStorage.setItem('authToken', JSON.stringify(data.user));
            messageDiv.innerHTML = `<div class="success-message">تم تسجيل الدخول بنجاح!</div>`;
            setTimeout(() => {
                window.location.href = '/index.html';
            }, 1000);
        } else {
            messageDiv.innerHTML = `<div class="error-message">خطأ: ${data.error}</div>`;
        }
    } catch (error) {
        messageDiv.innerHTML = `<div class="error-message">خطأ: ${error.message}</div>`;
    }
}

// Check auth state immediately
const pathname = window.location.pathname;
const isLoginPage = pathname.includes('login.html');
const isSignupPage = pathname.includes('signup.html');
const isAuthPage = isLoginPage || isSignupPage;
let authCheckPending = true;

// Hide all content until auth check is complete
if (!isAuthPage) {
    document.body.style.display = 'none';
}

// Check auth state
setTimeout(() => {
    authCheckPending = false;
    
    if ((pathname === '/' || pathname === '') && !currentUser) {
        window.location.href = '/login.html';
        return;
    }
    
    if (currentUser && isAuthPage) {
        window.location.href = '/index.html';
        return;
    }
    
    if (!currentUser && !isAuthPage) {
        window.location.href = '/login.html';
        return;
    }
    
    document.body.style.display = 'block';
}, 100);

// Dummy logout for compatibility
function logout() {
    localStorage.removeItem('authToken');
    localStorage.clear();
    setTimeout(() => {
        window.location.href = '/login.html';
    }, 100);
}

// Login Form Handler
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const messageDiv = document.getElementById('authMessage');
            
            try {
                const formData = new FormData();
                formData.append('action', 'signin');
                formData.append('email', email);
                formData.append('password', password);
                
                const response = await fetch('/api/auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    localStorage.setItem('authToken', JSON.stringify(data.user));
                    messageDiv.innerHTML = `<div class="success-message">تم تسجيل الدخول بنجاح!</div>`;
                    setTimeout(() => {
                        window.location.href = '/index.html';
                    }, 1000);
                } else {
                    messageDiv.innerHTML = `<div class="error-message">خطأ: ${data.error || 'فشل تسجيل الدخول'}</div>`;
                }
            } catch (error) {
                messageDiv.innerHTML = `<div class="error-message">خطأ: ${error.message}</div>`;
            }
        });
    }
});


// Signup Form Handler
document.addEventListener('DOMContentLoaded', () => {
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const fullName = document.getElementById('fullName').value;
            const email = document.getElementById('signupEmail').value;
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const accountType = document.querySelector('input[name="accountType"]:checked').value;
            const messageDiv = document.getElementById('authMessage');
            
            if (password !== confirmPassword) {
                messageDiv.innerHTML = `<div class="error-message">كلمات المرور غير متطابقة!</div>`;
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('action', 'signup');
                formData.append('email', email);
                formData.append('password', password);
                formData.append('fullName', fullName);
                formData.append('accountType', accountType);
                
                const response = await fetch('/api/auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    localStorage.setItem('authToken', JSON.stringify(data.user));
                    localStorage.setItem(`userType_${data.user.uid}`, accountType);
                    messageDiv.innerHTML = `<div class="success-message">تم إنشاء الحساب بنجاح!</div>`;
                    setTimeout(() => {
                        window.location.href = '/index.html';
                    }, 1000);
                } else {
                    messageDiv.innerHTML = `<div class="error-message">خطأ: ${data.error || 'فشل إنشاء الحساب'}</div>`;
                }
            } catch (error) {
                messageDiv.innerHTML = `<div class="error-message">خطأ: ${error.message}</div>`;
            }
        });
    }
});


// Attach Google Sign-In button
document.addEventListener('DOMContentLoaded', () => {
    const googleBtn = document.getElementById('googleAuthBtn');
    if (googleBtn) {
        googleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            handleGoogleSignIn();
        });
    }
});

// Get user-friendly error messages
function getErrorMessage(code) {
    const errors = {
        'auth/invalid-email': 'البريد الإلكتروني غير صحيح',
        'auth/user-disabled': 'هذا الحساب معطل',
        'auth/user-not-found': 'المستخدم غير موجود',
        'auth/wrong-password': 'كلمة المرور غير صحيحة',
        'auth/email-already-in-use': 'هذا البريد الإلكتروني مستخدم بالفعل',
        'auth/weak-password': 'كلمة المرور ضعيفة جداً',
        'auth/operation-not-allowed': 'هذه العملية غير مسموحة',
        'auth/popup-closed-by-user': 'تم إغلاق النافذة من قبل المستخدم'
    };
    return errors[code] || 'حدث خطأ ما';
}
