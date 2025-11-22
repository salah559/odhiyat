// Firebase Configuration - Wait for Firebase to load
let firebaseReady = false;

function initializeFirebase() {
    if (firebaseReady || typeof firebase === 'undefined') return;
    
    const firebaseConfig = {
        apiKey: "AIzaSyDemoKeyForOdhiyaty123456789",
        authDomain: "odhiyaty-demo.firebaseapp.com",
        projectId: "odhiyaty-demo",
        storageBucket: "odhiyaty-demo.appspot.com",
        messagingSenderId: "123456789012",
        appId: "1:123456789012:web:abcdef123456"
    };
    
    try {
        if (firebase.apps.length === 0) {
            firebase.initializeApp(firebaseConfig);
        }
        firebaseReady = true;
    } catch (e) {
        console.log('Firebase init error:', e);
    }
}

// Check Firebase every 100ms
if (typeof firebase !== 'undefined') {
    initializeFirebase();
} else {
    let checkInterval = setInterval(() => {
        if (typeof firebase !== 'undefined') {
            clearInterval(checkInterval);
            initializeFirebase();
        }
    }, 100);
}
