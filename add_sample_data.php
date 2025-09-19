<?php
/**
 * Sample Data Generator for Afghanistan Bazaar
 * This script adds realistic sample items to the marketplace database
 */

require_once 'database.php';

echo "Afghanistan Bazaar - Sample Data Generator\n";
echo "==========================================\n\n";

// Sample data for all categories and cities
$sampleItems = [
    // Vehicles
    [
        'title' => 'تویوتا کورولا ۲۰۱۵',
        'description' => 'موتر در حالت عالی، کیلومتر کم، تمام کاغذات موجود. رنگ سفید، موتور ۱.۶ لیتری',
        'price' => 850000,
        'category' => 'vehicles',
        'city' => 'kabul',
        'seller_name' => 'احمد رضا',
        'seller_phone' => '0701234567'
    ],
    [
        'title' => 'موتورسایکل هوندا ۱۲۵',
        'description' => 'موتور سایکل نو خریداری شده، مدل ۲۰۲۳، کم استعمال، بدون خرابی',
        'price' => 65000,
        'category' => 'vehicles',
        'city' => 'herat',
        'seller_name' => 'محمد علی',
        'seller_phone' => '0702345678'
    ],
    [
        'title' => 'نیسان پیکاپ دبل کابین',
        'description' => 'مناسب برای کارهای تجارتی، حالت موتور عالی، تایرهای نو',
        'price' => 1200000,
        'category' => 'vehicles',
        'city' => 'mazar',
        'seller_name' => 'عبدالحکیم',
        'seller_phone' => '0703456789'
    ],

    // Real Estate
    [
        'title' => 'آپارتمان ۳ خوابه در شهرنو',
        'description' => 'آپارتمان ۱۲۰ متری، ۳ خوابه، آشپزخانه مجهز، پارکینگ و آسانسور موجود',
        'price' => 2500000,
        'category' => 'realestate',
        'city' => 'kabul',
        'seller_name' => 'حاج عبدالله',
        'seller_phone' => '0704567890'
    ],
    [
        'title' => 'خانه باغی در قلب هرات',
        'description' => 'خانه ۲۰۰ متری با باغچه، ۴ اتاق خواب، حیاط وسیع، نزدیک به مرکز شهر',
        'price' => 1800000,
        'category' => 'realestate',
        'city' => 'herat',
        'seller_name' => 'میر احمد',
        'seller_phone' => '0705678901'
    ],
    [
        'title' => 'دوکان تجارتی در مرکز شهر',
        'description' => 'دوکان ۵۰ متری در بهترین محل بازار، مناسب برای تجارت',
        'price' => 800000,
        'category' => 'realestate',
        'city' => 'kandahar',
        'seller_name' => 'غلام حیدر',
        'seller_phone' => '0706789012'
    ],

    // Electronics
    [
        'title' => 'آیفون ۱۳ پرو مکس',
        'description' => 'گوشی در حالت عالی، ۲۵۶ گیگابایت، با شارژر اصلی و محافظ صفحه نصب',
        'price' => 45000,
        'category' => 'electronics',
        'city' => 'kabul',
        'seller_name' => 'فریدون',
        'seller_phone' => '0707890123'
    ],
    [
        'title' => 'لپ تاپ ایسوس گیمینگ',
        'description' => 'لپ تاپ قدرتمند برای بازی و کار، پردازنده i7، کارت گرافیک GTX، رم ۱۶ گیگ',
        'price' => 38000,
        'category' => 'electronics',
        'city' => 'mazar',
        'seller_name' => 'نصرالله',
        'seller_phone' => '0708901234'
    ],
    [
        'title' => 'تلویزیون ال ای دی سامسونگ ۵۵ اینچ',
        'description' => 'تلویزیون هوشمند، کیفیت 4K، اینترنت وای فای، مناسب برای خانواده',
        'price' => 28000,
        'category' => 'electronics',
        'city' => 'jalalabad',
        'seller_name' => 'ظاهر شاه',
        'seller_phone' => '0709012345'
    ],

    // Jewelry
    [
        'title' => 'گردنبند طلای ۱۸ عیار',
        'description' => 'گردنبند زیبای زنانه، طلای اصل ۱۸ عیار، وزن ۱۵ گرم، طرح سنتی افغانی',
        'price' => 85000,
        'category' => 'jewelry',
        'city' => 'kabul',
        'seller_name' => 'حاجی محمد',
        'seller_phone' => '0700123456'
    ],
    [
        'title' => 'انگشتر نقره با سنگ عقیق',
        'description' => 'انگشتر مردانه، نقره عیار ۹۲۵، سنگ عقیق یمانی اصل',
        'price' => 12000,
        'category' => 'jewelry',
        'city' => 'herat',
        'seller_name' => 'استاد کریم',
        'seller_phone' => '0701234567'
    ],
    [
        'title' => 'دستبند طلای دخترانه',
        'description' => 'دستبند ظریف برای دختران، طلای ۲۱ عیار، طرح گل و برگ',
        'price' => 35000,
        'category' => 'jewelry',
        'city' => 'ghazni',
        'seller_name' => 'بی بی فاطمه',
        'seller_phone' => '0702345678'
    ],

    // Men's Clothing
    [
        'title' => 'کت و شلوار رسمی',
        'description' => 'کت و شلوار مردانه، پارچه درجه یک، مناسب برای مراسم رسمی',
        'price' => 4500,
        'category' => 'mens-clothes',
        'city' => 'kabul',
        'seller_name' => 'عبدالرشید',
        'seller_phone' => '0703456789'
    ],
    [
        'title' => 'پیراهن سنتی افغانی',
        'description' => 'پیراهن سفید سنتی، پارچه پنبه ای، مناسب برای نماز و مراسم مذهبی',
        'price' => 1200,
        'category' => 'mens-clothes',
        'city' => 'kandahar',
        'seller_name' => 'ملا صاحب',
        'seller_phone' => '0704567890'
    ],
    [
        'title' => 'جاکت چرمی مردانه',
        'description' => 'جاکت چرم طبیعی، مقاوم و گرم، مناسب برای فصل زمستان',
        'price' => 6800,
        'category' => 'mens-clothes',
        'city' => 'bamyan',
        'seller_name' => 'حاجی قربان',
        'seller_phone' => '0705678901'
    ],

    // Women's Clothing
    [
        'title' => 'لباس مجلسی زنانه',
        'description' => 'لباس زیبای مجلسی، رنگ آبی، مناسب برای عروسی و مهمانی',
        'price' => 3500,
        'category' => 'womens-clothes',
        'city' => 'kabul',
        'seller_name' => 'خانم مریم',
        'seller_phone' => '0706789012'
    ],
    [
        'title' => 'چادری کیفیت بالا',
        'description' => 'چادری زنانه، پارچه نرم و راحت، رنگ آبی و مشکی موجود',
        'price' => 800,
        'category' => 'womens-clothes',
        'city' => 'herat',
        'seller_name' => 'بی بی عایشه',
        'seller_phone' => '0707890123'
    ],
    [
        'title' => 'شال و روسری ابریشمی',
        'description' => 'مجموعه شال و روسری، ابریشم اصل، طرح های متنوع و زیبا',
        'price' => 2200,
        'category' => 'womens-clothes',
        'city' => 'farah',
        'seller_name' => 'خانم زهره',
        'seller_phone' => '0708901234'
    ],

    // Kids Clothing
    [
        'title' => 'لباس نوزادی پسرانه',
        'description' => 'ست کامل لباس نوزاد، پنبه ۱۰۰٪، نرم و مناسب برای پوست حساس',
        'price' => 1800,
        'category' => 'kids-clothes',
        'city' => 'kabul',
        'seller_name' => 'خانم صدیقه',
        'seller_phone' => '0709012345'
    ],
    [
        'title' => 'پیراهن دخترانه گلدار',
        'description' => 'پیراهن زیبا برای دختران ۵ تا ۱۰ سال، رنگ صورتی، طرح گل',
        'price' => 950,
        'category' => 'kids-clothes',
        'city' => 'kunduz',
        'seller_name' => 'خانم گلنار',
        'seller_phone' => '0700123456'
    ],
    [
        'title' => 'کفش ورزشی بچگانه',
        'description' => 'کفش راحت برای بازی، سایزهای مختلف، کیفیت عالی',
        'price' => 2400,
        'category' => 'kids-clothes',
        'city' => 'badakhshan',
        'seller_name' => 'شاه محمود',
        'seller_phone' => '0701234567'
    ],

    // Books & Education
    [
        'title' => 'قرآن کریم با ترجمه',
        'description' => 'قرآن مجید با ترجمه فارسی، خط درشت، جلد مقوا، مناسب برای مطالعه',
        'price' => 450,
        'category' => 'books',
        'city' => 'kabul',
        'seller_name' => 'مولوی صاحب',
        'seller_phone' => '0702345678'
    ],
    [
        'title' => 'کتاب تاریخ افغانستان',
        'description' => 'کتاب جامع تاریخ افغانستان، نوشته دکتور محمد حسن کاکر',
        'price' => 800,
        'category' => 'books',
        'city' => 'herat',
        'seller_name' => 'کتابفروش احمد',
        'seller_phone' => '0703456789'
    ],
    [
        'title' => 'کتابهای درسی دبیرستان',
        'description' => 'مجموعه کتابهای درسی کلاس دوازدهم، همه رشته ها موجود',
        'price' => 1200,
        'category' => 'books',
        'city' => 'mazar',
        'seller_name' => 'استاد رحیم',
        'seller_phone' => '0704567890'
    ],

    // Kids Items
    [
        'title' => 'دوچرخه کودکان',
        'description' => 'دوچرخه امن برای کودکان ۵ تا ۱۰ سال، چرخ کمکی موجود',
        'price' => 3200,
        'category' => 'kids',
        'city' => 'kabul',
        'seller_name' => 'فروشگاه بچگانه',
        'seller_phone' => '0705678901'
    ],
    [
        'title' => 'عروسک پولیشی دخترانه',
        'description' => 'عروسک زیبا و نرم، مناسب برای دختران، قابل شستشو',
        'price' => 850,
        'category' => 'kids',
        'city' => 'jalalabad',
        'seller_name' => 'خانم نرگس',
        'seller_phone' => '0706789012'
    ],
    [
        'title' => 'ماشین بازی کنترلی',
        'description' => 'ماشین کنترل از راه دور، باتری شارژی، مناسب برای پسران',
        'price' => 2800,
        'category' => 'kids',
        'city' => 'ghazni',
        'seller_name' => 'دوکان اسباب بازی',
        'seller_phone' => '0707890123'
    ],

    // Home Items
    [
        'title' => 'فرش دستباف افغانی',
        'description' => 'فرش زیبای دستباف، ابعاد ۳×۲ متر، طرح سنتی هرات',
        'price' => 15000,
        'category' => 'home',
        'city' => 'herat',
        'seller_name' => 'استاد قالی باف',
        'seller_phone' => '0708901234'
    ],
    [
        'title' => 'سرویس چای خوری ۶ نفره',
        'description' => 'سرویس چینی زیبا، ۶ نفره، مناسب برای پذیرایی از مهمان',
        'price' => 4200,
        'category' => 'home',
        'city' => 'kabul',
        'seller_name' => 'حاجی نعیم',
        'seller_phone' => '0709012345'
    ],
    [
        'title' => 'یخچال فریزر سامسونگ',
        'description' => 'یخچال ۲ درب، فریزر بالا، کم مصرف، گارانتی موجود',
        'price' => 18500,
        'category' => 'home',
        'city' => 'mazar',
        'seller_name' => 'فروشگاه لوازم خانگی',
        'seller_phone' => '0700123456'
    ],

    // Jobs
    [
        'title' => 'راننده تاکسی مورد نیاز',
        'description' => 'راننده با تجربه برای تاکسی، گواهینامه معتبر، سن بین ۲۵ تا ۴۵ سال',
        'price' => 8000,
        'category' => 'jobs',
        'city' => 'kabul',
        'seller_name' => 'شرکت حمل نقل',
        'seller_phone' => '0701234567'
    ],
    [
        'title' => 'معلم زبان انگلیسی',
        'description' => 'مدرس زبان انگلیسی با مدرک دانشگاهی، تجربه تدریس ۳ سال',
        'price' => 12000,
        'category' => 'jobs',
        'city' => 'herat',
        'seller_name' => 'آموزشگاه زبان',
        'seller_phone' => '0702345678'
    ],
    [
        'title' => 'فروشنده دوکان مواد غذایی',
        'description' => 'فروشنده صادق و کارآمد، آشنا با حسابداری ساده',
        'price' => 6500,
        'category' => 'jobs',
        'city' => 'kandahar',
        'seller_name' => 'سوپرمارکت برکت',
        'seller_phone' => '0703456789'
    ],

    // Services
    [
        'title' => 'تعمیر موبایل و تبلت',
        'description' => 'تعمیر انواع گوشی موبایل، تعویض صفحه، تعمیر برد، خدمات سریع',
        'price' => 500,
        'category' => 'services',
        'city' => 'kabul',
        'seller_name' => 'تعمیرگاه موبایل',
        'seller_phone' => '0704567890'
    ],
    [
        'title' => 'خیاطی زنانه در منزل',
        'description' => 'دوخت انواع لباس زنانه، تعدیل لباس، قیمت مناسب، کار تمیز',
        'price' => 800,
        'category' => 'services',
        'city' => 'herat',
        'seller_name' => 'خانم خیاط',
        'seller_phone' => '0705678901'
    ],
    [
        'title' => 'نقاشی ساختمان',
        'description' => 'نقاشی داخلی و خارجی، رنگ کاری، کار حرفه ای با بهترین مواد',
        'price' => 1200,
        'category' => 'services',
        'city' => 'mazar',
        'seller_name' => 'گروه نقاشان',
        'seller_phone' => '0706789012'
    ],

    // Games
    [
        'title' => 'پلی استیشن ۴ با بازی',
        'description' => 'کنسول PS4 اصل، با ۲ دسته و ۱۰ بازی، کارکرد عالی',
        'price' => 22000,
        'category' => 'games',
        'city' => 'kabul',
        'seller_name' => 'فروشگاه بازی',
        'seller_phone' => '0707890123'
    ],
    [
        'title' => 'شطرنج چوبی دست ساز',
        'description' => 'شطرنج زیبا از چوب گردو، دست ساز، مناسب برای هدیه',
        'price' => 1800,
        'category' => 'games',
        'city' => 'bamyan',
        'seller_name' => 'نجار هنرمند',
        'seller_phone' => '0708901234'
    ],
    [
        'title' => 'تخته نرد سنتی',
        'description' => 'تخته نرد با کیفیت، چوب طبیعی، مهره ها و تاس همراه',
        'price' => 1200,
        'category' => 'games',
        'city' => 'farah',
        'seller_name' => 'صنایع دستی',
        'seller_phone' => '0709012345'
    ],

    // Sports
    [
        'title' => 'توپ فوتبال اصل',
        'description' => 'توپ فوتبال مارک نایک، مناسب برای بازی در زمین چمن',
        'price' => 3500,
        'category' => 'sports',
        'city' => 'kabul',
        'seller_name' => 'فروشگاه ورزشی',
        'seller_phone' => '0700123456'
    ],
    [
        'title' => 'دوچرخه کوهستان',
        'description' => 'دوچرخه مناسب برای کوهستان، ۲۱ سرعته، ترمز دیسکی',
        'price' => 8500,
        'category' => 'sports',
        'city' => 'kunduz',
        'seller_name' => 'ورزش و تفریح',
        'seller_phone' => '0701234567'
    ],
    [
        'title' => 'کیسه بوکس با دستکش',
        'description' => 'کیسه تمرینی بوکس، دستکش و بانداژ همراه، مناسب خانه',
        'price' => 4800,
        'category' => 'sports',
        'city' => 'badakhshan',
        'seller_name' => 'باشگاه ورزشی',
        'seller_phone' => '0702345678'
    ]
];

