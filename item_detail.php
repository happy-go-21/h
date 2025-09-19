
<?php
require_once 'security.php';
require_once 'database.php';
secureSession();
session_start();
initializeSessionSecurity();

// Get item ID from URL
$item_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($item_id <= 0) {
    header('Location: index.html');
    exit;
}

// Get item details
$item = getItemById($item_id);

if (!$item) {
    header('Location: index.html');
    exit;
}

// Log contact view
$user_ip = $_SERVER['REMOTE_ADDR'] ?? '';
logContact($item_id, 'view_contact', $user_ip);
?>

<!DOCTYPE html>
<html lang="fa">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($item['title']); ?> | بازار افغانستان</title>
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

    .container {
      max-width: 800px;
      margin: 0 auto;
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      padding: 30px;
      backdrop-filter: blur(8px);
    }

    .back-btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: rgba(255,255,255,0.2);
      color: white;
      text-decoration: none;
      border-radius: 8px;
      margin-bottom: 20px;
      transition: all 0.3s ease;
    }

    .back-btn:hover {
      background-color: rgba(255,255,255,0.3);
    }

    .item-title {
      font-size: 28px;
      font-weight: bold;
      margin-bottom: 15px;
      color: #ffffff;
    }

    .item-price {
      font-size: 24px;
      font-weight: bold;
      color: #00ff88;
      margin-bottom: 20px;
    }

    .item-meta {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      margin-bottom: 25px;
    }

    .meta-item {
      background-color: rgba(255,255,255,0.05);
      padding: 15px;
      border-radius: 8px;
      border: 1px solid rgba(255,255,255,0.1);
    }

    .meta-label {
      font-weight: bold;
      color: #ffffff;
      margin-bottom: 5px;
    }

    .meta-value {
      color: rgba(255,255,255,0.8);
    }

    .item-description {
      background-color: rgba(255,255,255,0.05);
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 25px;
      line-height: 1.6;
    }

    .contact-section {
      background-color: rgba(37,211,102,0.1);
      border: 2px solid rgba(37,211,102,0.3);
      border-radius: 12px;
      padding: 20px;
      text-align: center;
    }

    .contact-buttons {
      display: flex;
      gap: 15px;
      justify-content: center;
      flex-wrap: wrap;
      margin-top: 15px;
    }

    .contact-btn {
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .whatsapp-btn {
      background-color: #25D366;
      color: white;
    }

    .whatsapp-btn:hover {
      background-color: #1da851;
    }

    .call-btn {
      background-color: #007bff;
      color: white;
    }

    .call-btn:hover {
      background-color: #0056b3;
    }

    .seller-info {
      background-color: rgba(255,255,255,0.05);
      padding: 20px;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    @media (max-width: 768px) {
      .container {
        padding: 15px;
      }
      
      .item-title {
        font-size: 22px;
      }
      
      .item-price {
        font-size: 20px;
      }
      
      .contact-buttons {
        flex-direction: column;
        align-items: center;
      }
      
      .contact-btn {
        width: 100%;
        max-width: 250px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <a href="javascript:history.back()" class="back-btn">← بازگشت</a>
    
    <h1 class="item-title"><?php echo htmlspecialchars($item['title']); ?></h1>
    
    <div class="item-price"><?php echo number_format($item['price']); ?> افغانی</div>
    
    <div class="item-meta">
      <div class="meta-item">
        <div class="meta-label">دسته‌بندی</div>
        <div class="meta-value"><?php echo htmlspecialchars($item['category']); ?></div>
      </div>
      <div class="meta-item">
        <div class="meta-label">شهر</div>
        <div class="meta-value"><?php echo htmlspecialchars($item['city']); ?></div>
      </div>
      <div class="meta-item">
        <div class="meta-label">تاریخ انتشار</div>
        <div class="meta-value"><?php echo date('Y/m/d', strtotime($item['created_at'])); ?></div>
      </div>
    </div>
    
    <?php if (!empty($item['description'])): ?>
    <div class="item-description">
      <h3 style="margin-top: 0; color: #ffffff;">توضیحات</h3>
      <p style="margin: 0; white-space: pre-wrap;"><?php echo htmlspecialchars($item['description']); ?></p>
    </div>
    <?php endif; ?>
    
    <div class="seller-info">
      <h3 style="margin-top: 0; color: #ffffff;">اطلاعات فروشنده</h3>
      <div class="meta-item">
        <div class="meta-label">نام فروشنده</div>
        <div class="meta-value"><?php echo htmlspecialchars($item['seller_name']); ?></div>
      </div>
    </div>
    
    <div class="contact-section">
      <h3 style="margin-top: 0; color: #25D366;">تماس با فروشنده</h3>
      <p>برای خرید این کالا با فروشنده تماس بگیرید</p>
      
      <div class="contact-buttons">
        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $item['seller_phone']); ?>" 
           class="contact-btn whatsapp-btn" 
           target="_blank"
           onclick="logContactAction('whatsapp')">
          📱 واتساپ
        </a>
        <a href="tel:<?php echo htmlspecialchars($item['seller_phone']); ?>" 
           class="contact-btn call-btn"
           onclick="logContactAction('call')">
          📞 تماس: <?php echo htmlspecialchars($item['seller_phone']); ?>
        </a>
      </div>
    </div>
  </div>

  <script>
    function logContactAction(type) {
      // Log contact interaction via AJAX
      fetch('contact_handler.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=log_contact&item_id=<?php echo $item_id; ?>&contact_type=${type}&csrf_token=<?php echo generateCSRFToken(); ?>`
      }).catch(error => {
        console.log('Contact logging failed:', error);
      });
    }
  </script>
</body>
</html>
