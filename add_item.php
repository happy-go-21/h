<?php
require_once 'security.php';
secureSession();
session_start();
initializeSessionSecurity();
require_once 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user_name = $_SESSION['user'];

// Get user's phone from the users file
$users_file = __DIR__ . '/../data/users.txt';
$user_phone = '';
if (file_exists($users_file)) {
    $lines = file($users_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode('|', trim($line));
        if (count($parts) == 3 && trim($parts[0]) == $user_name) {
            $user_phone = trim($parts[2]);
            break;
        }
    }
}

// Category names in different languages (same as search.php)
$categories = [
    'vehicles' => ['fa' => 'وسایط نقلیه', 'ps' => 'موټرونه', 'en' => 'Vehicles'],
    'realestate' => ['fa' => 'املاک', 'ps' => 'املاک', 'en' => 'Real Estate'],
    'electronics' => ['fa' => 'الکترونیکی', 'ps' => 'برقی توکي', 'en' => 'Electronics'],
    'jewelry' => ['fa' => 'جواهرات', 'ps' => 'ګاڼې', 'en' => 'Jewelry'],
    'mens-clothes' => ['fa' => 'لباس مردانه', 'ps' => 'د نارینه کالي', 'en' => 'Men\'s Clothing'],
    'womens-clothes' => ['fa' => 'لباس زنانه', 'ps' => 'د ښځینه کالي', 'en' => 'Women\'s Clothing'],
    'kids-clothes' => ['fa' => 'لباس اطفال', 'ps' => 'د ماشومانو کالي', 'en' => 'Kids Clothing'],
    'books' => ['fa' => 'آموزش', 'ps' => 'زده‌کړه', 'en' => 'Education'],
    'kids' => ['fa' => 'لوازم کودک', 'ps' => 'د ماشومانو توکي', 'en' => 'Kids Items'],
    'home' => ['fa' => 'لوازم خانگی', 'ps' => 'د کورونو توکي', 'en' => 'Home Items'],
    'jobs' => ['fa' => 'استخدام', 'ps' => 'دندې', 'en' => 'Jobs'],
    'services' => ['fa' => 'خدمات', 'ps' => 'خدمات', 'en' => 'Services'],
    'games' => ['fa' => 'سرگرمی', 'ps' => 'لوبې', 'en' => 'Games'],
    'sports' => ['fa' => 'ورزشی', 'ps' => 'سپورت', 'en' => 'Sports']
];

