# 📁 هيكل المشروع - Odhiyaty

## 📂 البنية التنظيمية

```
odhiyaty/
│
├── 🏠 الصفحات الرئيسية (Frontend)
│   ├── index.html              # الصفحة الرئيسية (HTML ثابت)
│   ├── products.php            # صفحة عرض الأضاحي
│   ├── product-details.php     # تفاصيل الأضحية + نموذج الطلب
│   └── contact.php             # صفحة التواصل
│
├── 👨‍💼 لوحة التحكم (Admin Panel)
│   └── admin/
│       ├── login.php           # تسجيل الدخول (Google + Email)
│       ├── index.php           # Dashboard الرئيسية
│       ├── products.php        # إدارة المنتجات
│       ├── product-form.php    # إضافة/تعديل منتج
│       ├── orders.php          # إدارة الطلبات
│       ├── admins.php          # إدارة المسؤولين
│       ├── contacts.php        # إدارة الرسائل
│       ├── logout.php          # تسجيل الخروج
│       ├── firebase-config.php # إعدادات Firebase
│       ├── firebase-verify.php # التحقق من Firebase Tokens
│       └── includes/           # Header/Footer للأدمن
│           ├── admin_header.php
│           └── admin_footer.php
│
├── 🎨 الأصول (Assets)
│   └── assets/
│       ├── css/
│       │   ├── style.css       # التصميم الرئيسي
│       │   └── admin.css       # تصميم لوحة التحكم
│       ├── js/
│       │   └── firebase-auth.js # نظام Firebase للمصادقة
│       └── images/
│           └── logos/          # اللوغو والصور
│
├── ⚙️ الإعدادات (Configuration)
│   └── config/
│       ├── database.php        # اتصال قاعدة البيانات
│       └── init.php            # الإعداد الأساسي للمشروع
│
├── 🔧 الوظائف المشتركة (Shared Components)
│   └── includes/
│       ├── functions.php       # الوظائف المساعدة
│       ├── header.php          # رأس الموقع
│       └── footer.php          # تذييل الموقع
│
├── 💾 قاعدة البيانات (Database)
│   └── database/
│       └── odhiyaty.db         # SQLite Database (Replit)
│
├── 📤 الملفات المرفوعة (Uploads)
│   └── uploads/
│       └── products/           # صور المنتجات
│
└── 📚 التوثيق (Documentation)
    ├── replit.md               # دليل المشروع الرئيسي
    ├── FIREBASE_SETUP.md       # دليل إعداد Firebase
    ├── ENV_VARIABLES.md        # دليل المتغيرات البيئية
    ├── PROJECT_STRUCTURE.md    # هذا الملف
    ├── .env.example            # مثال للمتغيرات البيئية
    └── setup_database.php      # سكريبت إعداد قاعدة البيانات
```

---

## 🎯 تصنيف الصفحات حسب الوظيفة

### 1️⃣ صفحات الزوار (Public Pages)
| الصفحة | المسار | الوصف | التقنية |
|--------|--------|-------|---------|
| الرئيسية | `/index.html` | صفحة الهبوط + معلومات الموقع | HTML ثابت |
| الأضاحي | `/products.php` | عرض وفلترة الأضاحي | PHP + SQLite |
| التفاصيل | `/product-details.php` | معلومات الأضحية + طلب | PHP + SQLite |
| التواصل | `/contact.php` | نموذج اتصال | PHP + SQLite |

### 2️⃣ صفحات الإدارة (Admin Pages)
| الصفحة | المسار | الوصف | المصادقة المطلوبة |
|--------|--------|-------|--------------------|
| تسجيل الدخول | `/admin/login.php` | Google OAuth + Email | ❌ عامة |
| Dashboard | `/admin/index.php` | لوحة الإحصائيات | ✅ Admin |
| المنتجات | `/admin/products.php` | إدارة الأضاحي | ✅ Admin |
| الطلبات | `/admin/orders.php` | تأكيد/إلغاء الطلبات | ✅ Admin |
| المسؤولين | `/admin/admins.php` | إدارة الصلاحيات | ✅ Super Admin |
| الرسائل | `/admin/contacts.php` | الرسائل الواردة | ✅ Admin |

### 3️⃣ API Endpoints
| الملف | الوظيفة | النوع |
|-------|---------|-------|
| `firebase-config.php` | توفير إعدادات Firebase | JSON |
| `firebase-verify.php` | التحقق من Google Tokens | POST |

---

## 🔐 نظام المصادقة

### طرق تسجيل الدخول:
1. **🔵 Google Sign-In** (Firebase Authentication)
   - سريع وآمن
   - لا حاجة لكلمة مرور
   - تسجيل تلقائي للأدمن الجديد

2. **📧 Email & Password** (التقليدية)
   - للمسؤولين الموجودين
   - كلمات مرور مشفرة (bcrypt)
   - حساب افتراضي: `bouazzasalah120120@gmail.com`

---

## 🗃️ قاعدة البيانات

### الجداول:
| اسم الجدول | الوصف | العلاقات |
|------------|-------|----------|
| `admins` | معلومات المسؤولين | - |
| `products` | الأضاحي والمنتجات | `orders.product_id` |
| `orders` | طلبات العملاء | → `products` |
| `contacts` | رسائل التواصل | - |

---

## 🚀 المتطلبات

### في Replit (الحالي):
- ✅ PHP 8.2+
- ✅ SQLite
- ✅ Firebase Secrets (Replit Secrets)

### للنشر على cPanel:
- PHP 8.2+
- MySQL/PostgreSQL
- ملف `.env` مع المتغيرات البيئية
- مجلد `uploads/` قابل للكتابة

---

## 📝 ملاحظات التطوير

### إضافة صفحة جديدة:
1. أنشئ الملف في المسار المناسب
2. استخدم `require_once __DIR__ . '/config/init.php'`
3. استخدم `includes/header.php` و `includes/footer.php`
4. أضف CSRF protection لجميع النماذج

### إضافة صفحة أدمن:
1. أنشئ الملف في `admin/`
2. أضف `require_once __DIR__ . '/../config/init.php'`
3. استخدم `admin/includes/admin_header.php`
4. تحقق من تسجيل الدخول: `require_login()`

---

## 🔄 التحديثات الأخيرة

### 19 نوفمبر 2025
✅ إضافة Firebase Authentication  
✅ تحسين تصميم زر Google Sign-In  
✅ إنشاء هيكل تنظيمي واضح  
✅ توثيق شامل للمشروع  

---

**للمزيد من المعلومات، راجع:**
- `replit.md` - الدليل الرئيسي الشامل
- `FIREBASE_SETUP.md` - دليل إعداد Firebase
- `ENV_VARIABLES.md` - دليل المتغيرات البيئية