// Function to add all sample items
function addSampleData() {
    $successCount = 0;
    $failCount = 0;
    
    global $sampleItems;
    
    echo "Adding sample marketplace items...\n\n";
    
    foreach ($sampleItems as $index => $item) {
        echo "Adding item " . ($index + 1) . ": " . $item['title'] . "\n";
        
        $itemId = addItem($item);
        
        if ($itemId) {
            echo "✓ Successfully added with ID: $itemId\n";
            $successCount++;
        } else {
            echo "✗ Failed to add item\n";
            $failCount++;
        }
        
        echo "  Category: " . $item['category'] . " | City: " . $item['city'] . " | Price: " . number_format($item['price']) . " AFN\n";
        echo "  Seller: " . $item['seller_name'] . " | Phone: " . $item['seller_phone'] . "\n\n";
    }
    
    echo "==========================================\n";
    echo "Sample Data Addition Complete!\n";
    echo "Successfully added: $successCount items\n";
    echo "Failed to add: $failCount items\n";
    echo "Total items processed: " . count($sampleItems) . "\n\n";
    
    return $successCount;
}

// Function to show statistics
function showStatistics() {
    echo "Database Statistics:\n";
    echo "-------------------\n";
    
    $totalItems = getItemsCount();
    echo "Total items in database: $totalItems\n\n";
    
    // Show items by category
    $categories = ['vehicles', 'realestate', 'electronics', 'jewelry', 'mens-clothes', 
                  'womens-clothes', 'kids-clothes', 'books', 'kids', 'home', 'jobs', 
                  'services', 'games', 'sports'];
    
    echo "Items by category:\n";
    foreach ($categories as $category) {
        $count = getItemsCount(['category' => $category]);
        echo "  $category: $count items\n";
    }
    
    echo "\n";
    
    // Show items by city
    $cities = ['kabul', 'herat', 'mazar', 'kandahar', 'jalalabad', 'ghazni', 
              'bamyan', 'farah', 'kunduz', 'badakhshan'];
    
    echo "Items by city:\n";
    foreach ($cities as $city) {
        $count = getItemsCount(['city' => $city]);
        echo "  $city: $count items\n";
    }
    
    echo "\n";
}

