# Firebase Setup for أضحيتي

## Configuration Steps

1. Go to [Firebase Console](https://console.firebase.google.com)
2. Create a new project or select existing one
3. Enable Authentication (Email/Password + Google Sign-In)
4. Get your Firebase config:
   - Project ID
   - API Key
   - Auth Domain
   - Storage Bucket
   - Messaging Sender ID
   - App ID

5. Add these as environment variables in Replit Secrets:
   - `FIREBASE_API_KEY`
   - `FIREBASE_AUTH_DOMAIN`
   - `FIREBASE_PROJECT_ID`
   - `FIREBASE_STORAGE_BUCKET`
   - `FIREBASE_MESSAGING_SENDER_ID`
   - `FIREBASE_APP_ID`

6. For Google OAuth, add Authorized redirect URIs in Firebase Console:
   - `https://[YOUR-REPLIT-DOMAIN].repl.co/login.html`

The auth system is configured in `assets/js/firebase-auth.js`
