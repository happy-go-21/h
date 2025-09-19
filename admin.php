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

// Get user's phone from the users file (for display purposes)
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

// Handle item deletion
if (isset($_POST['delete_item']) && isset($_POST['item_id'])) {
    // Validate CSRF token
    if (!validatePOSTCSRFToken()) {
        $_SESSION['error'] = 'Invalid security token. Please try again.';
        header('Location: admin.php');
        exit;
    }
    $item_id = intval($_POST['item_id']);
    // Verify this item belongs to the user before deleting
    $item = getItemById($item_id);
    if ($item && $item['seller_name'] == $user_name) {
        deleteItem($item_id);
        $_SESSION['success'] = 'آیتم با موفقیت حذف شد';
    } else {
        $_SESSION['error'] = 'شما اجازه حذف این آیتم را ندارید';
    }
    header('Location: admin.php');
    exit;
}

// Get search and pagination parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10; // Items per page
$offset = ($page - 1) * $limit;

// Get user's items with optional search
$filters = ['seller_name' => $user_name];
if ($search) {
    $filters['search'] = $search;
}

$user_items = getItems($filters, $limit, $offset);

// Get total count for pagination
$total_items_query = getItems(['seller_name' => $user_name]);
$total_items = count($total_items_query);
$total_pages = ceil($total_items / $limit);

// Get contact statistics for this seller
$total_contacts = getSellerContactCount($user_name);
$recent_contacts = getSellerContacts($user_name, 10, 0); // Get last 10 contacts

// Add contact count to each item for display
foreach ($user_items as &$item) {
    $item['contact_count'] = getItemContactCount($item['id']);
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
  <title>بازار افغانستان | پنل مدیریت</title>
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

    .user-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
      gap: 10px;
    }

    .user-info {
      color: #ffffff;
      font-size: 18px;
    }

    .user-stats {
      display: flex;
      gap: 20px;
      font-size: 14px;
      color: rgba(255,255,255,0.8);
    }

    .btn-primary {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #ffffff;
      color: #6a11cb;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-weight: bold;
      text-decoration: none;
      display: inline-block;
      text-align: center;
    }

    .btn-primary:hover {
      background-color: #f0f0f0;
    }

    .btn-danger {
      background-color: #e74c3c;
      color: white;
      padding: 8px 12px;
      font-size: 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-danger:hover {
      background-color: #c0392b;
    }

    .btn-edit {
      background-color: #3498db;
      color: white;
      padding: 8px 12px;
      font-size: 14px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .btn-edit:hover {
      background-color: #2980b9;
    }

    .search-form {
      display: flex;
      gap: 15px;
      align-items: center;
      margin-bottom: 20px;
      flex-wrap: wrap;
    }

    .search-form input {
      flex: 1;
      padding: 10px;
      font-size: 16px;
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 8px;
      background-color: rgba(255,255,255,0.1);
      color: #fff;
      min-width: 200px;
    }

    .search-form input::placeholder {
      color: rgba(255,255,255,0.6);
    }

    .search-form button {
      padding: 10px 20px;
      font-size: 16px;
      background-color: #ffffff;
      color: #6a11cb;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-weight: bold;
    }

    .search-form button:hover {
      background-color: #f0f0f0;
    }

    .items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .item-card {
      background-color: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 12px;
      padding: 15px;
      backdrop-filter: blur(5px);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .item-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }

    .item-title {
      font-size: 18px;
      font-weight: bold;
      color: #ffffff;
      margin-bottom: 10px;
    }

    .item-details {
      color: rgba(255,255,255,0.8);
      margin-bottom: 15px;
      line-height: 1.6;
    }

    .item-price {
      font-size: 20px;
      font-weight: bold;
      color: #00ff88;
      margin-bottom: 15px;
    }

    .item-actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
    }

    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 30px;
    }

    .pagination a, .pagination span {
      padding: 8px 12px;
      background-color: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 6px;
      color: #ffffff;
      text-decoration: none;
      transition: background-color 0.3s ease;
    }

    .pagination a:hover {
      background-color: rgba(255,255,255,0.2);
    }

    .pagination .current {
      background-color: #ffffff;
      color: #6a11cb;
      font-weight: bold;
    }

    .no-items {
      text-align: center;
      color: rgba(255,255,255,0.6);
      font-size: 18px;
      padding: 40px 20px;
    }

    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      font-weight: bold;
    }

    .alert.success {
      background-color: rgba(46, 204, 113, 0.2);
      border: 1px solid #2ecc71;
      color: #2ecc71;
    }

    .alert.error {
      background-color: rgba(231, 76, 60, 0.2);
      border: 1px solid #e74c3c;
      color: #e74c3c;
    }

    .contact-count {
      background-color: rgba(0,255,136,0.1);
      border: 1px solid rgba(0,255,136,0.3);
      border-radius: 6px;
      padding: 8px 12px;
      margin: 10px 0;
      color: #00ff88;
      font-size: 14px;
      text-align: center;
    }

    .contact-log {
      background-color: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
    }

    .contact-log-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }

    .contact-date {
      color: rgba(255,255,255,0.6);
      font-size: 12px;
    }

    .contact-type {
      background-color: rgba(0,123,255,0.2);
      color: #007bff;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
    }

    .contact-type.whatsapp {
      background-color: rgba(37,211,102,0.2);
      color: #25D366;
    }

    .contact-type.call {
      background-color: rgba(40,167,69,0.2);
      color: #28a745;
    }

    .item-link {
      color: #00ff88;
      text-decoration: none;
      font-weight: bold;
    }

    .item-link:hover {
      color: #ffffff;
      text-decoration: underline;
    }

    @media (max-width: 768px) {
      .items-grid {
        grid-template-columns: 1fr;
      }
      
      .search-form {
        flex-direction: column;
      }
      
      .search-form input {
        width: 100%;
        min-width: auto;
      }
      
      .user-header {
        flex-direction: column;
        text-align: center;
      }
      
      .top-tools {
        flex-direction: column;
        gap: 5px;
      }
    }
  </style>
