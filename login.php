<?php
require_once 'security.php';
secureSession();
session_start();
initializeSessionSecurity();

// Check for messages
$success_message = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="fa" id="html-element">
<head>
  <meta charset="UTF-8">
  <title>بازار افغانستان | ورود / ثبت‌نام</title>
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
      padding: 30px;
      margin: 50px auto;
      max-width: 400px;
      backdrop-filter: blur(8px);
    }

    .section-box h2 {
      margin-top: 0;
      color: #ffffff;
      font-size: 24px;
      text-align: center;
      border-bottom: 1px solid rgba(255,255,255,0.3);
      padding-bottom: 15px;
      margin-bottom: 25px;
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

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: bold;
      color: #ffffff;
    }

    .form-group input {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 8px;
      background-color: rgba(255,255,255,0.1);
      color: #fff;
      box-sizing: border-box;
    }

    .form-group input::placeholder {
      color: rgba(255,255,255,0.6);
    }

    .form-group input:focus {
      outline: none;
      border-color: #ffffff;
      background-color: rgba(255,255,255,0.2);
    }

    .btn-primary {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      background-color: #ffffff;
      color: #6a11cb;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-weight: bold;
      margin-bottom: 15px;
    }

    .btn-primary:hover {
      background-color: #f0f0f0;
    }

    .toggle-form {
      text-align: center;
      margin-top: 15px;
    }

    .toggle-form a {
      color: #ffffff;
      text-decoration: none;
      font-weight: bold;
    }

    .toggle-form a:hover {
      text-decoration: underline;
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

    @media (max-width: 768px) {
      .section-box {
        margin: 20px;
        padding: 20px;
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
    <button onclick="toggleLanguage()">🌐 <span id="lang-btn">زبان</span></button>
    <button onclick="toggleTheme()">🌙 <span id="theme-btn">حالت</span></button>
  </div>

  <div class="section-box">
    <?php if ($success_message): ?>
      <div class="alert alert-success"><?php echo escapeOutput($success_message); ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
      <div class="alert alert-error"><?php echo escapeOutput($error_message); ?></div>
    <?php endif; ?>

    <!-- ورود -->
    <div id="login-form">
      <h2 id="login-title">ورود به حساب کاربری</h2>
      <form method="POST" action="auth.php" onsubmit="return validateLoginForm()">
        <input type="hidden" name="action" value="login">
        <?php echo getCSRFTokenField(); ?>
        
        <div class="form-group">
          <label for="login-name" id="name-label">نام:</label>
          <input type="text" id="login-name" name="name" placeholder="نام خود را وارد کنید" required>
        </div>
        
        <div class="form-group">
          <label for="login-password" id="password-label">رمز عبور (4 رقم):</label>
          <input type="password" id="login-password" name="password" placeholder="****" maxlength="4" pattern="[0-9]{4}" required>
        </div>
        
        <button type="submit" class="btn-primary" id="login-submit">ورود</button>
      </form>
      
      <div class="toggle-form">
        <a href="#" onclick="toggleForms()" id="show-register">حساب کاربری ندارید؟ ثبت‌نام کنید</a>
      </div>
    </div>

    <!-- ثبت‌نام -->
    <div id="register-form" style="display: none;">
      <h2 id="register-title">ثبت‌نام حساب جدید</h2>
      <form method="POST" action="auth.php" onsubmit="return validateRegisterForm()">
        <input type="hidden" name="action" value="register">
        <?php echo getCSRFTokenField(); ?>
        
        <div class="form-group">
          <label for="register-name" id="reg-name-label">نام:</label>
          <input type="text" id="register-name" name="name" placeholder="نام خود را وارد کنید" required>
        </div>
        
        <div class="form-group">
          <label for="register-password" id="reg-password-label">رمز عبور (4 رقم):</label>
          <input type="password" id="register-password" name="password" placeholder="****" maxlength="4" pattern="[0-9]{4}" required>
        </div>
        
        <div class="form-group">
          <label for="register-phone" id="phone-label">شماره تماس:</label>
          <input type="tel" id="register-phone" name="phone" placeholder="0701234567" required>
        </div>
        
        <button type="submit" class="btn-primary" id="register-submit">ثبت‌نام</button>
      </form>
      
      <div class="toggle-form">
        <a href="#" onclick="toggleForms()" id="show-login">حساب کاربری دارید؟ وارد شوید</a>
      </div>
    </div>
  </div>

  <script>
    let currentLang = 'fa';
    
    const translations = {
      fa: {
        'back-btn': 'بازگشت',
        'home-btn': 'خانه',
        'lang-btn': 'زبان',
        'theme-btn': 'حالت',
        'login-title': 'ورود به حساب کاربری',
        'register-title': 'ثبت‌نام حساب جدید',
        'name-label': 'نام:',
        'password-label': 'رمز عبور (4 رقم):',
        'phone-label': 'شماره تماس:',
        'reg-name-label': 'نام:',
        'reg-password-label': 'رمز عبور (4 رقم):',
        'login-submit': 'ورود',
        'register-submit': 'ثبت‌نام',
        'show-register': 'حساب کاربری ندارید؟ ثبت‌نام کنید',
        'show-login': 'حساب کاربری دارید؟ وارد شوید'
      },
      ps: {
        'back-btn': 'بیرته',
        'home-btn': 'کور',
        'lang-btn': 'ژبه',
        'theme-btn': 'حالت',
        'login-title': 'د حساب کیمنه ننوتل',
        'register-title': 'د نوي حساب نوم‌لیکنه',
        'name-label': 'نوم:',
        'password-label': 'پټ نوم (4 شمیرې):',
        'phone-label': 'د تلیفون شمیره:',
        'reg-name-label': 'نوم:',
        'reg-password-label': 'پټ نوم (4 شمیرې):',
        'login-submit': 'ننوتل',
        'register-submit': 'نوم‌لیکنه',
        'show-register': 'حساب نلرې؟ نوم‌لیکنه وکړه',
        'show-login': 'حساب لرې؟ ننوځه'
      },
      en: {
        'back-btn': 'Back',
        'home-btn': 'Home',
        'lang-btn': 'Language',
        'theme-btn': 'Theme',
        'login-title': 'Login to Account',
        'register-title': 'Register New Account',
        'name-label': 'Name:',
        'password-label': 'Password (4 digits):',
        'phone-label': 'Phone Number:',
        'reg-name-label': 'Name:',
        'reg-password-label': 'Password (4 digits):',
        'login-submit': 'Login',
        'register-submit': 'Register',
        'show-register': "Don't have account? Register",
        'show-login': 'Have account? Login'
      }
    };

    function toggleLanguage() {
      const langs = ['fa', 'ps', 'en'];
      const currentIndex = langs.indexOf(currentLang);
      currentLang = langs[(currentIndex + 1) % langs.length];
      
      const html = document.getElementById('html-element');
      const body = document.body;
      
      html.lang = currentLang;
      
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
      
      // Update placeholders
      updatePlaceholders();
    }

    function updatePlaceholders() {
      const loginName = document.getElementById('login-name');
      const loginPassword = document.getElementById('login-password');
      const registerName = document.getElementById('register-name');
      const registerPassword = document.getElementById('register-password');
      const registerPhone = document.getElementById('register-phone');
      
      if (currentLang === 'fa') {
        loginName.placeholder = 'نام خود را وارد کنید';
        loginPassword.placeholder = '****';
        registerName.placeholder = 'نام خود را وارد کنید';
        registerPassword.placeholder = '****';
        registerPhone.placeholder = '0701234567';
      } else if (currentLang === 'ps') {
        loginName.placeholder = 'خپل نوم وليکئ';
        loginPassword.placeholder = '****';
        registerName.placeholder = 'خپل نوم وليکئ';
        registerPassword.placeholder = '****';
        registerPhone.placeholder = '0701234567';
      } else {
        loginName.placeholder = 'Enter your name';
        loginPassword.placeholder = '****';
        registerName.placeholder = 'Enter your name';
        registerPassword.placeholder = '****';
        registerPhone.placeholder = '0701234567';
      }
    }

    function toggleTheme() {
      document.body.classList.toggle('dark-mode');
    }

    function toggleForms() {
      const loginForm = document.getElementById('login-form');
      const registerForm = document.getElementById('register-form');
      
      if (loginForm.style.display === 'none') {
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
      } else {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
      }
    }

    function validateLoginForm() {
      const name = document.getElementById('login-name').value.trim();
      const password = document.getElementById('login-password').value;
      
      if (!name) {
        alert(currentLang === 'fa' ? 'لطفاً نام را وارد کنید' : currentLang === 'ps' ? 'مهرباني وکړه نوم وليکئ' : 'Please enter name');
        return false;
      }
      
      if (password.length !== 4 || !/^\d{4}$/.test(password)) {
        alert(currentLang === 'fa' ? 'رمز عبور باید 4 رقم باشد' : currentLang === 'ps' ? 'پټ نوم باید 4 شمیرې وي' : 'Password must be 4 digits');
        return false;
      }
      
      return true;
    }

    function validateRegisterForm() {
      const name = document.getElementById('register-name').value.trim();
      const password = document.getElementById('register-password').value;
      const phone = document.getElementById('register-phone').value.trim();
      
      if (!name) {
        alert(currentLang === 'fa' ? 'لطفاً نام را وارد کنید' : currentLang === 'ps' ? 'مهرباني وکړه نوم وليکئ' : 'Please enter name');
        return false;
      }
      
      if (password.length !== 4 || !/^\d{4}$/.test(password)) {
        alert(currentLang === 'fa' ? 'رمز عبور باید 4 رقم باشد' : currentLang === 'ps' ? 'پټ نوم باید 4 شمیرې وي' : 'Password must be 4 digits');
        return false;
      }
      
      if (!phone) {
        alert(currentLang === 'fa' ? 'لطفاً شماره تماس را وارد کنید' : currentLang === 'ps' ? 'مهرباني وکړه د تلیفون شمیره وليکئ' : 'Please enter phone number');
        return false;
      }
      
      return true;
    }
  </script>
</body>
</html>