$cities = [
    'kabul' => ['fa' => 'کابل', 'ps' => 'کابل', 'en' => 'Kabul'],
    'herat' => ['fa' => 'هرات', 'ps' => 'هرات', 'en' => 'Herat'],
    'mazar' => ['fa' => 'مزار شریف', 'ps' => 'مزار شریف', 'en' => 'Mazar Sharif'],
    'kandahar' => ['fa' => 'قندهار', 'ps' => 'کندهار', 'en' => 'Kandahar'],
    'jalalabad' => ['fa' => 'جلال‌آباد', 'ps' => 'جلال‌آباد', 'en' => 'Jalalabad'],
    'ghazni' => ['fa' => 'غزنی', 'ps' => 'غزني', 'en' => 'Ghazni'],
    'bamyan' => ['fa' => 'بامیان', 'ps' => 'باميان', 'en' => 'Bamyan'],
    'farah' => ['fa' => 'فراه', 'ps' => 'فراه', 'en' => 'Farah'],
    'kunduz' => ['fa' => 'کندز', 'ps' => 'کندز', 'en' => 'Kunduz'],
    'badakhshan' => ['fa' => 'بدخشان', 'ps' => 'بدخشان', 'en' => 'Badakhshan']
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate CSRF token
    if (!validatePOSTCSRFToken()) {
        $_SESSION['error'] = 'Invalid security token. Please try again.';
        header('Location: add_item.php');
        exit;
    }
    $title = sanitizeInput(trim($_POST['title'] ?? ''), 'text');
    $description = sanitizeInput(trim($_POST['description'] ?? ''), 'text');
    $price = trim($_POST['price'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $city = trim($_POST['city'] ?? '');

    // Validation
    $errors = [];

    if (empty($title)) {
        $errors[] = 'عنوان الزامی است';
    } elseif (strlen($title) < 3 || strlen($title) > 255) {
        $errors[] = 'عنوان باید بین 3 تا 255 کاراکتر باشد';
    }

    if (empty($price)) {
        $errors[] = 'قیمت الزامی است';
    } elseif (!is_numeric($price) || $price <= 0) {
        $errors[] = 'قیمت باید عدد مثبت باشد';
    }

    if (empty($category) || !isset($categories[$category])) {
        $errors[] = 'دسته‌بندی معتبر انتخاب کنید';
    }

    if (empty($city) || !isset($cities[$city])) {
        $errors[] = 'شهر معتبر انتخاب کنید';
    }

    if (!empty($description) && strlen($description) > 1000) {
        $errors[] = 'توضیحات نباید بیش از 1000 کاراکتر باشد';
    }

    if (empty($user_phone)) {
        $errors[] = 'خطای سیستم: شماره تلفن یافت نشد';
    }

    if (empty($errors)) {
        // Prepare item data
        $itemData = [
            'title' => $title,
            'description' => $description,
            'price' => floatval($price),
            'category' => $category,
            'city' => $city,
            'seller_name' => $user_name,
            'seller_phone' => $user_phone,
            'image_path' => '' // For future image upload functionality
        ];

        // Add item to database
        $itemId = addItem($itemData);

        if ($itemId) {
            $_SESSION['success'] = 'آیتم با موفقیت اضافه شد!';
            header('Location: admin.php');
            exit;
        } else {
            $_SESSION['error'] = 'خطا در ذخیره کردن آیتم. لطفاً دوباره تلاش کنید.';
        }
    } else {
        $_SESSION['error'] = implode('<br>', $errors);
    }
}

// Check for messages
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fa" id="html-element">
<head>
  <meta charset="UTF-8">
  <title>بازار افغانستان | افزودن آیتم جدید</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <style>
    body {
      margin: 0;
      font-family: 'Tahoma', sans-serif;
      direction: rtl;
      color: #f0f0f0;
      padding: 20px;
      min-height: 100vh;
      background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
    }

    body.ltr {
      direction: ltr;
    }

    body.pashto {
      font-family: 'Tahoma', 'Afghan Sans', sans-serif;
    }

    .section-box {
      background-color: rgba(255, 255, 255, 0.1);
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
      padding: 20px;
      margin-bottom: 30px;
      backdrop-filter: blur(8px);
    }

    .section-box h2 {
      margin-top: 0;
      color: #ffffff;
      font-size: 22px;
      border-bottom: 1px solid rgba(255,255,255,0.3);
      padding-bottom: 10px;
    }

    .top-tools {
      position: fixed;
      top: 15px;
      right: 15px;
      display: flex;
      gap: 10px;
      z-index: 999;
    }

    .top-tools button, .top-tools a {
      padding: 8px 14px;
      border: none;
      border-radius: 20px;
      background-color: rgba(255,255,255,0.2);
      color: white;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
    }

    .top-tools button:hover, .top-tools a:hover {
      background-color: rgba(255,255,255,0.4);
    }

    .back-btn {
      background-color: rgba(255,255,255,0.3) !important;
    }

    .alert {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: bold;
    }

    .alert-success {
      background-color: rgba(39, 174, 96, 0.8);
      border: 1px solid rgba(39, 174, 96, 1);
      color: #ffffff;
    }

    .alert-error {
      background-color: rgba(231, 76, 60, 0.8);
      border: 1px solid rgba(231, 76, 60, 1);
      color: #ffffff;
    }

    .form-container {
      max-width: 600px;
      margin: 0 auto;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      color: #ffffff;
      font-weight: bold;
      font-size: 16px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 8px;
      background-color: rgba(255,255,255,0.1);
      color: #fff;
      backdrop-filter: blur(5px);
      box-sizing: border-box;
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
      color: rgba(255,255,255,0.6);
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      border-color: #ffffff;
      outline: none;
      background-color: rgba(255,255,255,0.2);
    }

    .form-group select option {
      background-color: #2a2a2a;
      color: #fff;
    }

    .form-group textarea {
      min-height: 100px;
      resize: vertical;
    }

    .required {
      color: #ff6b6b;
    }

    .btn-primary {
      width: 100%;
      padding: 15px 20px;
      font-size: 18px;
      background-color: #ffffff;
      color: #6a11cb;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-weight: bold;
      margin-top: 20px;
    }

    .btn-primary:hover {
      background-color: #f0f0f0;
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0,0,0,0.3);
    }

    .btn-secondary {
      display: inline-block;
      padding: 12px 20px;
      font-size: 16px;
      background-color: rgba(255,255,255,0.2);
      color: #ffffff;
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-weight: bold;
      text-decoration: none;
      text-align: center;
      margin-bottom: 20px;
    }

    .btn-secondary:hover {
      background-color: rgba(255,255,255,0.3);
      border-color: rgba(255,255,255,0.5);
    }

    .user-info {
      background-color: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
    }

    .user-details {
      color: rgba(255,255,255,0.9);
    }

    .user-details strong {
      color: #ffffff;
    }

    @media (max-width: 768px) {
      body {
        padding: 10px;
      }

      .form-container {
        max-width: 100%;
      }

      .top-tools {
        flex-direction: column;
        gap: 5px;
        top: 10px;
        right: 10px;
      }

      .user-info {
        flex-direction: column;
        text-align: center;
      }
    }

    @media (max-width: 480px) {
      .form-group input,
      .form-group select,
      .form-group textarea {
        font-size: 16px;
        padding: 10px;
      }

      .btn-primary {
        padding: 12px;
        font-size: 16px;
      }
    }
  </style>
</head>
<body>

  <div class="top-tools">
    <a href="admin.php" class="back-btn">↩ <span id="back-btn">بازگشت به پنل</span></a>
    <a href="index.html">🏠 <span id="home-btn">خانه</span></a>
    <a href="auth.php?logout=1">🚪 <span id="logout-btn">خروج</span></a>
    
    <div class="language-selector">
      <label for="language-select">🌐 </label>
      <select id="language-select" onchange="setLanguage(this.value)">
        <option value="fa" <?php echo (isset($_COOKIE['language']) && $_COOKIE['language'] == 'fa') ? 'selected' : ''; ?>>فارسی</option>
        <option value="ps" <?php echo (isset($_COOKIE['language']) && $_COOKIE['language'] == 'ps') ? 'selected' : ''; ?>>پشتو</option>
        <option value="en" <?php echo (isset($_COOKIE['language']) && $_COOKIE['language'] == 'en') ? 'selected' : ''; ?>>English</option>
      </select>
    </div>

    <button onclick="toggleTheme()">🌙 <span id="theme-btn">حالت</span></button>
  </div>

  <div class="form-container">
    <div class="section-box">
      <h2 id="page-title">افزودن آیتم جدید</h2>

      <div class="user-info">
        <div class="user-details">
          <strong id="seller-label">فروشنده:</strong> <?php echo htmlspecialchars($user_name); ?>
          &nbsp;&nbsp;&nbsp;
          <strong id="phone-label">تلفن:</strong> <?php echo htmlspecialchars($user_phone); ?>
        </div>
      </div>

      <?php if ($error_message): ?>
        <div class="alert alert-error">
          ⚠️ <?php echo $error_message; ?>
        </div>
      <?php endif; ?>

      <?php if ($success_message): ?>
        <div class="alert alert-success">
          ✅ <?php echo $success_message; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="add_item.php">
        <?php echo getCSRFTokenField(); ?>
        <div class="form-group">
          <label for="title" id="title-label">عنوان آیتم <span class="required">*</span></label>
          <input type="text" id="title" name="title" 
                 placeholder="عنوان آیتم خود را وارد کنید" 
                 value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" 
                 required maxlength="255">
        </div>

        <div class="form-group">
          <label for="description" id="description-label">توضیحات</label>
          <textarea id="description" name="description" 
                    placeholder="توضیحات تفصیلی آیتم خود را بنویسید"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
          <label for="price" id="price-label">قیمت (افغانی) <span class="required">*</span></label>
          <input type="number" id="price" name="price" 
                 placeholder="قیمت را به افغانی وارد کنید" 
                 value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" 
                 required min="1" step="0.01">
        </div>

        <div class="form-group">
          <label for="category" id="category-label">دسته‌بندی <span class="required">*</span></label>
          <select id="category" name="category" required>
            <option value="" id="category-placeholder">انتخاب دسته‌بندی</option>
            <?php 
            $selected_category = $_POST['category'] ?? '';
            foreach ($categories as $key => $cat): 
            ?>
              <option value="<?php echo $key; ?>" 
                      <?php echo ($selected_category == $key) ? 'selected' : ''; ?>
                      data-fa="<?php echo htmlspecialchars($cat['fa']); ?>"
                      data-ps="<?php echo htmlspecialchars($cat['ps']); ?>"
                      data-en="<?php echo htmlspecialchars($cat['en']); ?>">
                <?php echo htmlspecialchars($cat['fa']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="city" id="city-label">شهر <span class="required">*</span></label>
          <select id="city" name="city" required>
            <option value="" id="city-placeholder">انتخاب شهر</option>
            <?php 
            $selected_city = $_POST['city'] ?? '';
            foreach ($cities as $key => $city_info): 
            ?>
              <option value="<?php echo $key; ?>" 
                      <?php echo ($selected_city == $key) ? 'selected' : ''; ?>
                      data-fa="<?php echo htmlspecialchars($city_info['fa']); ?>"
                      data-ps="<?php echo htmlspecialchars($city_info['ps']); ?>"
                      data-en="<?php echo htmlspecialchars($city_info['en']); ?>">
                <?php echo htmlspecialchars($city_info['fa']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn-primary" id="submit-btn">
          ➕ افزودن آیتم
        </button>
      </form>

      <a href="admin.php" class="btn-secondary" id="cancel-btn">
        ❌ انصراف
      </a>
    </div>
  </div>

  <script>
    // Language switching functionality
    let currentLanguage = 'fa'; // Default language

    // Function to set the language and update UI elements
    function setLanguage(lang) {
      currentLanguage = lang;
      updateLanguage();
      // Optionally, set a cookie to remember the user's preference
      document.cookie = "language=" + lang + ";path=/";
    }

    const translations = {
      fa: {
        'page-title': 'افزودن آیتم جدید',
        'back-btn': 'بازگشت به پنل',
        'home-btn': 'خانه',
        'logout-btn': 'خروج',
        'lang-btn': 'زبان',
        'theme-btn': 'حالت',
        'seller-label': 'فروشنده:',
        'phone-label': 'تلفن:',
        'title-label': 'عنوان آیتم',
        'description-label': 'توضیحات',
        'price-label': 'قیمت (افغانی)',
        'category-label': 'دسته‌بندی',
        'city-label': 'شهر',
        'category-placeholder': 'انتخاب دسته‌بندی',
        'city-placeholder': 'انتخاب شهر',
        'submit-btn': '➕ افزودن آیتم',
        'cancel-btn': '❌ انصراف',
        'language-select-label': 'فارسی',
        'language-select-ps': 'پشتو',
        'language-select-en': 'English'
      },
      ps: {
        'page-title': 'نوی توکی ورزیادول',
        'back-btn': 'بیرته پینل ته',
        'home-btn': 'کور',
        'logout-btn': 'وتل',
        'lang-btn': 'ژبه',
        'theme-btn': 'حالت',
        'seller-label': 'پلورونکی:',
        'phone-label': 'ټلیفون:',
        'title-label': 'د توکي نوم',
        'description-label': 'تفصیل',
        'price-label': 'قیمت (افغانۍ)',
        'category-label': 'کټګورۍ',
        'city-label': 'ښار',
        'category-placeholder': 'کټګورۍ وټاکئ',
        'city-placeholder': 'ښار وټاکئ',
        'submit-btn': '➕ توکی ورزیادول',
        'cancel-btn': '❌ لغوه کول',
        'language-select-label': 'فارسی',
        'language-select-ps': 'پښتو',
        'language-select-en': 'English'
      },
      en: {
        'page-title': 'Add New Item',
        'back-btn': 'Back to Panel',
        'home-btn': 'Home',
        'logout-btn': 'Logout',
        'lang-btn': 'Language',
        'theme-btn': 'Theme',
        'seller-label': 'Seller:',
        'phone-label': 'Phone:',
        'title-label': 'Item Title',
        'description-label': 'Description',
        'price-label': 'Price (AFN)',
        'category-label': 'Category',
        'city-label': 'City',
        'category-placeholder': 'Select Category',
        'city-placeholder': 'Select City',
        'submit-btn': '➕ Add Item',
        'cancel-btn': '❌ Cancel',
        'language-select-label': 'Farsi',
        'language-select-ps': 'Pashto',
        'language-select-en': 'English'
      }
    };

    function updateLanguage() {
      const lang = translations[currentLanguage];

      for (const [key, value] of Object.entries(lang)) {
        const element = document.getElementById(key);
        if (element) {
          if (element.tagName === 'INPUT' && element.type !== 'submit') {
            element.placeholder = value;
          } else if (element.tagName === 'LABEL' && element.htmlFor === 'language-select') {
             // Special handling for the label of the language select
             const langSelect = document.getElementById('language-select');
             if (langSelect) {
                const selectedOption = langSelect.querySelector(`option[value="${currentLanguage}"]`);
                if (selectedOption) {
                    langSelect.previousElementSibling.textContent = selectedOption.textContent;
                }
             }
          }
           else if (element.tagName === 'OPTION' && element.value === currentLanguage) {
             // Update the selected option's text if it's the current language
             element.textContent = value;
           }
          else {
            element.textContent = value;
          }
        }
      }

      // Update select options
      updateSelectOptions();

      // Update document attributes
      document.documentElement.lang = currentLanguage;
      document.body.className = currentLanguage === 'en' ? 'ltr' : (currentLanguage === 'ps' ? 'pashto' : '');
    }

    function updateSelectOptions() {
      // Update category options
      const categorySelect = document.getElementById('category');
      const categoryOptions = categorySelect.querySelectorAll('option[data-fa]');
      categoryOptions.forEach(option => {
        const key = currentLanguage;
        option.textContent = option.dataset[key] || option.dataset.fa;
      });

      // Update city options
      const citySelect = document.getElementById('city');
      const cityOptions = citySelect.querySelectorAll('option[data-fa]');
      cityOptions.forEach(option => {
        const key = currentLanguage;
        option.textContent = option.dataset[key] || option.dataset.fa;
      });

      // Update language select options
      const languageSelect = document.getElementById('language-select');
      if (languageSelect) {
        const options = languageSelect.options;
        for (let i = 0; i < options.length; i++) {
          const option = options[i];
          const langKey = option.value;
          if (translations[currentLanguage] && translations[currentLanguage][`language-select-${langKey}`]) {
            option.textContent = translations[currentLanguage][`language-select-${langKey}`];
          } else if (langKey === 'fa') {
             option.textContent = 'فارسی'; // Fallback for Persian
          }
        }
      }
    }


    function toggleTheme() {
      // Theme toggle functionality (can be implemented later)
      console.log('Theme toggle clicked');
    }

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
      const title = document.getElementById('title').value.trim();
      const price = document.getElementById('price').value.trim();
      const category = document.getElementById('category').value;
      const city = document.getElementById('city').value;

      if (!title || !price || !category || !city) {
        e.preventDefault();
        alert('لطفاً تمام فیلدهای الزامی را پر کنید');
        return false;
      }

      if (parseFloat(price) <= 0) {
        e.preventDefault();
        alert('قیمت باید عدد مثبت باشد');
        return false;
      }
    });

    // Initialize page with language from cookie or default to 'fa'
    const savedLanguage = document.cookie.split('; ').find(row => row.startsWith('language='));
    if (savedLanguage) {
      currentLanguage = savedLanguage.split('=')[1];
    }
    updateLanguage();
  </script>

</body>
</html>