</head>
<body>

  <div class="top-tools">
    <a href="index.html">🏠 <span id="home-btn">خانه</span></a>
    <a href="auth.php?logout=1">🚪 <span id="logout-btn">خروج</span></a>
    <select id="language-selector" onchange="setLanguage(this.value)" style="padding: 8px 14px; border: none; border-radius: 20px; background-color: rgba(255,255,255,0.2); color: white; font-weight: bold; cursor: pointer; box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: all 0.3s ease;">
      <option value="fa" style="background-color: #2a2a2a; color: #fff;">🇦🇫 دری</option>
      <option value="ps" style="background-color: #2a2a2a; color: #fff;">🇦🇫 پشتو</option>
      <option value="en" style="background-color: #2a2a2a; color: #fff;">🇺🇸 English</option>
    </select>
    <button onclick="toggleTheme()">🌙 <span id="theme-btn">حالت</span></button>
  </div>

  <!-- User Header -->
  <div class="section-box">
    <div class="user-header">
      <div>
        <div class="user-info">
          <span id="welcome-text">خوش آمدید</span>، <?php echo htmlspecialchars($user_name); ?>
        </div>
        <div class="user-stats">
          <span><span id="total-items-text">کل آیتم‌ها</span>: <?php echo $total_items; ?></span>
          <span><span id="total-contacts-text">کل تماس‌ها</span>: <?php echo $total_contacts; ?></span>
          <?php if ($user_phone): ?>
          <span><span id="phone-text">تماس</span>: <?php echo htmlspecialchars($user_phone); ?></span>
          <?php endif; ?>
        </div>
      </div>
      <a href="add_item.php" class="btn-primary">
        ➕ <span id="add-item-btn">افزودن آیتم جدید</span>
      </a>
    </div>
  </div>

  <!-- Messages -->
  <?php if ($success_message): ?>
  <div class="alert success"><?php echo htmlspecialchars($success_message); ?></div>
  <?php endif; ?>
  
  <?php if ($error_message): ?>
  <div class="alert error"><?php echo htmlspecialchars($error_message); ?></div>
  <?php endif; ?>

  <!-- Search -->
  <div class="section-box">
    <h2 id="search-title">جستجو در آیتم‌های شما</h2>
    <form class="search-form" method="GET">
      <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
             placeholder="عنوان یا توضیحات را جستجو کنید" id="search-input">
      <button type="submit">🔍 <span id="search-btn">جستجو</span></button>
      <?php if ($search): ?>
      <a href="admin.php" class="btn-primary">❌ <span id="clear-search">پاک کردن</span></a>
      <?php endif; ?>
    </form>
  </div>

  <!-- User Items -->
  <div class="section-box">
    <h2 id="items-title">آیتم‌های شما</h2>
    
    <?php if (empty($user_items)): ?>
    <div class="no-items">
      <p id="no-items-text">
        <?php if ($search): ?>
          هیچ آیتمی با این جستجو پیدا نشد
        <?php else: ?>
          شما هنوز آیتمی ثبت نکرده‌اید
        <?php endif; ?>
      </p>
      <?php if (!$search): ?>
      <a href="add_item.php" class="btn-primary">اولین آیتم خود را اضافه کنید</a>
      <?php endif; ?>
    </div>
    <?php else: ?>
    
    <div class="items-grid">
      <?php foreach ($user_items as $item): ?>
      <div class="item-card">
        <div class="item-title"><?php echo htmlspecialchars($item['title']); ?></div>
        <div class="item-details">
          <div><strong><span id="category-label">دسته‌بندی</span>:</strong> 
               <span id="cat-<?php echo $item['category']; ?>"><?php echo htmlspecialchars($item['category']); ?></span></div>
          <div><strong><span id="city-label">شهر</span>:</strong> <?php echo htmlspecialchars($item['city']); ?></div>
          <div><strong><span id="date-label">تاریخ</span>:</strong> <?php echo date('Y-m-d', strtotime($item['created_at'])); ?></div>
          <?php if ($item['description']): ?>
          <div><strong><span id="description-label">توضیحات</span>:</strong> <?php echo htmlspecialchars(substr($item['description'], 0, 100)); ?><?php echo strlen($item['description']) > 100 ? '...' : ''; ?></div>
          <?php endif; ?>
        </div>
        <div class="item-price">💰 <?php echo number_format($item['price']); ?> <span id="currency">افغانی</span></div>
        
        <!-- Contact Count Display -->
        <?php if ($item['contact_count'] > 0): ?>
        <div class="contact-count">
          <span>📞 <?php echo $item['contact_count']; ?> <span id="contacts-text">تماس</span></span>
        </div>
        <?php endif; ?>
        
        <div class="item-actions">
          <a href="edit_item.php?id=<?php echo $item['id']; ?>" class="btn-edit">
            ✏️ <span id="edit-btn">ویرایش</span>
          </a>
          <form method="POST" style="display: inline;" onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این آیتم را حذف کنید؟')">
            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
            <?php echo getCSRFTokenField(); ?>
            <button type="submit" name="delete_item" class="btn-danger">
              🗑️ <span id="delete-btn">حذف</span>
            </button>
          </form>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination">
      <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page-1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>">&laquo; <span id="prev-page">قبلی</span></a>
      <?php endif; ?>
      
      <?php for ($i = max(1, $page-2); $i <= min($total_pages, $page+2); $i++): ?>
        <?php if ($i == $page): ?>
          <span class="current"><?php echo $i; ?></span>
        <?php else: ?>
          <a href="?page=<?php echo $i; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>"><?php echo $i; ?></a>
        <?php endif; ?>
      <?php endfor; ?>
      
      <?php if ($page < $total_pages): ?>
        <a href="?page=<?php echo $page+1; ?><?php echo $search ? '&search='.urlencode($search) : ''; ?>"><span id="next-page">بعدی</span> &raquo;</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php endif; ?>
  </div>

  <!-- Recent Contacts Section -->
  <?php if (!empty($recent_contacts)): ?>
  <div class="section-box">
    <h2 id="contacts-title">تماس‌های اخیر</h2>
    
    <div class="contact-logs">
      <?php foreach ($recent_contacts as $contact): ?>
      <div class="contact-log">
        <div class="contact-log-header">
          <div>
            <a href="search.php?q=<?php echo urlencode($contact['item_title']); ?>" class="item-link">
              <?php echo htmlspecialchars($contact['item_title']); ?>
            </a>
            <div class="contact-date">
              <?php echo date('Y/m/d H:i', strtotime($contact['contacted_at'])); ?>
            </div>
          </div>
          <div class="contact-type <?php echo $contact['contact_type']; ?>">
            <?php 
            switch($contact['contact_type']) {
              case 'whatsapp':
                echo '📱 واتساپ';
                break;
              case 'call':
                echo '📞 تماس';
                break;
              case 'copy_phone':
                echo '📋 کاپی';
                break;
              default:
                echo '👁️ بازدید';
            }
            ?>
          </div>
        </div>
        
        <div class="contact-details">
          <div><strong><span id="price-label">قیمت</span>:</strong> <?php echo number_format($contact['price']); ?> <span id="currency">افغانی</span></div>
          <div><strong><span id="category-label">دسته‌بندی</span>:</strong> <?php echo htmlspecialchars($contact['category']); ?></div>
          
          <?php if (!empty($contact['buyer_name'])): ?>
          <div><strong><span id="buyer-label">خریدار</span>:</strong> <?php echo htmlspecialchars($contact['buyer_name']); ?></div>
          <?php endif; ?>
          
          <?php if (!empty($contact['buyer_phone'])): ?>
          <div><strong><span id="buyer-phone-label">تلفن خریدار</span>:</strong> <?php echo htmlspecialchars($contact['buyer_phone']); ?></div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    
    <?php if (count($recent_contacts) >= 10): ?>
    <div style="text-align: center; margin-top: 20px;">
      <span style="color: rgba(255,255,255,0.7); font-size: 14px;">
        <span id="showing-recent">نمایش ۱۰ تماس اخیر</span>
      </span>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <script>
    let currentLang = 'fa';
    
    const translations = {
      fa: {
        'home-btn': 'خانه',
        'logout-btn': 'خروج',
        'lang-btn': 'زبان',
        'theme-btn': 'حالت',
        'welcome-text': 'خوش آمدید',
        'total-items-text': 'کل آیتم‌ها',
        'total-contacts-text': 'کل تماس‌ها',
        'contacts-text': 'تماس',
        'contacts-title': 'تماس‌های اخیر',
        'phone-text': 'تماس',
        'add-item-btn': 'افزودن آیتم جدید',
        'search-title': 'جستجو در آیتم‌های شما',
        'items-title': 'آیتم‌های شما',
        'search-btn': 'جستجو',
        'clear-search': 'پاک کردن',
        'no-items-text': 'شما هنوز آیتمی ثبت نکرده‌اید',
        'category-label': 'دسته‌بندی',
        'city-label': 'شهر',
        'date-label': 'تاریخ',
        'description-label': 'توضیحات',
        'currency': 'افغانی',
        'edit-btn': 'ویرایش',
        'delete-btn': 'حذف',
        'prev-page': 'قبلی',
        'next-page': 'بعدی'
      },
      ps: {
        'home-btn': 'کور',
        'logout-btn': 'وتل',
        'lang-btn': 'ژبه',
        'theme-btn': 'حالت',
        'welcome-text': 'راغلاست',
        'total-items-text': 'ټول توکي',
        'phone-text': 'اړیکه',
        'add-item-btn': 'نوی توکی ورزیادول',
        'search-title': 'ستاسو په توکیو کې لټون',
        'items-title': 'ستاسو توکي',
        'search-btn': 'لټون',
        'clear-search': 'پاکول',
        'no-items-text': 'تاسو تر دغه وخته توکی نه دی ثبت کړی',
        'category-label': 'ډله',
        'city-label': 'ښار',
        'date-label': 'نېټه',
        'description-label': 'تشریح',
        'currency': 'افغانۍ',
        'edit-btn': 'سمون',
        'delete-btn': 'له منځه وړل',
        'prev-page': 'مخکنی',
        'next-page': 'راتلونکی'
      },
      en: {
        'home-btn': 'Home',
        'logout-btn': 'Logout',
        'lang-btn': 'Language',
        'theme-btn': 'Theme',
        'welcome-text': 'Welcome',
        'total-items-text': 'Total Items',
        'phone-text': 'Phone',
        'add-item-btn': 'Add New Item',
        'search-title': 'Search Your Items',
        'items-title': 'Your Items',
        'search-btn': 'Search',
        'clear-search': 'Clear',
        'no-items-text': 'You haven\'t added any items yet',
        'category-label': 'Category',
        'city-label': 'City',
        'date-label': 'Date',
        'description-label': 'Description',
        'currency': 'AFN',
        'edit-btn': 'Edit',
        'delete-btn': 'Delete',
        'prev-page': 'Previous',
        'next-page': 'Next'
      }
    };

    // Category translations
    const categoryTranslations = {
      'vehicles': { fa: 'وسایط نقلیه', ps: 'موټرونه', en: 'Vehicles' },
      'realestate': { fa: 'املاک', ps: 'املاک', en: 'Real Estate' },
      'electronics': { fa: 'الکترونیکی', ps: 'برقی توکي', en: 'Electronics' },
      'jewelry': { fa: 'جواهرات', ps: 'ګاڼې', en: 'Jewelry' },
      'mens-clothes': { fa: 'لباس مردانه', ps: 'د نارینه کالي', en: 'Men\'s Clothing' },
      'womens-clothes': { fa: 'لباس زنانه', ps: 'د ښځینه کالي', en: 'Women\'s Clothing' },
      'kids-clothes': { fa: 'لباس اطفال', ps: 'د ماشومانو کالي', en: 'Kids Clothing' },
      'books': { fa: 'آموزش', ps: 'زده‌کړه', en: 'Education' },
      'kids': { fa: 'لوازم کودک', ps: 'د ماشومانو توکي', en: 'Kids Items' },
      'home': { fa: 'لوازم خانگی', ps: 'د کورونو توکي', en: 'Home & Garden' },
      'jobs': { fa: 'استخدام', ps: 'دندې', en: 'Jobs' },
      'services': { fa: 'خدمات', ps: 'خدماتو', en: 'Services' },
      'games': { fa: 'سرگرمی', ps: 'لوبې', en: 'Entertainment' },
      'sports': { fa: 'ورزشی', ps: 'ورزش', en: 'Sports' }
    };

    function setLanguage(lang) {
      currentLang = lang;
      
      // Update text direction and font
      const body = document.body;
      const html = document.getElementById('html-element');
      const selector = document.getElementById('language-selector');
      
      selector.value = currentLang;
      
      if (currentLang === 'en') {
        body.classList.add('ltr');
        body.classList.remove('pashto');
        html.setAttribute('lang', 'en');
        html.setAttribute('dir', 'ltr');
      } else if (currentLang === 'ps') {
        body.classList.remove('ltr');
        body.classList.add('pashto');
        html.setAttribute('lang', 'ps');
        html.setAttribute('dir', 'rtl');
      } else {
        body.classList.remove('ltr', 'pashto');
        html.setAttribute('lang', 'fa');
        html.setAttribute('dir', 'rtl');
      }
      
      // Update all translatable texts
      Object.keys(translations[currentLang]).forEach(id => {
        const element = document.getElementById(id);
        if (element) {
          element.textContent = translations[currentLang][id];
        }
      });
      
      // Update category translations
      Object.keys(categoryTranslations).forEach(category => {
        const element = document.getElementById('cat-' + category);
        if (element && categoryTranslations[category][currentLang]) {
          element.textContent = categoryTranslations[category][currentLang];
        }
      });
      
      // Update search input placeholder
      const searchInput = document.getElementById('search-input');
      if (searchInput) {
        const placeholders = {
          fa: 'عنوان یا توضیحات را جستجو کنید',
          ps: 'سرلیک یا تفصیلات ولټوئ',
          en: 'Search title or description'
        };
        searchInput.setAttribute('placeholder', placeholders[currentLang]);
      }
      
      localStorage.setItem('afghanMarketLang', currentLang);
    }

    // Load saved preferences
    document.addEventListener('DOMContentLoaded', function() {
      const savedLang = localStorage.getItem('afghanMarketLang') || 'fa';
      setLanguage(savedLang);
      
      const savedTheme = localStorage.getItem('afghanMarketTheme');
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
      }
    });

    function toggleTheme() {
      document.body.classList.toggle('dark-mode');
      const isDark = document.body.classList.contains('dark-mode');
      localStorage.setItem('afghanMarketTheme', isDark ? 'dark' : 'light');
    }

    // Load saved preferences
    document.addEventListener('DOMContentLoaded', function() {
      const savedLang = localStorage.getItem('afghanMarketLang');
      if (savedLang && savedLang !== currentLang) {
        currentLang = savedLang;
        toggleLanguage();
      }
      
      const savedTheme = localStorage.getItem('afghanMarketTheme');
      if (savedTheme === 'dark') {
        document.body.classList.add('dark-mode');
      }
    });
  </script>

</body>
</html>