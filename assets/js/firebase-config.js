// Firebase Configuration
const firebaseConfig = {
    apiKey: "AIzaSyDemoKeyForOdhiyaty123456789",
    authDomain: "odhiyaty-demo.firebaseapp.com",
    projectId: "odhiyaty-demo",
    storageBucket: "odhiyaty-demo.appspot.com",
    messagingSenderId: "123456789012",
    appId: "1:123456789012:web:abcdef123456"
};

// Initialize Firebase
if (typeof firebase !== 'undefined' && firebase.apps && firebase.apps.length === 0) {
    firebase.initializeApp(firebaseConfig);
}

// Get Auth instance
const auth = firebase.auth();