// Function to show recent items
function showRecentItems($limit = 10) {
    echo "Recent items added:\n";
    echo "------------------\n";
    
    $items = getItems([], $limit, 0);
    
    foreach ($items as $item) {
        echo "ID: {$item['id']} | {$item['title']}\n";
        echo "Category: {$item['category']} | City: {$item['city']} | Price: " . number_format($item['price']) . " AFN\n";
        echo "Added: {$item['created_at']}\n\n";
    }
}

// Main execution
echo "Starting sample data addition process...\n\n";

try {
    // Test database connection first
    echo "Testing database connection...\n";
    $testResults = testDatabase();
    
    $hasErrors = false;
    foreach ($testResults as $test => $result) {
        if (strpos($result, 'ERROR') !== false || strpos($result, 'CRITICAL ERROR') !== false) {
            $hasErrors = true;
            break;
        }
    }
    
    if ($hasErrors) {
        echo "Database test failed! Please check database configuration.\n";
        foreach ($testResults as $test => $result) {
            echo "$test: $result\n";
        }
        exit(1);
    }
    
    echo "✓ Database connection successful!\n\n";
    
    // Add sample data
    $addedCount = addSampleData();
    
    if ($addedCount > 0) {
        echo "Sample data added successfully!\n\n";
        
        // Show statistics
        showStatistics();
        
        // Show some recent items
        showRecentItems(5);
        
        echo "==========================================\n";
        echo "You can now visit the website to see the sample data!\n";
        echo "Use search.php to search through the added items.\n";
        echo "==========================================\n";
    } else {
        echo "No items were added. Please check the error messages above.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Please check your database configuration and try again.\n";
    exit(1);
}

?>