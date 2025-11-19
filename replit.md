# موقع Odhiyaty - أضحيتي

## نظرة عامة
موقع "Odhiyaty" هو منصة احترافية لبيع الأضاحي والأغنام. تم بناء المشروع باستخدام **معمارية Frontend/Backend منفصلة**:
- **Frontend**: صفحات HTML/JavaScript تتواصل مع Backend عبر REST API
- **Backend**: PHP REST API endpoints لإدارة البيانات
- **Admin Panel**: لوحة تحكم PHP للإدارة

الصفحة الرئيسية **index.html** هي صفحة HTML ثابتة بتصميم عصري متجاوب. صفحات العملاء (products.php, product-details.php, contact.php) تستخدم JavaScript لجلب البيانات من API.

## التقنيات المستخدمة
- **Frontend**: HTML5, CSS3, Vanilla JavaScript (Client-Side Rendering)
- **Backend API**: PHP 8.2 REST API
- **Database**: SQLite (متوافق مع MySQL/PostgreSQL)
- **المصادقة**: Firebase Authentication مع Google Sign-In (Admin Panel)
- **التصميم**: Custom CSS مع Glassmorphism
- **الأمان**: PDO Prepared Statements, CSRF Protection, Password Hashing, Firebase Token Verification, HTML Escaping
- **البيئة**: Replit Environment

## بنية المشروع
```
.
├── admin/                   # لوحة تحكم الأدمن (Server-Side Rendering)
│   ├── includes/           # Header/Footer للأدمن
│   ├── index.php           # Dashboard الرئيسية
│   ├── login.php           # تسجيل دخول الأدمن
│   ├── products.php        # إدارة المنتجات
│   ├── product-form.php    # إضافة/تعديل منتج
│   ├── orders.php          # إدارة الطلبات
│   ├── admins.php          # إدارة المسؤولين
│   ├── contacts.php        # إدارة الرسائل
│   └── logout.php          # تسجيل الخروج
├── api/                     # REST API Endpoints
│   ├── products.php        # GET /api/products.php - قائمة المنتجات
│   ├── product-details.php # GET /api/product-details.php?id= - تفاصيل منتج
│   ├── order.php           # POST /api/order.php - إرسال طلب
│   ├── contact.php         # POST /api/contact.php - إرسال رسالة
│   └── csrf-token.php      # GET /api/csrf-token.php - جلب CSRF token
├── assets/
│   ├── css/
│   │   ├── style.css       # التصميم الرئيسي
│   │   ├── admin.css       # تصميم لوحة التحكم
│   │   └── google-button.css # تصميم زر Google
│   ├── images/
│   │   ├── logos/          # اللوغو
│   │   └── placeholder.svg # صورة افتراضية للمنتجات
│   └── js/
│       └── api.js          # وظائف JavaScript المشتركة للـ API
├── config/
│   ├── database.php        # إعدادات قاعدة البيانات
│   └── init.php            # ملف الإعداد الأساسي
├── database/
│   └── odhiyaty.db         # قاعدة بيانات SQLite
├── includes/
│   ├── functions.php       # الوظائف المشتركة
│   ├── header.php          # رأس الموقع (Server-Side)
│   └── footer.php          # تذييل الموقع (Server-Side)
├── uploads/products/       # مجلد رفع الصور
├── index.html              # الصفحة الرئيسية (HTML ثابت)
├── products.php            # صفحة عرض الأضاحي (Frontend with JS)
├── product-details.php     # تفاصيل الأضحية (Frontend with JS)
├── contact.php             # صفحة التواصل (Frontend with JS)
├── setup_database.php      # إعداد قاعدة البيانات
├── router.php              # PHP Router للخادم المدمج
└── add_sample_products.php # إضافة منتجات تجريبية

```

## الميزات الرئيسية

### للعملاء
✅ **الصفحة الرئيسية (index.html)**: صفحة HTML ثابتة بتصميم فاخر
  - عرض 3 منتجات عينة
  - قسم Hero بتدرج ذهبي
  - قسم تواصل معنا مدمج
  - روابط داخلية (Anchor links)
  - لا تحتاج PHP أو قاعدة بيانات
