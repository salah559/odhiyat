// Auth State Management
let currentUser = null;

// Helper to wait for Firebase
function waitForFirebase(callback, maxAttempts = 100) {
    if (maxAttempts <= 0) {
        console.log('Firebase timeout - executing callback anyway');
        try { callback(); } catch(e) { console.log('Callback error:', e); }
        return;
    }
    
    try {
        if (window.firebaseInitialized && typeof firebase !== 'undefined' && firebase.auth && typeof firebase.auth() === 'object') {
            callback();
            return;
        }
    } catch (e) {
        // Continue
    }
    
    setTimeout(() => waitForFirebase(callback, maxAttempts - 1), 50);
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

// If user is on root path (/) redirect to login instead
if (pathname === '/' || pathname === '') {
    window.location.href = '/login.html';
}

waitForFirebase(() => {
    try {
        firebase.auth().onAuthStateChanged((user) => {
            currentUser = user;
            authCheckPending = false;
            
            if (user && isAuthPage) {
                // User is logged in but on auth page, redirect to home
                console.log('Redirecting logged-in user from auth page to home');
                setTimeout(() => {
                    window.location.href = '/index.html';
                }, 100);
            } else if (!user && !isAuthPage) {
                // User is NOT logged in and NOT on auth page, redirect to login
                console.log('Redirecting unauthorized user to login');
                setTimeout(() => {
                    window.location.href = '/login.html';
                }, 100);
            } else if (isAuthPage) {
                // User not logged in and on auth page, allow access
                console.log('Auth page accessible');
                document.body.style.display = 'block';
            } else if (user && !isAuthPage) {
                // User logged in and on regular page, allow access
                console.log('User logged in, page accessible');
                document.body.style.display = 'block';
            }
        });
    } catch (e) {
        console.error('Auth state check error:', e);
        authCheckPending = false;
        document.body.style.display = 'block';
    }
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

// Google Auth Handler for Login - Enhanced version
const googleAuthLoginHandler = async () => {
    const messageDiv = document.getElementById('authMessage');
    
    // Wait for Firebase to initialize
    let attempts = 0;
    while (!window.firebaseInitialized && attempts < 100) {
        await new Promise(resolve => setTimeout(resolve, 50));
        attempts++;
    }
    
    try {
        if (typeof firebase === 'undefined' || !firebase.auth) {
            throw new Error('Firebase not initialized');
        }
        const provider = new firebase.auth.GoogleAuthProvider();
        const result = await firebase.auth().signInWithPopup(provider);
        window.location.href = '/index.html';
    } catch (error) {
        const errorMsg = error.code ? getErrorMessage(error.code) : (error.message || 'خطأ غير متوقع');
        messageDiv.innerHTML = `<div class="error-message">خطأ: ${errorMsg}</div>`;
    }
};

// Attach Google Auth button handler
document.addEventListener('DOMContentLoaded', () => {
    const googleAuthBtn = document.getElementById('googleAuth');
    if (googleAuthBtn) {
        googleAuthBtn.addEventListener('click', (e) => {
            e.preventDefault();
            googleAuthLoginHandler();
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

// Google Auth Handler for Signup - Enhanced version
const googleAuthSignupHandler = async () => {
    const messageDiv = document.getElementById('authMessage');
    
    // Wait for Firebase to initialize
    let attempts = 0;
    while (!window.firebaseInitialized && attempts < 100) {
        await new Promise(resolve => setTimeout(resolve, 50));
        attempts++;
    }
    
    try {
        if (typeof firebase === 'undefined' || !firebase.auth) {
            throw new Error('Firebase not initialized');
        }
        const accountType = document.querySelector('input[name="accountType"]:checked').value;
        const provider = new firebase.auth.GoogleAuthProvider();
        const result = await firebase.auth().signInWithPopup(provider);
        localStorage.setItem(`userType_${result.user.uid}`, accountType);
        window.location.href = '/index.html';
    } catch (error) {
        const errorMsg = error.code ? getErrorMessage(error.code) : (error.message || 'خطأ غير متوقع');
        messageDiv.innerHTML = `<div class="error-message">خطأ: ${errorMsg}</div>`;
    }
};

// Attach Google Signup button handler
document.addEventListener('DOMContentLoaded', () => {
    const googleSignupBtn = document.getElementById('googleSignup');
    if (googleSignupBtn) {
        googleSignupBtn.addEventListener('click', (e) => {
            e.preventDefault();
            googleAuthSignupHandler();
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
    console.log('Logout function called');
    
    try {
        // Clear all user data immediately
        currentUser = null;
        localStorage.clear();
        sessionStorage.clear();
        
        // Try to sign out from Firebase
        if (typeof firebase !== 'undefined' && firebase.auth && typeof firebase.auth() === 'object') {
            try {
                firebase.auth().signOut().catch(() => {
                    // Silently handle Firebase signout errors
                });
            } catch (e) {
                // Silently handle Firebase errors
            }
        }
    } catch (e) {
        // Silently handle any errors
    }
    
    // Force redirect to login page
    console.log('Redirecting to login page');
    setTimeout(() => {
        window.location.href = '/login.html';
    }, 100);
}
