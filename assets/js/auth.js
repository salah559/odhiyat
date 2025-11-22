// Auth State Management
let currentUser = null;

// Hide page content until auth is verified
function hidePageContent() {
    document.body.style.display = 'none';
}

function showPageContent() {
    document.body.style.display = '';
}

// Helper to wait for Firebase
function waitForFirebase(callback, maxAttempts = 50) {
    if (typeof firebase !== 'undefined' && firebase.auth) {
        callback();
    } else if (maxAttempts > 0) {
        setTimeout(() => waitForFirebase(callback, maxAttempts - 1), 100);
    }
}

// Check auth state on page load
document.addEventListener('DOMContentLoaded', () => {
    const isAuthPage = window.location.pathname === '/login.html' || window.location.pathname === '/signup.html';
    
    if (!isAuthPage) {
        hidePageContent();
    }
    
    waitForFirebase(() => {
        firebase.auth().onAuthStateChanged((user) => {
            currentUser = user;
            if (user && !isAuthPage) {
                console.log('User logged in:', user.email);
                showPageContent();
            } else if (!user && !isAuthPage) {
                window.location.href = '/login.html';
            } else if (isAuthPage && user) {
                window.location.href = '/index.html';
            } else if (isAuthPage) {
                showPageContent();
            }
        });
    });
});

// Login Form Handler
waitForFirebase(() => {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const messageDiv = document.getElementById('authMessage');
            
            try {
                const result = await firebase.auth().signInWithEmailAndPassword(email, password);
                messageDiv.innerHTML = `<div class="success-message">تم تسجيل الدخول بنجاح!</div>`;
                setTimeout(() => {
                    window.location.href = '/index.html';
                }, 1000);
            } catch (error) {
                messageDiv.innerHTML = `<div class="error-message">خطأ: ${getErrorMessage(error.code)}</div>`;
            }
        });
    }
});

// Google Auth Handler for Login
waitForFirebase(() => {
    const googleAuthBtn = document.getElementById('googleAuth');
    if (googleAuthBtn) {
        googleAuthBtn.addEventListener('click', async () => {
            const provider = new firebase.auth.GoogleAuthProvider();
            try {
                const result = await firebase.auth().signInWithPopup(provider);
                window.location.href = '/index.html';
            } catch (error) {
                const messageDiv = document.getElementById('authMessage');
                messageDiv.innerHTML = `<div class="error-message">خطأ: ${getErrorMessage(error.code)}</div>`;
            }
        });
    }
});

// Signup Form Handler
waitForFirebase(() => {
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
                const result = await firebase.auth().createUserWithEmailAndPassword(email, password);
                await result.user.updateProfile({
                    displayName: fullName
                });
                
                // Store user type in localStorage
                localStorage.setItem(`userType_${result.user.uid}`, accountType);
                
                messageDiv.innerHTML = `<div class="success-message">تم إنشاء الحساب بنجاح!</div>`;
                setTimeout(() => {
                    window.location.href = '/index.html';
                }, 1000);
            } catch (error) {
                messageDiv.innerHTML = `<div class="error-message">خطأ: ${getErrorMessage(error.code)}</div>`;
            }
        });
    }
});

// Google Auth Handler for Signup
waitForFirebase(() => {
    const googleSignupBtn = document.getElementById('googleSignup');
    if (googleSignupBtn) {
        googleSignupBtn.addEventListener('click', async () => {
            const accountType = document.querySelector('input[name="accountType"]:checked').value;
            const provider = new firebase.auth.GoogleAuthProvider();
            try {
                const result = await firebase.auth().signInWithPopup(provider);
                localStorage.setItem(`userType_${result.user.uid}`, accountType);
                window.location.href = '/index.html';
            } catch (error) {
                const messageDiv = document.getElementById('authMessage');
                messageDiv.innerHTML = `<div class="error-message">خطأ: ${getErrorMessage(error.code)}</div>`;
            }
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

// Logout function - global scope
function logout() {
    waitForFirebase(() => {
        firebase.auth().signOut().then(() => {
            window.location.href = '/login.html';
        }).catch((error) => {
            console.error('Logout error:', error);
            window.location.href = '/login.html';
        });
    });
}