✅ **تصفح الأضاحي (products.php)**: نظام فلترة متقدم حسب النوع، الوزن، والسعر  
✅ **تفاصيل الأضحية**: معرض صور، معلومات كاملة، نموذج طلب  
✅ **نظام الطلبات**: كل أضحية يمكن طلبها مرة واحدة فقط  
✅ **حماية من الطلبات المتزامنة**: SELECT FOR UPDATE لمنع الحجز المزدوج  
✅ **صفحة تواصل معنا (contact.php)**: نموذج اتصال آمن  

### للمسؤولين
✅ **لوحة تحكم شاملة**: إحصائيات في الوقت الفعلي  
✅ **إدارة المنتجات**: إضافة، تعديل، حذف + رفع صور متعددة  
✅ **إدارة الطلبات**: 
  - تأكيد الطلبات (يتم تعليم الأضحية كـ "مباعة" بدلاً من حذفها)
  - إلغاء الطلبات (إعادة الأضحية للمتاحة)
✅ **إدارة المسؤولين**: إضافة/حذف أدمن  
✅ **إدارة الرسائل**: عرض رسائل العملاء  
✅ **نظام مصادقة متعدد**: 
  - **تسجيل الدخول عبر Google**: باستخدام Firebase Authentication
  - **تسجيل الدخول التقليدي**: عبر البريد الإلكتروني وكلمة المرور
  - **إدارة الجلسات**: Session Management آمن
  - **التحقق من الهوية**: Firebase Token Verification  

## الأمان

### حماية SQL Injection
- استخدام PDO Prepared Statements في جميع الاستعلامات
- عدم استخدام string concatenation في SQL

### حماية CSRF
- توليد CSRF tokens لجميع النماذج
- التحقق من الـ tokens في جميع POST requests
- تطبيق CSRF على: تسجيل الدخول، الطلبات، إدارة المنتجات، الطلبات، الأدمن، الرسائل

### حماية XSS
- استخدام `htmlspecialchars()` على جميع المخرجات
- تنظيف المدخلات باستخدام `clean_input()`

### حماية من الطلبات المتزامنة
- استخدام `SELECT ... FOR UPDATE` في نظام الطلبات
- إعادة فحص حالة المنتج داخل الـ transaction
- منع طلب نفس الأضحية من عدة مستخدمين في نفس الوقت

## قاعدة البيانات

### الجداول
1. **admins**: معلومات المسؤولين وصلاحياتهم
2. **products**: معلومات الأضاحي (عنوان، نوع، وزن، سعر، صور، حالة)
3. **orders**: طلبات العملاء
4. **contacts**: رسائل التواصل

### العلاقات
- `orders.product_id` → `products.id` (ON DELETE CASCADE)

## تسجيل الدخول للمسؤولين

### طريقة 1: تسجيل الدخول عبر Google (موصى بها)
1. اذهب إلى `/admin/login.php`
2. انقر على زر "تسجيل الدخول عبر Google"
3. اختر حساب Google الخاص بك
4. سيتم إنشاء حساب مسؤول لك تلقائياً

### طريقة 2: حساب Super Admin الافتراضي
```
Email: bouazzasalah120120@gmail.com
Password: admin123
```

⚠️ **مهم**: يرجى تغيير كلمة المرور بعد أول تسجيل دخول!

### Firebase Authentication
لتفعيل تسجيل الدخول عبر Google، يجب إعداد Firebase:
- راجع ملف `FIREBASE_SETUP.md` للدليل الكامل
- المتغيرات المطلوبة: FIREBASE_API_KEY, FIREBASE_AUTH_DOMAIN, FIREBASE_PROJECT_ID, وغيرها
- يتم تخزين المتغيرات بشكل آمن في Replit Secrets

## إعداد المشروع

### بيئة Replit (الإعداد التلقائي)
✅ **تم إعداد المشروع تلقائياً في Replit!**

