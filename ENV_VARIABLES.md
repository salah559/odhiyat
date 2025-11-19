# دليل المتغيرات البيئية (Environment Variables)

## في Replit (الوضع الحالي) ✅

المتغيرات البيئية محفوظة بشكل آمن في **Replit Secrets**. يمكنك عرضها والتعديل عليها من:
- Tools → Secrets في Replit

### المتغيرات الموجودة حالياً:
```
FIREBASE_API_KEY=********
FIREBASE_AUTH_DOMAIN=********
FIREBASE_PROJECT_ID=********
FIREBASE_STORAGE_BUCKET=********
FIREBASE_MESSAGING_SENDER_ID=********
FIREBASE_APP_ID=********
```

## للنشر على cPanel أو خادم آخر 🚀

### الخطوة 1: إنشاء ملف .env

في المجلد الرئيسي للمشروع، أنشئ ملف `.env` (يمكنك نسخ `.env.example`):

```bash
cp .env.example .env
```

### الخطوة 2: ملء القيم

افتح ملف `.env` وأضف القيم الحقيقية:

```env
# Firebase Configuration
FIREBASE_API_KEY=AIzaSyA...
FIREBASE_AUTH_DOMAIN=your-project.firebaseapp.com
FIREBASE_PROJECT_ID=your-project-id
FIREBASE_STORAGE_BUCKET=your-project.appspot.com
FIREBASE_MESSAGING_SENDER_ID=123456789
FIREBASE_APP_ID=1:123456789:web:abc123

# Database Configuration (اختياري - للتحويل من SQLite إلى MySQL)
# DB_TYPE=mysql
# DB_HOST=localhost
# DB_NAME=odhiyaty_db
# DB_USER=your_username
# DB_PASSWORD=your_password
```

### الخطوة 3: تحديث config/database.php (للـ MySQL)

إذا كنت تريد استخدام MySQL بدلاً من SQLite، قم بتحديث `config/database.php`:

```php
<?php
// قراءة من .env إذا كان متاحاً
if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

$dbType = getenv('DB_TYPE') ?: 'sqlite';

if ($dbType === 'mysql') {
    $host = getenv('DB_HOST') ?: 'localhost';
    $dbname = getenv('DB_NAME') ?: 'odhiyaty_db';
    $user = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';
    
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} else {
    // SQLite (Replit)
    // الكود الحالي...
}
?>
```

## ⚠️ تحذيرات أمنية

1. **لا تنشر ملف .env على GitHub أبداً!** ✋
2. ملف `.env` مضاف تلقائياً إلى `.gitignore` للحماية
3. استخدم `.env.example` كمرجع فقط (بدون قيم حقيقية)
4. غيّر جميع كلمات المرور والمفاتيح قبل النشر على الإنتاج

## كيفية قراءة المتغيرات في PHP

```php
// قراءة متغير
$apiKey = getenv('FIREBASE_API_KEY');

// أو باستخدام $_ENV
$apiKey = $_ENV['FIREBASE_API_KEY'] ?? '';
```

## للمطورين

عند تطوير المشروع محلياً:
1. انسخ `.env.example` إلى `.env`
2. اطلب القيم الحقيقية من مدير المشروع
3. لا تشارك ملف `.env` مع أحد
