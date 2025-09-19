
# بازار افغانستان | Afghanistan Market

<div align="center">
  <h3>🇦🇫 بزرگترین بازار آنلاین افغانستان</h3>
  <p>پلتفرم خرید و فروش املاک، خودرو، الکترونیکی و سایر کالاها</p>
  
  ![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white)
  ![SQLite](https://img.shields.io/badge/SQLite-3-003B57?style=flat-square&logo=sqlite&logoColor=white)
  ![HTML5](https://img.shields.io/badge/HTML5-E34F26?style=flat-square&logo=html5&logoColor=white)
  ![CSS3](https://img.shields.io/badge/CSS3-1572B6?style=flat-square&logo=css3&logoColor=white)
  ![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)
</div>

## 📋 فهرست مطالب

- [ویژگی‌ها](#ویژگیها)
- [پیش‌نیازها](#پیش-نیازها)
- [نصب و راه‌اندازی](#نصب-و-راه-اندازی)
- [ساختار پروژه](#ساختار-پروژه)
- [دسته‌بندی‌ها](#دسته-بندیها)
- [شهرهای پشتیبانی شده](#شهرهای-پشتیبانی-شده)
- [امنیت](#امنیت)
- [API Documentation](#api-documentation)
- [مشارکت](#مشارکت)
- [مجوز](#مجوز)

## 🎯 ویژگی‌ها

### 🌍 چندزبانه
- **فارسی/دری** - زبان اصلی با پشتیبانی کامل RTL
- **پشتو** - زبان دوم رسمی افغانستان
- **انگلیسی** - برای کاربران بین‌المللی

### 🛒 دسته‌بندی‌های کامل
- 🚗 وسایط نقلیه (خودرو، موتور، دوچرخه)
- 🏠 املاک (خانه، آپارتمان، زمین، تجاری)
- 📱 الکترونیکی (موبایل، لپ‌تاپ، تلویزیون)
- 💎 جواهرات (طلا، نقره، سنگ قیمتی)
- 👔 لباس مردانه
- 👗 لباس زنانه
- 👶 لباس اطفال
- 📚 آموزش و کتاب
- 🧸 لوازم کودک
- 🛋️ لوازم خانگی
- 💼 استخدام و کار
- 🛠️ خدمات
- 🎮 سرگرمی و بازی
- ⚽ ورزش و تناسب اندام

### 🏙️ پوشش سراسری
پشتیبانی از تمام شهرهای بزرگ افغانستان:
- کابل، هرات، مزار شریف، قندهار
- جلال‌آباد، غزنی، بامیان، فراه
- کندز، بدخشان

### 🔒 امنیت پیشرفته
- احراز هویت کاربر
- محافظت CSRF
- Session Security
- XSS Protection
- SQL Injection Prevention

### 📱 طراحی واکنش‌گرا
- سازگار با موبایل، تبلت و دسکتاپ
- رابط کاربری مدرن با افکت‌های شیشه‌ای
- تم تیره با طیف رنگی بنفش-آبی

## 🔧 پیش‌نیازها

```bash
# نیازمندی‌های سیستم
PHP >= 8.0
SQLite3
Web Server (Apache/Nginx یا PHP Built-in Server)
```

## 🚀 نصب و راه‌اندازی

### 1️⃣ دانلود پروژه
```bash
git clone https://github.com/your-username/afghanistan-market.git
cd afghanistan-market
```

### 2️⃣ راه‌اندازی پایگاه داده
```bash
# ایجاد دایرکتوری data
mkdir -p data

# اجرای فایل پایگاه داده
php database.php
```

### 3️⃣ تولید داده‌های نمونه (اختیاری)
```bash
# اضافه کردن 1400 آگهی نمونه
php generate_comprehensive_data.php
```

### 4️⃣ راه‌اندازی سرور
```bash
# سرور توسعه PHP
php -S 0.0.0.0:5000

# یا با Apache/Nginx
# قرار دادن فایل‌ها در DocumentRoot
```

### 5️⃣ دسترسی به برنامه
```
http://localhost:5000
```

## 📁 ساختار پروژه

```
afghanistan-market/
├── 📄 index.html              # صفحه اصلی
├── 🔐 login.php               # ورود و ثبت‌نام
├── 🔍 search.php              # جستجو و نمایش آگهی‌ها
├── ➕ add_item.php            # افزودن آگهی جدید
├── 👤 admin.php               # پنل کاربری
├── 📋 item_detail.php         # جزئیات آگهی
├── 🗄️ database.php            # توابع پایگاه داده
├── 🔒 security.php            # توابع امنیتی
├── 🎯 get_items.php           # API دریافت آگهی‌ها
├── 📞 contact_handler.php     # مدیریت تماس‌ها
├── 🔧 generate_comprehensive_data.php  # تولید داده نمونه
├── 📱 manifest.json           # PWA Manifest
├── 🖼️ images/                 # تصاویر دسته‌بندی‌ها
├── 💾 data/                   # پایگاه داده SQLite
└── 📖 README.md               # مستندات پروژه
```

## 💾 ساختار پایگاه داده

### جدول items
```sql
CREATE TABLE items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    description TEXT,
    price REAL NOT NULL,
    category TEXT NOT NULL,
    city TEXT NOT NULL,
    seller_name TEXT NOT NULL,
    seller_phone TEXT NOT NULL,
    image_path TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### جدول contacts
```sql
CREATE TABLE contacts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    item_id INTEGER NOT NULL,
    buyer_name TEXT NOT NULL,
    buyer_phone TEXT NOT NULL,
    message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (item_id) REFERENCES items(id)
);
```

## 🔌 API Documentation

### دریافت آگهی‌ها
```http
GET /get_items.php?category=electronics&city=kabul&limit=12&page=1
```

**پارامترها:**
- `category` (اختیاری): دسته‌بندی
- `city` (اختیاری): شهر
- `q` (اختیاری): کلمه کلیدی جستجو
- `limit` (اختیاری): تعداد آگهی (پیش‌فرض: 12)
- `page` (اختیاری): شماره صفحه (پیش‌فرض: 1)

**پاسخ:**
```json
{
  "success": true,
  "items": [...],
  "pagination": {
    "current_page": 1,
    "total_pages": 5,
    "total_items": 57
  }
}
```

## 🛡️ ویژگی‌های امنیتی

- **CSRF Protection**: محافظت در برابر حملات Cross-Site Request Forgery
- **Session Security**: مدیریت امن جلسات کاربری
- **Input Sanitization**: پاکسازی ورودی‌های کاربر
- **XSS Prevention**: جلوگیری از حملات Cross-Site Scripting
- **SQL Injection Protection**: استفاده از Prepared Statements

## 🔧 پیکربندی

### تنظیمات پایگاه داده
فایل پایگاه داده در مسیر `data/marketplace.db` ذخیره می‌شود.

### تنظیمات امنیت
- Session timeout: 30 دقیقه
- CSRF token expiry: 1 ساعت
- Maximum login attempts: 5 بار

## 🌟 ویژگی‌های پیشرفته

### Progressive Web App (PWA)
- قابلیت نصب روی موبایل
- کار آفلاین
- آیکون‌های اختصاصی

### سیستم تماس هوشمند
- لینک مستقیم WhatsApp
- تماس تلفنی
- کپی خودکار شماره

### جستجوی پیشرفته
- جستجو در عنوان و توضیحات
- فیلتر بر اساس دسته‌بندی
- فیلتر بر اساس شهر
- صفحه‌بندی نتایج

## 🤝 مشارکت

برای مشارکت در پروژه:

1. Fork کنید
2. Branch جدید ایجاد کنید (`git checkout -b feature/AmazingFeature`)
3. تغییرات را Commit کنید (`git commit -m 'Add some AmazingFeature'`)
4. Push کنید (`git push origin feature/AmazingFeature`)
5. Pull Request ایجاد کنید

## 📞 تماس و پشتیبانی

- **وب‌سایت**: [afghanistan-market.replit.app](https://afghanistan-market.replit.app)
- **ایمیل**: support@afghanistanmarket.com
- **Telegram**: @AfghanistanMarket

## 📝 مجوز

این پروژه تحت مجوز MIT منتشر شده است. برای جزئیات بیشتر فایل [LICENSE](LICENSE) را مطالعه کنید.

---

<div align="center">
  <p>ساخته شده با ❤️ برای مردم افغانستان</p>
  <p>Made with ❤️ for the people of Afghanistan</p>
</div>
