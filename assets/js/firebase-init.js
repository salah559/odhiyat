// Initialize Firebase with config from server
let firebaseApp = null;

async function initializeFirebase() {
    try {
        const response = await fetch('/api/firebase-config.php');
        const firebaseConfig = await response.json();
        
        if (!firebaseConfig.apiKey) {
            console.error('Firebase config not found');
            return false;
        }
        
        firebaseApp = firebase.initializeApp(firebaseConfig);
        console.log('✓ Firebase initialized');
        
        // Enable Google Sign-In
        const provider = new firebase.auth.GoogleAuthProvider();
        provider.addScope('profile');
        provider.addScope('email');
        
        window.firebaseReady = true;
        return true;
    } catch (error) {
        console.error('Firebase init error:', error);
        return false;
    }
}

// Wait for Firebase scripts to load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeFirebase);
} else {
    initializeFirebase();
}
