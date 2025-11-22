// Firebase Configuration - Wait for Firebase to load
let firebaseReady = false;
let firebaseConfig = null;

// Fetch Firebase config from server
function getFirebaseConfig() {
    if (firebaseConfig) return Promise.resolve(firebaseConfig);
    
    return fetch('/api/firebase-config.php')
        .then(response => response.json())
        .then(config => {
            // Validate that we have required fields
            if (config.apiKey && config.projectId) {
                firebaseConfig = config;
                return config;
            }
            throw new Error('Invalid Firebase config');
        })
        .catch(e => {
            // Fallback: Use demo config (won't work for real authentication)
            firebaseConfig = {
                apiKey: "AIzaSyDemoKeyForOdhiyaty123456789",
                authDomain: "odhiyaty-demo.firebaseapp.com",
                projectId: "odhiyaty-demo",
                storageBucket: "odhiyaty-demo.appspot.com",
                messagingSenderId: "123456789012",
                appId: "1:123456789012:web:abcdef123456"
            };
            return firebaseConfig;
        });
}

function initializeFirebase() {
    if (firebaseReady || typeof firebase === 'undefined') return;
    
    getFirebaseConfig()
        .then(config => {
            try {
                if (firebase && firebase.apps && firebase.apps.length === 0) {
                    firebase.initializeApp(config);
                }
                firebaseReady = true;
            } catch (e) {
                console.log('Firebase init error:', e);
                firebaseReady = true; // Mark as ready even on error
            }
        })
        .catch(e => {
            console.log('Firebase config load error:', e);
            firebaseReady = true; // Mark as ready even on error
        });
}

// Check Firebase every 100ms, max 100 attempts (10 seconds)
let checkAttempts = 0;
if (typeof firebase !== 'undefined') {
    initializeFirebase();
} else {
    let checkInterval = setInterval(() => {
        checkAttempts++;
        if (typeof firebase !== 'undefined') {
            clearInterval(checkInterval);
            initializeFirebase();
        } else if (checkAttempts >= 100) {
            clearInterval(checkInterval);
            firebaseReady = true; // Force ready state after timeout
        }
    }, 100);
}