المشروع جاهز للعمل فوراً:
- قاعدة بيانات SQLite تم إنشاؤها في `database/odhiyaty.db`
- خادم PHP يعمل على المنفذ 5000
- جميع الجداول والمسؤول الافتراضي تم إنشاؤهم

### إعداد يدوي (إذا لزم الأمر)

1. **إعداد قاعدة البيانات**
```bash
php setup_database.php
```

2. **تشغيل الموقع**
```bash
php -S 0.0.0.0:5000
```

### 3. الوصول للموقع
- **الموقع الرئيسي**: يظهر تلقائياً في معاينة Replit
- **لوحة التحكم**: `/admin/`
- **تسجيل دخول الأدمن**: `/admin/login.php`

## ملاحظات مهمة

### التوافق مع cPanel
✅ الكود متوافق 100% مع بيئة cPanel  
✅ استخدام .htaccess لإعدادات Apache  
✅ حجم رفع الملفات: 10MB  
✅ أنواع الصور المدعومة: JPG, PNG, WEBP  

### التوافق مع قواعد البيانات
الكود مكتوب باستخدام PDO ويعمل مع SQLite في Replit، ولكن يمكن تحويله بسهولة إلى MySQL/PostgreSQL:
1. `config/database.php` - تغيير DSN من sqlite إلى mysql أو pgsql
2. `setup_database.php` - تعديل أنواع البيانات حسب قاعدة البيانات المستخدمة

### رفع الصور
- المسار: `uploads/products/`
- الحد الأقصى: 5MB لكل صورة
- يتم إعادة تسمية الصور تلقائياً لتجنب التعارض

## الإحصائيات المتاحة
- إجمالي الأضاحي
- الأضاحي المتاحة
- الأضاحي المباعة
- إجمالي الطلبات
- طلبات قيد الانتظار
- طلبات مؤكدة
- إجمالي المبيعات (DH)

## الحالات المتاحة

### حالات الأضاحي
- **available**: متوفرة للطلب
- **reserved**: محجوزة (بعد إرسال طلب)
- **sold**: مباعة (بعد تأكيد الطلب)

### حالات الطلبات
- **pending**: قيد الانتظار
- **confirmed**: مؤكد (الأضحية تصبح مباعة)
- **cancelled**: ملغي (الأضحية تعود للمتاحة)

## التحديثات المستقبلية المقترحة
- [ ] نظام إشعارات للطلبات الجديدة عبر البريد الإلكتروني
- [ ] تقارير مبيعات متقدمة مع تصدير Excel/PDF
- [ ] نظام تقييمات ومراجعات العملاء
- [ ] خاصية الحجز المؤقت للأضاحي
- [ ] تكامل مع بوابات الدفع الإلكتروني (PayPal, Stripe)

## التصميم والواجهة

### نظام التصميم الحديث
تم تحديث الموقع بتصميم عصري فاخر يتناسب مع اللوغو:

**🎨 نظام الألوان:**
- ذهبي أساسي: #C4A661
- رمادي داكن: #1F1F1F  
- خلفية دافئة: #F8F5F0

**✨ التأثيرات المرئية:**
- Glassmorphism (شفافية + ضبابية)
- تدرجات لونية ناعمة
- ظلال ذهبية Glow effects
- Animations سلسة عند التحميل
- أزرار دائرية بتأثيرات Hover متقدمة
- بطاقات منتجات بحد ذهبي وظلال متعددة الطبقات
- استخدام Emoji للمنتجات العينة (🐑 🐏) بدلاً من الصور

**📱 متجاوب تماماً:**
- يعمل بشكل مثالي على جميع الأجهزة
- أحجام نصوص ديناميكية
- شبكة CSS مرنة

**🔄 حل مشكلة التخزين المؤقت:**
- إضافة version parameter لملف CSS (?v=20251118)
- يضمن تحميل أحدث إصدار من التصميم

## الدعم والصيانة
- تم بناء المشروع بواسطة Replit Agent
- تاريخ الإنشاء: نوفمبر 2025
- تاريخ آخر تحديث: 18 نوفمبر 2025
- PHP Version: 8.2+
- Database: SQLite (Replit) / MySQL (cPanel)

