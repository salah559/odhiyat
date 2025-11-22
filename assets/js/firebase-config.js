// Firebase Configuration
window.firebaseInitialized = false;

async function getFirebaseConfig() {
    try {
        const response = await fetch('/api/firebase-config.php');
        const config = await response.json();
        if (config.apiKey && config.projectId) {
            console.log('✓ Firebase config loaded');
            return config;
        }
    } catch (e) {
        console.error('Config load error:', e);
    }
    return null;
}

async function initializeFirebaseApp() {
    console.log('Initializing Firebase v9...');
    
    const firebaseConfig = await getFirebaseConfig();
    if (!firebaseConfig) {
        window.firebaseInitialized = true;
        return;
    }
    
    let attempts = 0;
    while (typeof firebase === 'undefined' && attempts < 240) {
        await new Promise(r => setTimeout(r, 25));
        attempts++;
    }
    
    if (typeof firebase === 'undefined') {
        console.error('Firebase SDK timeout');
        window.firebaseInitialized = true;
        return;
    }
    
    try {
        if (!firebase.apps?.length) {
            firebase.initializeApp(firebaseConfig);
        }
        window.firebaseInitialized = true;
        console.log('✓ Firebase v9 ready');
    } catch (e) {
        console.error('Firebase init:', e.message);
        window.firebaseInitialized = true;
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeFirebaseApp);
} else {
    initializeFirebaseApp();
}
