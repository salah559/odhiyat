// Firebase Configuration - Wait for Firebase SDK to load
window.firebaseInitialized = false;

// Get Firebase config from server
async function getFirebaseConfig() {
    try {
        const response = await fetch('/api/firebase-config.php');
        const config = await response.json();
        if (config.apiKey && config.projectId) {
            return config;
        }
        throw new Error('Invalid config');
    } catch (e) {
        console.log('Using fallback Firebase config');
        return {
            apiKey: "AIzaSyDemoKeyForOdhiyaty123456789",
            authDomain: "odhiyaty-demo.firebaseapp.com",
            projectId: "odhiyaty-demo",
            storageBucket: "odhiyaty-demo.appspot.com",
            messagingSenderId: "123456789012",
            appId: "1:123456789012:web:abcdef123456"
        };
    }
}

// Initialize Firebase when SDK is ready
async function initializeFirebaseApp() {
    // Wait for Firebase SDK to load - longer timeout
    let attempts = 0;
    const maxAttempts = 300; // 15 seconds
    while (typeof firebase === 'undefined' && attempts < maxAttempts) {
        await new Promise(resolve => setTimeout(resolve, 50));
        attempts++;
    }
    
    if (typeof firebase === 'undefined') {
        console.error('Firebase SDK failed to load after ' + (attempts * 50) + 'ms');
        window.firebaseInitialized = true; // Mark as ready anyway
        return;
    }
    
    try {
        console.log('Firebase SDK loaded, initializing...');
        const config = await getFirebaseConfig();
        console.log('Firebase config loaded:', config.projectId);
        
        if (firebase.apps.length === 0) {
            firebase.initializeApp(config);
            console.log('Firebase app initialized');
        }
        
        window.firebaseInitialized = true;
    } catch (e) {
        console.error('Firebase initialization error:', e);
        window.firebaseInitialized = true;
    }
}

// Start initialization immediately when script loads
console.log('Firebase config script loaded');
initializeFirebaseApp();
