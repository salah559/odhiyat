// Firebase Configuration - Wait for Firebase SDK to load
window.firebaseInitialized = false;

// Get Firebase config from server
async function getFirebaseConfig() {
    try {
        const response = await fetch('/api/firebase-config.php');
        const config = await response.json();
        return config;
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
    // Wait for Firebase SDK to load
    let attempts = 0;
    while (typeof firebase === 'undefined' && attempts < 200) {
        await new Promise(resolve => setTimeout(resolve, 50));
        attempts++;
    }
    
    if (typeof firebase === 'undefined') {
        console.error('Firebase SDK failed to load');
        return;
    }
    
    try {
        const config = await getFirebaseConfig();
        
        if (firebase.apps.length === 0) {
            firebase.initializeApp(config);
        }
        
        window.firebaseInitialized = true;
        console.log('Firebase initialized successfully');
    } catch (e) {
        console.error('Firebase initialization error:', e);
        window.firebaseInitialized = true;
    }
}

// Start initialization immediately
initializeFirebaseApp();
