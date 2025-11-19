# دليل إعداد Firebase Authentication

## الخطوة 1: إنشاء مشروع Firebase

1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. انقر على "Add project" أو "إضافة مشروع"
3. أدخل اسم المشروع (مثال: Odhiyaty)
4. اتبع الخطوات لإنشاء المشروع

## الخطوة 2: إضافة تطبيق ويب

1. من لوحة تحكم المشروع، انقر على أيقونة "Web" (`</>`)
2. أدخل اسم التطبيق (مثال: Odhiyaty Admin)
3. **لا تحتاج** لتفعيل Firebase Hosting
4. انقر على "Register app"
5. ستظهر لك بيانات التكوين (firebaseConfig) - احتفظ بها

## الخطوة 3: تفعيل Google Authentication

1. من القائمة الجانبية، اختر "Authentication"
2. انقر على "Get started"
3. اذهب إلى تبويب "Sign-in method"
4. انقر على "Google"
5. فعّل الخيار (Enable)
6. أدخل بريد إلكتروني للدعم
7. احفظ التغييرات

## الخطوة 4: إضافة Domain المصرح به

1. في صفحة Authentication > Settings > Authorized domains
2. أضف domain الخاص بموقعك في Replit
3. مثال: `your-project-name.repl.co`

## الخطوة 5: نسخ بيانات التكوين

من بيانات firebaseConfig، ستحتاج:

```javascript
{
  apiKey: "AIza...",
  authDomain: "your-project.firebaseapp.com",
  projectId: "your-project-id",
  storageBucket: "your-project.appspot.com",
  messagingSenderId: "123456789",
  appId: "1:123456789:web:abc123"
}
```

## الخطوة 6: إضافة المتغيرات في Replit

سيطلب منك Replit Agent إضافة المتغيرات التالية:

- `FIREBASE_API_KEY`: قيمة apiKey
- `FIREBASE_AUTH_DOMAIN`: قيمة authDomain
- `FIREBASE_PROJECT_ID`: قيمة projectId
- `FIREBASE_STORAGE_BUCKET`: قيمة storageBucket
- `FIREBASE_MESSAGING_SENDER_ID`: قيمة messagingSenderId
- `FIREBASE_APP_ID`: قيمة appId

## ملاحظات مهمة

✅ يمكنك استخدام تسجيل الدخول عبر Google أو النظام القديم (البريد وكلمة المرور)
✅ المستخدمون الجدد الذين يسجلون عبر Google سيتم إضافتهم تلقائياً للنظام
✅ يمكن للـ Super Admin إدارة صلاحيات المستخدمين من لوحة التحكم
✅ جميع مستخدمي Google يبدأون بصلاحيات عادية (ليسوا Super Admin)

## اختبار تسجيل الدخول

1. اذهب إلى `/admin/login.php`
2. انقر على زر "تسجيل الدخول عبر Google"
3. اختر حساب Google الخاص بك
4. سيتم توجيهك تلقائياً للوحة التحكم

## حل المشاكل

### لا يعمل زر Google Sign-In
- تأكد من إضافة جميع المتغيرات البيئية بشكل صحيح
- تأكد من تفعيل Google Authentication في Firebase Console
- تأكد من إضافة Domain في Authorized domains

### رسالة "Unauthorized domain"
- أضف domain موقعك في Firebase Console > Authentication > Settings > Authorized domains

### لا يتم إنشاء حساب المستخدم
- تحقق من أن قاعدة البيانات تعمل بشكل صحيح
- تحقق من أن جدول admins موجود
