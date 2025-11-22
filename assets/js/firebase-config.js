// Firebase Configuration - Wait for Firebase to load
window.firebaseReady = false;
window.firebaseInitialized = false;
let firebaseConfig = null;

// Fetch Firebase config from server
function getFirebaseConfig() {
    if (firebaseConfig) return Promise.resolve(firebaseConfig);
    
    return fetch('/api/firebase-config.php')
        .then(response => response.json())
        .then(config => {
            if (config.apiKey && config.projectId) {
                firebaseConfig = config;
                return config;
            }
            throw new Error('Invalid Firebase config');
        })
        .catch(e => {
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
    if (window.firebaseInitialized || typeof firebase === 'undefined') return;
    
    getFirebaseConfig()
        .then(config => {
            try {
                if (firebase && firebase.apps && firebase.apps.length === 0) {
                    firebase.initializeApp(config);
                    window.firebaseInitialized = true;
                    window.firebaseReady = true;
                }
            } catch (e) {
                console.log('Firebase init error:', e);
                window.firebaseReady = true;
            }
        })
        .catch(e => {
            console.log('Firebase config load error:', e);
            window.firebaseReady = true;
        });
}

// Check Firebase every 50ms
let checkAttempts = 0;
const maxAttempts = 200; // 10 seconds
if (typeof firebase !== 'undefined') {
    initializeFirebase();
} else {
    let checkInterval = setInterval(() => {
        checkAttempts++;
        if (typeof firebase !== 'undefined') {
            clearInterval(checkInterval);
            initializeFirebase();
        } else if (checkAttempts >= maxAttempts) {
            clearInterval(checkInterval);
            window.firebaseReady = true;
        }
    }, 50);
}
