<?php
// Include security and database functions
require_once 'security.php';
require_once 'database.php';
// Security functions already handle session initialization
initializeSessionSecurity();

// Get search parameters
$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$city = isset($_GET['city']) ? $_GET['city'] : '';

// Pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$items_per_page = 12;
$offset = ($page - 1) * $items_per_page;

// Build filters for database query
$filters = [];
if (!empty($query)) {
    $filters['search'] = $query;
}
if (!empty($category)) {
    $filters['category'] = $category;
}
if (!empty($city)) {
    $filters['city'] = $city;
}

// Get items from database
$items = getItems($filters, $items_per_page, $offset);
$total_items = getItemsCount($filters);
$total_pages = ceil($total_items / $items_per_page);

// Category names in different languages
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
?>
<!DOCTYPE html>
<html lang="fa" id="html-element">
<head>
  <meta charset="UTF-8">
  <title>بازار افغانستان | جستجو</title>
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

    .search-form {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      align-items: center;
      margin-bottom: 20px;
    }

    .search-form input {
      flex: 2;
      padding: 10px;
      font-size: 16px;
      border: 2px solid #ffffff;
      border-radius: 8px;
      background-color: rgba(255,255,255,0.2);
      color: #fff;
    }

    .search-form input::placeholder {
      color: rgba(255,255,255,0.7);
    }

    .search-form select {
      flex: 1;
      padding: 10px;
      font-size: 16px;
      border: 2px solid #ffffff;
      border-radius: 8px;
      background-color: rgba(255,255,255,0.2);
      color: #fff;
    }

    .search-form select option {
      background-color: #2a2a2a;
      color: #fff;
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

    .top-tools {
      position: fixed;
      top: 15px;
      right: 15px;
      display: flex;
      gap: 10px;
      z-index: 999;
    }

    .top-tools button {
      padding: 8px 14px;
      border: none;
      border-radius: 20px;
      background-color: rgba(255,255,255,0.2);
      color: white;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      transition: all 0.3s ease;
    }

    .top-tools button:hover {
      background-color: rgba(255,255,255,0.4);
    }

    .back-btn {
      background-color: rgba(255,255,255,0.3) !important;
    }

    .search-results {
      margin-top: 20px;
    }

    .result-item {
      background-color: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 15px;
    }

    .no-results {
      text-align: center;
      font-size: 18px;
      color: #ffffff;
      padding: 40px;
      background-color: rgba(255,255,255,0.1);
      border-radius: 8px;
      margin-top: 20px;
    }

    .items-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .item-card {
      background-color: rgba(255,255,255,0.1);
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 12px;
      padding: 20px;
      backdrop-filter: blur(8px);
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .item-card:hover {
      background-color: rgba(255,255,255,0.15);
      border-color: rgba(255,255,255,0.5);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    .item-title {
      font-size: 18px;
      font-weight: bold;
      color: #ffffff;
      margin-bottom: 10px;
      line-height: 1.3;
    }

    .item-price {
      font-size: 20px;
      font-weight: bold;
      color: #00ff88;
      margin-bottom: 10px;
    }

    .item-description {
      color: rgba(255,255,255,0.8);
      margin-bottom: 15px;
      line-height: 1.4;
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .item-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
      font-size: 14px;
      color: rgba(255,255,255,0.7);
    }

    .item-category {
      background-color: rgba(0,255,136,0.2);
      color: #00ff88;
      padding: 4px 8px;
      border-radius: 6px;
      border: 1px solid rgba(0,255,136,0.3);
    }

    .item-city {
      color: rgba(255,255,255,0.9);
    }

    .item-date {
      font-size: 12px;
      color: rgba(255,255,255,0.6);
      margin-bottom: 10px;
    }

    .contact-btn {
      background: linear-gradient(45deg, #00c6ff, #0072ff);
      color: white;
      border: none;
      padding: 10px 15px;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      width: 100%;
      transition: all 0.3s ease;
    }

    .contact-btn:hover {
      background: linear-gradient(45deg, #0072ff, #00c6ff);
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0,114,255,0.3);
    }

    /* Contact Modal Styles */
    .contact-modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.7);
      backdrop-filter: blur(5px);
    }

    .contact-modal-content {
      background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
      border: 2px solid rgba(255,255,255,0.3);
      margin: 10% auto;
      padding: 30px;
      border-radius: 15px;
      width: 90%;
      max-width: 450px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
      position: relative;
      backdrop-filter: blur(10px);
    }

    .modal-close {
      position: absolute;
      top: 15px;
      right: 20px;
      font-size: 28px;
      font-weight: bold;
      color: rgba(255,255,255,0.8);
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .modal-close:hover {
      color: #ffffff;
    }

    .modal-header {
      text-align: center;
      margin-bottom: 25px;
    }

    .modal-header h3 {
      color: #ffffff;
      margin: 0 0 10px 0;
      font-size: 24px;
    }

    .seller-info {
      background-color: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 25px;
      text-align: center;
    }

    .seller-name {
      font-size: 20px;
      font-weight: bold;
      color: #00ff88;
      margin-bottom: 10px;
    }

    .seller-phone {
      font-size: 18px;
      color: #ffffff;
      font-family: 'Courier New', monospace;
      letter-spacing: 1px;
      margin-bottom: 15px;
    }

    .contact-actions {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .contact-action-btn {
      padding: 15px 20px;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }

    .whatsapp-btn {
      background: linear-gradient(45deg, #25D366, #128C7E);
      color: white;
    }

    .whatsapp-btn:hover {
      background: linear-gradient(45deg, #128C7E, #25D366);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(37,211,102,0.4);
    }

    .call-btn {
      background: linear-gradient(45deg, #007bff, #0056b3);
      color: white;
    }

    .call-btn:hover {
      background: linear-gradient(45deg, #0056b3, #007bff);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0,123,255,0.4);
    }

    .copy-btn {
      background: linear-gradient(45deg, #6c757d, #495057);
      color: white;
    }

    .copy-btn:hover {
      background: linear-gradient(45deg, #495057, #6c757d);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(108,117,125,0.4);
    }

    .contact-note {
      background-color: rgba(0,255,136,0.1);
      border: 1px solid rgba(0,255,136,0.3);
      border-radius: 8px;
      padding: 15px;
      margin-top: 20px;
      font-size: 14px;
      color: rgba(255,255,255,0.9);
      text-align: center;
    }

    @media (max-width: 768px) {
      .contact-modal-content {
        width: 95%;
        margin: 5% auto;
        padding: 20px;
      }
      
      .contact-action-btn {
        padding: 12px 15px;
        font-size: 14px;
      }
    }

    .pagination {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 10px;
      margin-top: 30px;
      flex-wrap: wrap;
    }

    .pagination a, .pagination span {
      padding: 8px 12px;
      background-color: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 6px;
      color: #ffffff;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .pagination a:hover {
      background-color: rgba(255,255,255,0.2);
      border-color: rgba(255,255,255,0.5);
    }

    .pagination .current {
      background-color: #00ff88;
      color: #000;
      border-color: #00ff88;
    }

    .search-info {
      color: rgba(255,255,255,0.9);
      margin-bottom: 20px;
      padding: 10px;
      background-color: rgba(255,255,255,0.05);
      border-radius: 6px;
      border-left: 4px solid #00ff88;
    }

    .circle-list {
      display: flex;
      gap: 20px;
      overflow-x: auto;
      padding-top: 10px;
    }

    .circle-item {
      min-width: 100px;
      height: 100px;
      background: radial-gradient(circle at top left, #ffffff, #e0e0e0);
      border-radius: 50%;
      box-shadow: 6px 6px 12px #d1d1d1, -6px -6px 12px #ffffff;
      text-align: center;
      padding-top: 20px;
      font-size: 26px;
      font-weight: bold;
      color: #333;
      transition: transform 0.3s;
      cursor: pointer;
    }

    .circle-item:hover {
      transform: scale(1.1);
      background: linear-gradient(to bottom, #00c6ff, #007bff);
      color: white;
    }

    .circle-item span {
      display: block;
      margin-top: 5px;
      font-size: 18px;
    }

    @media (max-width: 768px) {
      .search-form {
        flex-direction: column;
      }
      
      .search-form input, .search-form select {
        flex: 1;
        width: 100%;
      }
      
      .circle-item {
        min-width: 90px;
        height: 90px;
        font-size: 22px;
        padding-top: 15px;
      }
      
      .circle-item span {
        font-size: 16px;
        margin-top: 3px;
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
    <button onclick="location.href='index.html'" class="back-btn">↩ <span id="back-btn">بازگشت</span></button>
    <button onclick="location.href='index.html'">🏠 <span id="home-btn">خانه</span></button>
    <button onclick="location.href='login.php'">🔐 <span id="login-btn">ورود / ثبت‌نام</span></button>
    <select id="language-selector" onchange="setLanguage(this.value)" style="padding: 8px 14px; border: none; border-radius: 20px; background-color: rgba(255,255,255,0.2); color: white; font-weight: bold; cursor: pointer; box-shadow: 0 4px 8px rgba(0,0,0,0.2); transition: all 0.3s ease;">
      <option value="fa" style="background-color: #2a2a2a; color: #fff;">🇦🇫 دری</option>
      <option value="ps" style="background-color: #2a2a2a; color: #fff;">🇦🇫 پشتو</option>
      <option value="en" style="background-color: #2a2a2a; color: #fff;">🇺🇸 English</option>
    </select>
    <button onclick="toggleTheme()">🌙 <span id="theme-btn">حالت</span></button>
  </div>

  <div class="section-box">
    <h2 id="search-title">جستجو در بازار</h2>
    <form class="search-form" method="GET" action="search.php">
      <input type="text" name="q" placeholder="چی می‌گردی؟" id="searchInput" value="<?php echo htmlspecialchars($query); ?>">
      <select name="category" id="categorySelect">
        <option value="">انتخاب دسته‌بندی</option>
        <?php foreach($categories as $key => $names): ?>
        <option value="<?php echo $key; ?>" <?php echo ($category == $key) ? 'selected' : ''; ?>><?php echo $names['fa']; ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit">🔍 <span id="search-btn">جستجو</span></button>
    </form>
  </div>

  <?php if ($query || $category || $city): ?>
  <div class="section-box">
    <h2 id="results-title">نتایج جستجو</h2>
    
    <?php
    $searchTerms = [];
    if ($query) $searchTerms[] = "کلمه کلیدی: \"" . escapeOutput($query) . "\"";
    if ($category && isset($categories[$category])) $searchTerms[] = "دسته‌بندی: " . escapeOutput($categories[$category]['fa']);
    if ($city && isset($cities[$city])) $searchTerms[] = "شهر: " . escapeOutput($cities[$city]['fa']);
    
    if (!empty($searchTerms)):
    ?>
    <p id="search-terms" style="color: #ffffff; margin-bottom: 20px;">
      <?php echo implode(' | ', $searchTerms); ?>
    </p>
    <?php endif; ?>
    
    <?php if ($total_items > 0): ?>
    <div class="search-info">
      <span id="search-count-text"><?php echo $total_items; ?> آگهی پیدا شد</span>
      <?php if ($total_pages > 1): ?>
      - <span id="page-info">صفحه <?php echo $page; ?> از <?php echo $total_pages; ?></span>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <div class="search-results">
      <?php if (!empty($items)): ?>
        <div class="items-grid">
          <?php foreach ($items as $item): ?>
            <div class="item-card" onclick="openItemDetail(<?php echo intval($item['id']); ?>)">
              <div class="item-date">
                <?php echo date('Y/m/d', strtotime($item['created_at'])); ?>
              </div>
              
              <h3 class="item-title"><?php echo htmlspecialchars($item['title']); ?></h3>
              
              <div class="item-price">
                <?php echo number_format($item['price']); ?> <span id="currency">افغانی</span>
              </div>
              
              <?php if (!empty($item['description'])): ?>
              <div class="item-description">
                <?php echo htmlspecialchars($item['description']); ?>
              </div>
              <?php endif; ?>
              
              <div class="item-meta">
                <span class="item-category">
                  <?php 
                  if (isset($categories[$item['category']])) {
                    echo $categories[$item['category']]['fa'];
                  } else {
                    echo htmlspecialchars($item['category']);
                  }
                  ?>
                </span>
                <span class="item-city">
                  <?php 
                  if (isset($cities[$item['city']])) {
                    echo $cities[$item['city']]['fa'];
                  } else {
                    echo htmlspecialchars($item['city']);
                  }
                  ?>
                </span>
              </div>
              
              <button class="contact-btn" id="contact-btn-text" onclick="event.stopPropagation(); showContactInfo(<?php echo intval($item['id']); ?>, <?php echo json_encode($item['seller_name'], JSON_HEX_APOS | JSON_HEX_QUOT); ?>, <?php echo json_encode($item['seller_phone'], JSON_HEX_APOS | JSON_HEX_QUOT); ?>)">
                📞 تماس با فروشنده
              </button>
            </div>
          <?php endforeach; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
          <?php if ($page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" id="prev-btn">قبلی</a>
          <?php endif; ?>
          
          <?php 
          $start = max(1, $page - 2);
          $end = min($total_pages, $page + 2);
          
          if ($start > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
            <?php if ($start > 2): ?><span>...</span><?php endif; ?>
          <?php endif; ?>
          
          <?php for ($i = $start; $i <= $end; $i++): ?>
            <?php if ($i == $page): ?>
              <span class="current"><?php echo $i; ?></span>
            <?php else: ?>
              <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
            <?php endif; ?>
          <?php endfor; ?>
          
          <?php if ($end < $total_pages): ?>
            <?php if ($end < $total_pages - 1): ?><span>...</span><?php endif; ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>"><?php echo $total_pages; ?></a>
          <?php endif; ?>
          
          <?php if ($page < $total_pages): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" id="next-btn">بعدی</a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        
      <?php else: ?>
        <div class="no-results">
          <p id="no-results-text">هیچ آگهی برای این جستجو پیدا نشد.</p>
          <p id="no-results-suggestion">لطفاً کلمات کلیدی دیگری امتحان کنید یا دسته‌بندی دیگری انتخاب کنید.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Contact Modal -->
  <div id="contactModal" class="contact-modal">
    <div class="contact-modal-content">
      <span class="modal-close" onclick="closeContactModal()">&times;</span>
      
      <div class="modal-header">
        <h3 id="modal-title">تماس با فروشنده</h3>
      </div>
      
      <div class="seller-info">
        <div class="seller-name" id="modal-seller-name"></div>
        <div class="seller-phone" id="modal-seller-phone"></div>
      </div>
      
      <div class="contact-actions">
        <a id="whatsapp-link" class="contact-action-btn whatsapp-btn" href="#" target="_blank" onclick="logContact('whatsapp')">
          <span>📱</span>
          <span id="whatsapp-text">واتساپ پیغام</span>
        </a>
        
        <a id="call-link" class="contact-action-btn call-btn" href="#" onclick="logContact('call')">
          <span>📞</span>
          <span id="call-text">تماس تلفنی</span>
        </a>
        
        <button class="contact-action-btn copy-btn" onclick="copyPhone()">
          <span>📋</span>
          <span id="copy-text">کاپی شماره</span>
        </button>
      </div>
      
      <div class="contact-note">
        <span id="contact-note-text">بهترین ساعت تماس: ۸ صبح تا ۸ شب</span>
      </div>
    </div>
  </div>

  <script>
    let currentLang = 'fa';
    
    const translations = {
      fa: {
        'back-btn': 'بازگشت',
        'home-btn': 'خانه',
        'login-btn': 'ورود / ثبت‌نام',
        'lang-btn': 'زبان',
        'theme-btn': 'حالت',
        'search-title': 'جستجو در بازار',
        'search-btn': 'جستجو',
        'results-title': 'نتایج جستجو',
        'no-results-text': 'هیچ آگهی برای این جستجو پیدا نشد.',
        'no-results-suggestion': 'لطفاً کلمات کلیدی دیگری امتحان کنید یا دسته‌بندی دیگری انتخاب کنید.',
        'jewelry-cat': 'جواهرات',
        'mens-clothes-cat': 'لباس مردانه',
        'womens-clothes-cat': 'لباس زنانه',
        'kids-clothes-cat': 'لباس اطفال',
        'contact-btn-text': '📞 تماس با فروشنده',
        'currency': 'افغانی',
        'prev-btn': 'قبلی',
        'next-btn': 'بعدی',
        'modal-title': 'تماس با فروشنده',
        'whatsapp-text': 'واتساپ پیغام',
        'call-text': 'تماس تلفنی',
        'copy-text': 'کاپی شماره',
        'contact-note-text': 'بهترین ساعت تماس: ۸ صبح تا ۸ شب',
        'search-count-text': 'آگهی پیدا شد',
        'page-info': 'صفحه'
      },
      ps: {
        'back-btn': 'بیرته',
        'home-btn': 'کور',
        'login-btn': 'ننوتل / نوم‌لیکنه',
        'lang-btn': 'ژبه',
        'theme-btn': 'حالت',
        'search-title': 'په بازار کې لټون',
        'search-btn': 'لټون',
        'results-title': 'د لټون پایلې',
        'no-results-text': 'د دې لټون لپاره کوم اعلان ونه موندل شو.',
        'no-results-suggestion': 'مهرباني وکړه نورې کلیدي کلمې وآزمویئ یا بله برخه وټاکئ.',
        'jewelry-cat': 'ګاڼې',
        'mens-clothes-cat': 'د نارینه کالي',
        'womens-clothes-cat': 'د ښځینه کالي',
        'kids-clothes-cat': 'د ماشومانو کالي',
        'contact-btn-text': '📞 د پلورونکي سره اړیکه',
        'currency': 'افغانۍ',
        'prev-btn': 'مخکنۍ',
        'next-btn': 'راتلونکۍ',
        'modal-title': 'د پلورونکي سره اړیکه',
        'whatsapp-text': 'واټساپ پیغام',
        'call-text': 'تلیفوني اړیکه',
        'copy-text': 'د شمیرې کاپي',
        'contact-note-text': 'د اړیکې غوره وخت: ۸ سهار څخه ۸ شپه پورې',
        'search-count-text': 'اعلانونه وموندل شول',
        'page-info': 'پاڼه'
      },
      en: {
        'back-btn': 'Back',
        'home-btn': 'Home',
        'login-btn': 'Login / Register',
        'lang-btn': 'Language',
        'theme-btn': 'Theme',
        'search-title': 'Search in Market',
        'search-btn': 'Search',
        'results-title': 'Search Results',
        'no-results-text': 'No ads found for this search.',
        'no-results-suggestion': 'Please try different keywords or select another category.',
        'jewelry-cat': 'Jewelry',
        'mens-clothes-cat': 'Men\'s Clothing',
        'womens-clothes-cat': 'Women\'s Clothing',
        'kids-clothes-cat': 'Kids Clothing',
        'contact-btn-text': '📞 Contact Seller',
        'currency': 'AFN',
        'prev-btn': 'Previous',
        'next-btn': 'Next',
        'modal-title': 'Contact Seller',
        'whatsapp-text': 'WhatsApp Message',
        'call-text': 'Phone Call',
        'copy-text': 'Copy Number',
        'contact-note-text': 'Best contact time: 8 AM to 8 PM',
        'search-count-text': 'ads found',
        'page-info': 'Page'
      }
    };

    function setLanguage(lang) {
      currentLang = lang;
      
      const html = document.getElementById('html-element');
      const body = document.body;
      const selector = document.getElementById('language-selector');
      
      html.lang = currentLang;
      selector.value = currentLang;
      
      if (currentLang === 'en') {
        body.classList.add('ltr');
        body.classList.remove('pashto');
        body.style.direction = 'ltr';
      } else {
        body.classList.remove('ltr');
        body.style.direction = 'rtl';
        
        if (currentLang === 'ps') {
          body.classList.add('pashto');
        } else {
          body.classList.remove('pashto');
        }
      }
      
      const translation = translations[currentLang];
      for (const [key, value] of Object.entries(translation)) {
        const element = document.getElementById(key);
        if (element) {
          element.textContent = value;
        }
      }
      
      // Update placeholder
      const searchInput = document.getElementById('searchInput');
      if (currentLang === 'fa') {
        searchInput.placeholder = 'چی می‌گردی؟';
      } else if (currentLang === 'ps') {
        searchInput.placeholder = 'څه غواړې؟';
      } else {
        searchInput.placeholder = 'What are you looking for?';
      }
      
      localStorage.setItem('afghanMarketLang', currentLang);
    }

    // Load saved language preference
    document.addEventListener('DOMContentLoaded', function() {
      const savedLang = localStorage.getItem('afghanMarketLang') || 'fa';
      setLanguage(savedLang);
    });

    function toggleTheme() {
      document.body.classList.toggle('dark-mode');
    }

    // Global variables for contact tracking
    let currentContactInfo = {
      itemId: null,
      sellerName: '',
      sellerPhone: ''
    };

    function openItemDetail(itemId) {
      window.location.href = `item_detail.php?id=${itemId}`;
    }

    function showContactInfo(itemId, sellerName, sellerPhone) {
      // Store current contact info
      currentContactInfo = { itemId, sellerName, sellerPhone };
      
      // Update modal content
      document.getElementById('modal-seller-name').textContent = sellerName;
      document.getElementById('modal-seller-phone').textContent = sellerPhone;
      
      // Convert Afghan phone to WhatsApp format (07xxxxxxxx to +93xxxxxxxx)
      const whatsappPhone = convertToWhatsAppFormat(sellerPhone);
      const whatsappMessage = encodeURIComponent(getWhatsAppMessage());
      
      // Update WhatsApp link
      document.getElementById('whatsapp-link').href = `https://wa.me/${whatsappPhone}?text=${whatsappMessage}`;
      
      // Update call link  
      document.getElementById('call-link').href = `tel:${sellerPhone}`;
      
      // Show modal
      document.getElementById('contactModal').style.display = 'block';
      
      // Log the contact view
      logContactToServer('view_contact');
    }

    function closeContactModal() {
      document.getElementById('contactModal').style.display = 'none';
    }

    function convertToWhatsAppFormat(phone) {
      // Afghan phone format: 07xxxxxxxx -> +937xxxxxxxx
      let cleanPhone = phone.replace(/\D/g, ''); // Remove non-digits
      
      if (cleanPhone.startsWith('07') && cleanPhone.length === 10) {
        return '937' + cleanPhone.substring(2);
      } else if (cleanPhone.startsWith('937') && cleanPhone.length === 12) {
        return cleanPhone;
      } else if (cleanPhone.startsWith('07')) {
        return '937' + cleanPhone.substring(2);
      }
      
      return cleanPhone; // Return as is if format not recognized
    }

    function getWhatsAppMessage() {
      const itemTitle = currentContactInfo.sellerName ? `${currentContactInfo.sellerName}` : '';
      
      if (currentLang === 'fa') {
        return `سلام، در مورد آگهی شما در بازار افغانستان سوال داشتم.`;
      } else if (currentLang === 'ps') {
        return `سلام، ستاسو د اعلان په اړه په افغان بازار کې پوښتنه لرم.`;
      } else {
        return `Hi, I'm interested in your ad on Afghanistan Market.`;
      }
    }

    function logContact(contactType) {
      logContactToServer(contactType);
    }

    function logContactToServer(contactType) {
      // Send contact log to server
      const formData = new FormData();
      formData.append('item_id', currentContactInfo.itemId);
      formData.append('contact_type', contactType);
      formData.append('action', 'log_contact');
      formData.append('csrf_token', '<?php echo generateCSRFToken(); ?>');
      
      fetch('contact_handler.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        console.log('Contact logged:', data);
      })
      .catch(error => {
        console.log('Contact logging failed:', error);
      });
    }

    function copyPhone() {
      const phone = currentContactInfo.sellerPhone;
      
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(phone).then(() => {
          showCopySuccess();
        });
      } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = phone;
        document.body.appendChild(textArea);
        textArea.select();
        try {
          document.execCommand('copy');
          showCopySuccess();
        } catch (err) {
          console.error('Copy failed:', err);
        }
        document.body.removeChild(textArea);
      }
    }

    function showCopySuccess() {
      const btn = document.querySelector('.copy-btn');
      const originalText = btn.innerHTML;
      
      const successText = currentLang === 'fa' ? 
        '<span>✅</span><span>کاپی شد!</span>' :
        currentLang === 'ps' ? 
        '<span>✅</span><span>کاپي شوه!</span>' :
        '<span>✅</span><span>Copied!</span>';
      
      btn.innerHTML = successText;
      btn.style.background = 'linear-gradient(45deg, #28a745, #20c997)';
      
      setTimeout(() => {
        btn.innerHTML = originalText;
        btn.style.background = 'linear-gradient(45deg, #6c757d, #495057)';
      }, 2000);
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
      const modal = document.getElementById('contactModal');
      if (event.target === modal) {
        closeContactModal();
      }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeContactModal();
      }
    });
  </script>
</body>
</html>
