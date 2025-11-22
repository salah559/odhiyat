// Firebase Configuration
const firebaseConfig = {
    apiKey: localStorage.getItem('FIREBASE_API_KEY') || process.env.FIREBASE_API_KEY,
    authDomain: localStorage.getItem('FIREBASE_AUTH_DOMAIN') || process.env.FIREBASE_AUTH_DOMAIN,
    projectId: localStorage.getItem('FIREBASE_PROJECT_ID') || process.env.FIREBASE_PROJECT_ID,
    storageBucket: localStorage.getItem('FIREBASE_STORAGE_BUCKET') || process.env.FIREBASE_STORAGE_BUCKET,
    messagingSenderId: localStorage.getItem('FIREBASE_MESSAGING_SENDER_ID') || process.env.FIREBASE_MESSAGING_SENDER_ID,
    appId: localStorage.getItem('FIREBASE_APP_ID') || process.env.FIREBASE_APP_ID
};

// Initialize Firebase
try {
    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }
} catch (error) {
    console.warn('Firebase already initialized or config missing:', error);
}

// Auth state management
firebase.auth().onAuthStateChanged((user) => {
    if (user) {
        localStorage.setItem('currentUser', JSON.stringify({
            uid: user.uid,
            email: user.email,
            displayName: user.displayName,
            photoURL: user.photoURL
        }));
    } else {
        localStorage.removeItem('currentUser');
    }
});

// Helper function to get current user
function getCurrentUser() {
    return firebase.auth().currentUser;
}

// Helper function to logout
async function logout() {
    try {
        await firebase.auth().signOut();
        localStorage.removeItem('currentUser');
        localStorage.removeItem('userType');
        window.location.href = '/login.html';
    } catch (error) {
        console.error('Logout error:', error);
    }
}

// Check if user is authenticated
function isAuthenticated() {
    return !!getCurrentUser();
}