## آخر التعديلات

### 19 نوفمبر 2025 (التحديث الثالث - معمارية Frontend/Backend منفصلة)
✅ **تحويل إلى معمارية Client-Side Rendering**:
  - إنشاء مجلد api/ مع REST API endpoints
  - تحويل products.php, product-details.php, contact.php إلى Frontend مع JavaScript
  - إنشاء assets/js/api.js للوظائف المشتركة
  - فصل Frontend عن Backend بشكل كامل
  - استخدام Fetch API للتواصل مع Backend
  - حماية XSS بإضافة HTML escaping في JavaScript
  - إضافة صورة placeholder.svg افتراضية للمنتجات

✅ **API Endpoints**:
  - GET /api/products.php - جلب قائمة المنتجات مع فلاتر
  - GET /api/product-details.php?id=X - جلب تفاصيل منتج
  - POST /api/order.php - إرسال طلب (مع CSRF protection)
  - POST /api/contact.php - إرسال رسالة (مع CSRF protection)
  - GET /api/csrf-token.php - جلب CSRF token

✅ **تحسينات الأمان**:
  - إضافة HTML escaping في JavaScript (escapeHtml function)
  - الحفاظ على CSRF protection في جميع POST requests
  - تنظيف المدخلات في Backend API

### 19 نوفمبر 2025 (التحديث الثاني)
✅ **تحسين تصميم زر Google Sign-In**:
  - زر بتدرج لوني أزرق-أخضر (ألوان Google الرسمية)
  - تصميم أكبر وأوضح بـ Box Shadow متقدم
  - أيقونة Google محسنة بخلفية بيضاء
  - نص "✨ طريقة سريعة وآمنة" تحت الزر
  - ملف CSS منفصل للزر (google-button.css)

✅ **تنظيم بنية المشروع**:
  - إنشاء ملف `PROJECT_STRUCTURE.md` - دليل شامل لهيكل المشروع
  - تصنيف الصفحات حسب الوظيفة (عامة، إدارة، API)
  - توثيق كامل لجميع المجلدات والملفات
  - إنشاء مجلدات `pages/` و `docs/` للتنظيم المستقبلي

✅ **ملفات توثيق جديدة**:
  - `PROJECT_STRUCTURE.md` - هيكل المشروع التفصيلي
  - `ENV_VARIABLES.md` - دليل المتغيرات البيئية
  - `assets/css/google-button.css` - ملف CSS للزر

### 19 نوفمبر 2025 (التحديث الأول)
✅ **إضافة Firebase Authentication**:
  - تكامل كامل مع Firebase لتسجيل الدخول عبر Google
  - زر "تسجيل الدخول عبر Google" في صفحة تسجيل الدخول للأدمن
  - التحقق الآمن من Firebase ID Tokens في الخادم
  - إنشاء حسابات مسؤولين تلقائياً للمستخدمين الجدد عبر Google
  - دعم المصادقة المزدوجة (Google + التقليدية)
  - ملف دليل إعداد Firebase شامل (FIREBASE_SETUP.md)

### 18 نوفمبر 2025
✅ تحويل الصفحة الرئيسية من index.php إلى index.html
✅ جعل الصفحة الرئيسية HTML ثابت (لا تحتاج PHP)
✅ إضافة قسم "تواصل معنا" مدمج في الصفحة الرئيسية
✅ استخدام روابط داخلية (Anchor links) للتنقل
✅ إضافة منتجات عينة مع رموز Emoji
✅ حل مشكلة التخزين المؤقت للـ CSS

---

**ملاحظات هامة للنشر:**
1. **index.html**: صفحة HTML ثابتة - تعمل على أي استضافة (حتى بدون PHP)
2. **باقي الصفحات**: تحتاج PHP 8.2+ و MySQL
3. **للنشر على cPanel**: تحديث `config/database.php` لاستخدام MySQL بدلاً من SQLite
4. **الصفحة الرئيسية جاهزة**: يمكن نشرها مباشرة بدون أي تعديلات
