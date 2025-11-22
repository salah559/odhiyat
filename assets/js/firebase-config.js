// Firebase Configuration - Load from server and inject
window.firebaseInitialized = false;
let firebaseConfig = null;

// Get Firebase config from server
async function getFirebaseConfig() {
    try {
        const response = await fetch('/api/firebase-config.php');
        const config = await response.json();
        if (config.apiKey && config.projectId) {
            console.log('Firebase config loaded from server');
            return config;
        }
        throw new Error('Invalid config');
    } catch (e) {
        console.log('Could not load config from server');
        return null;
    }
}

// Dynamically load Firebase SDK from alternative CDN
function loadFirebaseSDK() {
    return new Promise((resolve) => {
        // Try primary CDN
        const script1 = document.createElement('script');
        script1.src = 'https://www.gstatic.com/firebasejs/10.7.0/firebase-app.js';
        script1.onerror = () => {
            console.log('Primary Firebase CDN failed, trying alternative');
            const script2 = document.createElement('script');
            script2.src = 'https://cdn.jsdelivr.net/npm/firebase@10.7.0/app';
            script2.onload = () => resolve(true);
            script2.onerror = () => {
                console.log('All Firebase CDNs failed');
                resolve(false);
            };
            document.head.appendChild(script2);
        };
        script1.onload = () => {
            console.log('Firebase SDK loaded');
            resolve(true);
        };
        document.head.appendChild(script1);
    });
}

// Initialize Firebase
async function initializeFirebaseApp() {
    console.log('Starting Firebase initialization...');
    
    // Load config first
    firebaseConfig = await getFirebaseConfig();
    if (!firebaseConfig) {
        console.error('No Firebase config available');
        window.firebaseInitialized = true;
        return;
    }
    
    // Wait for Firebase global
    let attempts = 0;
    while (typeof firebase === 'undefined' && attempts < 100) {
        await new Promise(resolve => setTimeout(resolve, 50));
        attempts++;
    }
    
    if (typeof firebase === 'undefined') {
        console.error('Firebase SDK still not available');
        window.firebaseInitialized = true;
        return;
    }
    
    try {
        if (firebase.apps.length === 0) {
            firebase.initializeApp(firebaseConfig);
        }
        window.firebaseInitialized = true;
        console.log('Firebase initialized successfully');
    } catch (e) {
        console.error('Firebase init error:', e);
        window.firebaseInitialized = true;
    }
}

// Start on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeFirebaseApp);
} else {
    initializeFirebaseApp();
}
