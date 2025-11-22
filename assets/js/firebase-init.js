// Initialize Firebase with config from server
let firebaseApp = null;

async function initializeFirebase() {
    try {
        const response = await fetch('/api/firebase-config.php');
        
        if (!response.ok) {
            throw new Error(`Firebase config failed: ${response.status}`);
        }
        
        const text = await response.text();
        
        let firebaseConfig;
        try {
            firebaseConfig = JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON from firebase-config.php:', text);
            throw new Error('Invalid Firebase config response');
        }
        
        if (!firebaseConfig.apiKey) {
            console.error('Firebase config missing apiKey');
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
