<?php
require_once 'database.php';

// Clear all existing data for exact count
function clearAllData() {
    try {
        $pdo = initDatabase();
        $sql = "DELETE FROM items";  // Clear all items for exact 1400 count
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        echo "Cleared all existing data for exact 1400 items\n";
    } catch (Exception $e) {
        echo "Error clearing data: " . $e->getMessage() . "\n";
    }
}

// Categories and their specific item templates
$categories = [
    'vehicles' => [
        'items' => [
            ['title' => 'تویوتا کامری {year}', 'desc' => 'حالت عالی، کیلومتر کم، همه کاغذات کامل', 'price' => [800000, 1200000]],
            ['title' => 'هوندا سیویک {year}', 'desc' => 'موتور بی‌نقص، رنگ {color}، کامل', 'price' => [650000, 950000]],
            ['title' => 'موتور سیکلت {brand}', 'desc' => 'مدل {year}، کم کارکرد، بدون مشکل', 'price' => [45000, 85000]],
            ['title' => 'نیسان پیکاپ {year}', 'desc' => 'مناسب کار تجارتی، موتور عالی', 'price' => [900000, 1400000]],
            ['title' => 'میتسوبیشی L300', 'desc' => 'ون مسافری، راحت و بی‌مصرف', 'price' => [550000, 750000]],
            ['title' => 'دوچرخه کوهستانی', 'desc' => 'دوچرخه مدرن، مناسب جاده و کوهستان', 'price' => [8000, 15000]],
            ['title' => 'ریکشا سه‌چرخ', 'desc' => 'ریکشا مسافری، درآمد روزانه بالا', 'price' => [180000, 280000]],
            ['title' => 'بایسیکل شهری', 'desc' => 'دوچرخه مناسب شهر، سبک و راحت', 'price' => [3500, 6500]],
            ['title' => 'موتور برقی', 'desc' => 'موتور سیکلت برقی، بی‌صدا و اقتصادی', 'price' => [75000, 120000]],
            ['title' => 'جیپ آف رود {year}', 'desc' => 'مناسب راه‌های صعب‌العبور', 'price' => [1100000, 1600000]]
        ]
    ],
    'realestate' => [
        'items' => [
            ['title' => 'آپارتمان {rooms} خوابه', 'desc' => '{area} متری، طبقه {floor}، آسانسور دار', 'price' => [1500000, 3500000]],
            ['title' => 'خانه باغی {area} متری', 'desc' => 'حیاط وسیع، باغچه، محیط آرام', 'price' => [1200000, 2800000]],
            ['title' => 'دوکان تجارتی', 'desc' => 'در بازار مرکزی، موقعیت عالی', 'price' => [800000, 1800000]],
            ['title' => 'زمین مسکونی', 'desc' => '{area} متر، سند دار، قابل ساخت', 'price' => [500000, 1200000]],
            ['title' => 'مغازه {area} متری', 'desc' => 'در خیابان اصلی، مناسب هر کسب', 'price' => [600000, 1400000]],
            ['title' => 'دفتر کار {area} متری', 'desc' => 'مناسب شرکت، لوکیشن مرکزی', 'price' => [400000, 900000]],
            ['title' => 'انبار {area} متری', 'desc' => 'مناسب نگهداری کالا، امن', 'price' => [300000, 700000]],
            ['title' => 'ویلا باغی {rooms} خوابه', 'desc' => 'محیط سرسبز، استخر، مدرن', 'price' => [2500000, 5000000]],
            ['title' => 'کارخانه {area} متری', 'desc' => 'مناسب تولید، مجوز کامل', 'price' => [1800000, 3500000]],
            ['title' => 'اتاق اجاره‌ای', 'desc' => 'مبله، تمیز، نزدیک مرکز شهر', 'price' => [8000, 18000]]
        ]
    ],
    'electronics' => [
        'items' => [
            ['title' => 'گوشی {brand} {model}', 'desc' => '{ram} گیگ رم، {storage} حافظه، دوربین عالی', 'price' => [25000, 85000]],
            ['title' => 'لپ‌تاپ {brand} {model}', 'desc' => '{ram} گیگ رم، {storage} SSD، برای کار و تحصیل', 'price' => [35000, 120000]],
            ['title' => 'تلویزیون {size} اینچی', 'desc' => 'کیفیت {quality}، صدای عالی، ضمانت دار', 'price' => [28000, 95000]],
            ['title' => 'کامپیوتر رومیزی', 'desc' => 'قدرتمند، مناسب گیمینگ و کار', 'price' => [45000, 150000]],
            ['title' => 'تبلت {brand}', 'desc' => '{storage} حافظه، مناسب مطالعه', 'price' => [15000, 45000]],
            ['title' => 'ساعت هوشمند', 'desc' => 'مقاوم آب، مانیتور سلامت', 'price' => [8000, 25000]],
            ['title' => 'هدفون بی‌سیم', 'desc' => 'صدای با کیفیت، باتری بلندمدت', 'price' => [5000, 18000]],
            ['title' => 'شارژر وایرلس', 'desc' => 'شارژ سریع، مناسب همه گوشی‌ها', 'price' => [2500, 8000]],
            ['title' => 'کنسول بازی {brand}', 'desc' => 'با دسته و بازی‌های محبوب', 'price' => [55000, 120000]],
            ['title' => 'دوربین دیجیتال', 'desc' => '{mp} مگاپیکسل، عکس‌های باکیفیت', 'price' => [18000, 75000]]
        ]
    ],
    'jewelry' => [
        'items' => [
            ['title' => 'انگشتر طلا {karat} عیار', 'desc' => 'طراحی زیبا، وزن {weight} گرم', 'price' => [15000, 45000]],
            ['title' => 'گردنبند طلا', 'desc' => 'طراحی مدرن، {karat} عیار', 'price' => [18000, 65000]],
            ['title' => 'دستبند نقره', 'desc' => 'کار دستی، طراحی سنتی', 'price' => [8000, 25000]],
            ['title' => 'گوشواره طلا', 'desc' => 'طراحی ظریف، مناسب مراسم', 'price' => [12000, 35000]],
            ['title' => 'ساعت طلا {brand}', 'desc' => 'ساعت لوکس، ضمانت اصالت', 'price' => [85000, 250000]],
            ['title' => 'سرویس طلا عروس', 'desc' => 'شامل گردنبند، انگشتر و گوشواره', 'price' => [125000, 350000]],
            ['title' => 'زنجیر طلا مردانه', 'desc' => 'طراحی مردانه، {karat} عیار', 'price' => [35000, 85000]],
            ['title' => 'آویز طلا', 'desc' => 'طراحی مذهبی، کیفیت بالا', 'price' => [8500, 22000]],
            ['title' => 'حلقه ازدواج', 'desc' => 'جفت حلقه طلا، طراحی کلاسیک', 'price' => [28000, 75000]],
            ['title' => 'سنگ قیمتی {gem}', 'desc' => 'سنگ اصل، گواهی اصالت دار', 'price' => [45000, 180000]]
        ]
    ],
    'mens-clothes' => [
        'items' => [
            ['title' => 'کت شلوار {brand}', 'desc' => 'کیفیت عالی، سایز {size}، رنگ {color}', 'price' => [8500, 25000]],
            ['title' => 'پیراهن مردانه', 'desc' => 'طراحی مدرن، پارچه با کیفیت', 'price' => [2500, 6500]],
            ['title' => 'شلوار جین {brand}', 'desc' => 'جین اصل، سایز {size}، رنگ {color}', 'price' => [3500, 8500]],
            ['title' => 'کفش مردانه {type}', 'desc' => 'چرم طبیعی، راحت و شیک', 'price' => [4500, 12000]],
            ['title' => 'ژاکت زمستانی', 'desc' => 'گرم و سبک، ضد آب، جیب‌دار', 'price' => [6500, 18000]],
            ['title' => 'تی‌شرت {brand}', 'desc' => 'پنبه خالص، طراحی جذاب', 'price' => [1500, 4500]],
            ['title' => 'کلاه مردانه', 'desc' => 'کلاه شیک، مناسب هر فصل', 'price' => [1200, 3500]],
            ['title' => 'کمربند چرمی', 'desc' => 'چرم اصل، قفل فلزی مقاوم', 'price' => [2200, 6500]],
            ['title' => 'جوراب مردانه', 'desc' => 'بسته ۶ عددی، پنبه خالص', 'price' => [800, 2200]],
            ['title' => 'لباس ورزشی', 'desc' => 'مناسب ورزش، جنس تنفسی', 'price' => [3500, 8500]]
        ]
    ],
    'womens-clothes' => [
        'items' => [
            ['title' => 'لباس مجلسی {color}', 'desc' => 'طراحی زیبا، مناسب مهمانی', 'price' => [8500, 25000]],
            ['title' => 'شال و روسری {brand}', 'desc' => 'پارچه ابریشم، طرح‌های متنوع', 'price' => [2500, 8500]],
            ['title' => 'تونیک {color}', 'desc' => 'راحت و شیک، سایز {size}', 'price' => [3500, 9500]],
            ['title' => 'کفش زنانه {type}', 'desc' => 'چرم طبیعی، پاشنه {heel} سانتی', 'price' => [4500, 15000]],
            ['title' => 'مانتو {season}', 'desc' => 'طراحی مدرن، جنس عالی', 'price' => [6500, 18000]],
            ['title' => 'شلوار زنانه', 'desc' => 'طراحی راحت، رنگ {color}', 'price' => [2800, 7500]],
            ['title' => 'بلوز {color}', 'desc' => 'پارچه نرم، طراحی شیک', 'price' => [2200, 6500]],
            ['title' => 'کیف دستی', 'desc' => 'چرم اصل، جای مناسب وسایل', 'price' => [3500, 12000]],
            ['title' => 'لباس عروسی', 'desc' => 'طراحی منحصربه‌فرد، سایز {size}', 'price' => [45000, 120000]],
            ['title' => 'جواهرات موی', 'desc' => 'گیره و تل زیبا، طلایی رنگ', 'price' => [1500, 4500]]
        ]
    ],
    'kids-clothes' => [
        'items' => [
            ['title' => 'لباس نوزادی', 'desc' => 'نرم و راحت، سایز {age} ماهه', 'price' => [1500, 4500]],
            ['title' => 'لباس پسرانه {age} ساله', 'desc' => 'طراحی شاد، رنگ {color}', 'price' => [2200, 6500]],
            ['title' => 'لباس دخترانه {color}', 'desc' => 'طراحی پرنسسی، سایز {age} ساله', 'price' => [2500, 7500]],
            ['title' => 'کفش بچگانه', 'desc' => 'راحت برای راه رفتن، سایز {size}', 'price' => [2800, 8500]],
            ['title' => 'کلاه بچگانه', 'desc' => 'محافظت از آفتاب، طراحی بامزه', 'price' => [800, 2500]],
            ['title' => 'لباس مدرسه', 'desc' => 'مطابق مقررات، کیفیت عالی', 'price' => [3200, 8500]],
            ['title' => 'لباس خواب بچگانه', 'desc' => 'نرم و گرم، طراحی کارتونی', 'price' => [1800, 4500]],
            ['title' => 'بالاپوش زمستانی', 'desc' => 'گرم و سبک، ضد آب', 'price' => [4500, 12000]],
            ['title' => 'شلوار ورزشی', 'desc' => 'مناسب بازی، جنس کشسان', 'price' => [1500, 4500]],
            ['title' => 'ست لباس {season}', 'desc' => 'ست کامل {items} تکه', 'price' => [3500, 9500]]
        ]
    ],
    'books' => [
        'items' => [
            ['title' => 'کتاب درسی {grade} {subject}', 'desc' => 'کتاب جدید، مطابق برنامه درسی', 'price' => [1500, 4500]],
            ['title' => 'دایره‌المعارف {subject}', 'desc' => 'اطلاعات کامل و جامع', 'price' => [8500, 25000]],
            ['title' => 'کتاب {language}', 'desc' => 'آموزش زبان قدم به قدم', 'price' => [2500, 8500]],
            ['title' => 'کتاب ادبیات {author}', 'desc' => 'شاهکار ادبیات، نسخه اصل', 'price' => [2200, 6500]],
            ['title' => 'کتاب تاریخ افغانستان', 'desc' => 'تاریخ کامل کشور عزیزمان', 'price' => [3500, 9500]],
            ['title' => 'کتاب آموزش {skill}', 'desc' => 'آموزش عملی و کاربردی', 'price' => [2800, 7500]],
            ['title' => 'قرآن کریم', 'desc' => 'خط زیبا، ترجمه و تفسیر', 'price' => [3200, 12000]],
            ['title' => 'کتاب داستان {genre}', 'desc' => 'داستان‌های جذاب و آموزنده', 'price' => [1800, 5500]],
            ['title' => 'کتاب علمی {subject}', 'desc' => 'اطلاعات روز دنیا، مصور', 'price' => [4500, 15000]],
            ['title' => 'مجموعه کتاب {series}', 'desc' => 'سری کامل {volumes} جلدی', 'price' => [12000, 45000]]
        ]
    ],
    'kids' => [
        'items' => [
            ['title' => 'اسباب‌بازی {toy}', 'desc' => 'ایمن و سرگرم‌کننده، سن {age}+', 'price' => [2500, 8500]],
            ['title' => 'دوچرخه کودکان', 'desc' => 'چرخ کمکی، مناسب سن {age}', 'price' => [8500, 25000]],
            ['title' => 'عروسک {type}', 'desc' => 'نرم و قابل شستشو، {size} سانتی', 'price' => [1500, 6500]],
            ['title' => 'بازی فکری {game}', 'desc' => 'تقویت هوش، سن {age}+', 'price' => [2200, 7500]],
            ['title' => 'ماشین بازی {type}', 'desc' => 'ماشین {control}، باتری خور', 'price' => [3500, 12000]],
            ['title' => 'لگو ساختنی {theme}', 'desc' => '{pieces} قطعه، طراحی خلاقانه', 'price' => [4500, 18000]],
            ['title' => 'کیت نقاشی', 'desc' => 'مداد رنگی، کاغذ، راهنما', 'price' => [1800, 5500]],
            ['title' => 'توپ بازی {sport}', 'desc' => 'مناسب کودکان، کیفیت عالی', 'price' => [1200, 3500]],
            ['title' => 'کوله‌پشتی مدرسه', 'desc' => 'طراحی شاد، جای کتاب‌ها', 'price' => [2800, 8500]],
            ['title' => 'بازی الکترونیکی', 'desc' => 'آموزشی و سرگرم‌کننده', 'price' => [5500, 15000]]
        ]
    ],
    'home' => [
        'items' => [
            ['title' => 'مبل راحتی {persons} نفره', 'desc' => 'چوب طبیعی، روکش {material}', 'price' => [25000, 85000]],
            ['title' => 'میز غذاخوری {size}', 'desc' => 'چوب بلوط، {chairs} عدد صندلی', 'price' => [18000, 55000]],
            ['title' => 'یخچال {brand} {size}', 'desc' => 'انرژی A+، فریزر جداگانه', 'price' => [35000, 95000]],
            ['title' => 'ماشین لباسشویی {brand}', 'desc' => '{capacity} کیلو، انرژی A++', 'price' => [28000, 75000]],
            ['title' => 'فرش دستباف {size}', 'desc' => 'طراحی سنتی، رنگ {color}', 'price' => [15000, 65000]],
            ['title' => 'آشپزخانه کابینت', 'desc' => 'MDF ضد آب، طراحی مدرن', 'price' => [45000, 120000]],
            ['title' => 'سرویس خواب {pieces}', 'desc' => 'چوب طبیعی، شامل {items}', 'price' => [35000, 95000]],
            ['title' => 'کولر گازی {brand}', 'desc' => '{btu} BTU، انرژی A++', 'price' => [22000, 65000]],
            ['title' => 'چراغ سقفی LED', 'desc' => 'نور مهتابی، کم مصرف', 'price' => [2500, 8500]],
            ['title' => 'پرده {room}', 'desc' => 'پارچه با کیفیت، رنگ {color}', 'price' => [3500, 12000]]
        ]
    ],
    'jobs' => [
        'items' => [
            ['title' => 'استخدام {job} باتجربه', 'desc' => 'حقوق {salary}، {benefits}', 'price' => [25000, 85000]],
            ['title' => 'کار پاره‌وقت {field}', 'desc' => 'ساعات منعطف، حقوق مناسب', 'price' => [12000, 35000]],
            ['title' => 'فرصت کار در {company}', 'desc' => 'شرکت معتبر، امکانات عالی', 'price' => [35000, 95000]],
            ['title' => 'استخدام راننده {vehicle}', 'desc' => 'گواهی معتبر، تجربه {years} سال', 'price' => [18000, 45000]],
            ['title' => 'کار آنلاین {type}', 'desc' => 'کار از خانه، درآمد ثابت', 'price' => [15000, 55000]],
            ['title' => 'استخدام {skill} کار', 'desc' => 'ابزار کار، حقوق روزانه', 'price' => [8000, 25000]],
            ['title' => 'کار در {industry}', 'desc' => 'محیط کار مدرن، بیمه', 'price' => [28000, 75000]],
            ['title' => 'فروشنده {product}', 'desc' => 'فروش محصولات، پورسانت', 'price' => [15000, 45000]],
            ['title' => 'آموزگار {subject}', 'desc' => 'تدریس خصوصی، انعطاف زمان', 'price' => [20000, 65000]],
            ['title' => 'کار {season} {type}', 'desc' => 'کار فصلی، پرداخت نقدی', 'price' => [10000, 35000]]
        ]
    ],
    'services' => [
        'items' => [
            ['title' => 'تعمیر {device}', 'desc' => 'تعمیر حرفه‌ای، ضمانت کار', 'price' => [2500, 15000]],
            ['title' => 'نصب {system}', 'desc' => 'نصب و راه‌اندازی توسط متخصص', 'price' => [5000, 25000]],
            ['title' => 'خدمات {service}', 'desc' => 'ارائه خدمات با کیفیت', 'price' => [3500, 18000]],
            ['title' => 'تمیزی {type}', 'desc' => 'نظافت کامل، مواد ضدعفونی', 'price' => [2800, 12000]],
            ['title' => 'حمل و نقل {type}', 'desc' => 'حمل ایمن کالا، قیمت مناسب', 'price' => [1500, 8500]],
            ['title' => 'طراحی {design}', 'desc' => 'طراحی حرفه‌ای، ایده خلاق', 'price' => [8500, 35000]],
            ['title' => 'آموزش {skill}', 'desc' => 'آموزش عملی توسط استاد', 'price' => [5500, 25000]],
            ['title' => 'مشاوره {field}', 'desc' => 'مشاوره تخصصی، تجربه بالا', 'price' => [3500, 15000]],
            ['title' => 'خدمات {maintenance}', 'desc' => 'نگهداری و تعمیر دوره‌ای', 'price' => [4500, 22000]],
            ['title' => 'تبلیغات {type}', 'desc' => 'تبلیغ مؤثر، بازخورد تضمینی', 'price' => [6500, 28000]]
        ]
    ],
    'games' => [
        'items' => [
            ['title' => 'بازی {platform} {title}', 'desc' => 'بازی محبوب، گرافیک عالی', 'price' => [3500, 15000]],
            ['title' => 'کنترلر {brand}', 'desc' => 'دسته بازی بی‌سیم، باتری بلند', 'price' => [4500, 18000]],
            ['title' => 'هدست گیمینگ', 'desc' => 'صدای محیطی، میکروفون دار', 'price' => [5500, 25000]],
            ['title' => 'صندلی گیمینگ', 'desc' => 'راحت برای ساعت‌ها بازی', 'price' => [18000, 55000]],
            ['title' => 'میز گیمینگ RGB', 'desc' => 'نور رنگی، کشوی کیبورد', 'price' => [12000, 35000]],
            ['title' => 'کیبورد مکانیکی', 'desc' => 'کلیدهای مکانیکی، نور RGB', 'price' => [8500, 28000]],
            ['title' => 'ماوس گیمینگ', 'desc' => 'دقت بالا، DPI قابل تنظیم', 'price' => [2500, 12000]],
            ['title' => 'مانیتور گیمینگ {size}"', 'desc' => 'رفرش {refresh}Hz، تأخیر کم', 'price' => [25000, 85000]],
            ['title' => 'کارت بازی {type}', 'desc' => 'بازی کارتی سنتی و مدرن', 'price' => [1500, 6500]],
            ['title' => 'پازل {pieces} قطعه', 'desc' => 'پازل چالشی، تصویر {theme}', 'price' => [1800, 8500]]
        ]
    ],
    'sports' => [
        'items' => [
            ['title' => 'توپ {sport}', 'desc' => 'استاندارد بین‌المللی، کیفیت حرفه‌ای', 'price' => [2500, 8500]],
            ['title' => 'کفش ورزشی {brand}', 'desc' => 'مناسب {sport}, سایز {size}', 'price' => [6500, 25000]],
            ['title' => 'دستگاه تناسب اندام', 'desc' => '{equipment}، مناسب خانه', 'price' => [15000, 85000]],
            ['title' => 'لباس ورزشی {sport}', 'desc' => 'جنس تنفسی، طراحی حرفه‌ای', 'price' => [3500, 12000]],
            ['title' => 'وسایل {sport}', 'desc' => 'تجهیزات کامل برای {sport}', 'price' => [5500, 25000]],
            ['title' => 'دوچرخه ورزشی', 'desc' => 'دوچرخه {type}، دنده {gears}', 'price' => [25000, 95000]],
            ['title' => 'کیف ورزشی', 'desc' => 'مناسب باشگاه، جای کفش جدا', 'price' => [2200, 8500]],
            ['title' => 'عضویت باشگاه {duration}', 'desc' => 'دسترسی به تمام امکانات', 'price' => [8500, 35000]],
            ['title' => 'ویتامین و مکمل', 'desc' => 'مکمل ورزشی، تأیید شده', 'price' => [3500, 15000]],
            ['title' => 'ساعت ورزشی {brand}', 'desc' => 'ضربان قلب، GPS دار', 'price' => [12000, 45000]]
        ]
    ]
];

// Provinces with their Persian names
$provinces = [
    'kabul' => 'کابل',
    'herat' => 'هرات', 
    'mazar' => 'مزار شریف',
    'kandahar' => 'قندهار',
    'jalalabad' => 'جلال‌آباد',
    'ghazni' => 'غزنی',
    'bamyan' => 'بامیان',
    'farah' => 'فراه',
    'kunduz' => 'کندز',
    'badakhshan' => 'بدخشان'
];

// Helper arrays for dynamic content
$years = ['۱۴۰۰', '۱۴۰۱', '۱۴۰۲', '۱۴۰۳', '۲۰۲۰', '۲۰۲۱', '۲۰۲۲', '۲۰۲۳'];
$colors = ['سفید', 'مشکی', 'قرمز', 'آبی', 'خاکستری', 'نقره‌ای', 'طلایی'];
$brands = ['Sony', 'Samsung', 'Apple', 'Huawei', 'LG', 'Dell', 'HP', 'Lenovo'];
$sellers = [
    ['name' => 'محمد احمدی', 'phone' => '0701234567'],
    ['name' => 'علی حسینی', 'phone' => '0702345678'], 
    ['name' => 'فاطمه کریمی', 'phone' => '0703456789'],
    ['name' => 'مریم رضایی', 'phone' => '0704567890'],
    ['name' => 'احمد عثمانی', 'phone' => '0705678901'],
    ['name' => 'خدیجه احمدی', 'phone' => '0706789012'],
    ['name' => 'عبدالله قادری', 'phone' => '0707890123'],
    ['name' => 'زینب حکیمی', 'phone' => '0708901234'],
    ['name' => 'یوسف امینی', 'phone' => '0709012345'],
    ['name' => 'آسیه نوری', 'phone' => '0701122334']
];

function getRandomValue($array) {
    return $array[array_rand($array)];
}

function getImageForCategory($category) {
    $imagePath = "images/$category/";
    if (is_dir($imagePath)) {
        $images = array_diff(scandir($imagePath), ['.', '..']);
        if (!empty($images)) {
            return $imagePath . getRandomValue($images);
        }
    }
    return '';
}

function processDynamicContent($text, $category) {
    global $years, $colors, $brands;
    
    $replacements = [
        '{year}' => getRandomValue($years),
        '{color}' => getRandomValue($colors),
        '{brand}' => getRandomValue($brands),
        '{rooms}' => rand(1, 4),
        '{area}' => rand(50, 200),
        '{floor}' => rand(1, 10),
        '{size}' => rand(38, 45),
        '{ram}' => rand(4, 16),
        '{storage}' => rand(64, 512),
        '{karat}' => rand(14, 22),
        '{weight}' => rand(2, 15),
        '{age}' => rand(2, 12),
        '{heel}' => rand(3, 8),
        '{season}' => getRandomValue(['بهاری', 'تابستانی', 'پاییزی', 'زمستانی']),
        '{grade}' => 'کلاس ' . rand(1, 12),
        '{subject}' => getRandomValue(['ریاضی', 'فیزیک', 'شیمی', 'ادبیات', 'تاریخ']),
        '{language}' => getRandomValue(['انگلیسی', 'عربی', 'اردو', 'فارسی']),
        '{author}' => getRandomValue(['حافظ', 'سعدی', 'فردوسی', 'مولانا']),
        '{skill}' => getRandomValue(['کامپیوتر', 'خیاطی', 'آشپزی', 'زبان']),
        '{toy}' => getRandomValue(['ماشین', 'عروسک', 'پازل', 'بلوک']),
        '{type}' => getRandomValue(['کنترلی', 'شارژی', 'کلاسیک']),
        '{game}' => getRandomValue(['شطرنج', 'منچ', 'پازل']),
        '{control}' => getRandomValue(['کنترلی', 'شارژی']),
        '{pieces}' => rand(50, 500),
        '{sport}' => getRandomValue(['فوتبال', 'بسکتبال', 'تنیس']),
        '{persons}' => rand(2, 6),
        '{material}' => getRandomValue(['چرم', 'پارچه', 'مخمل']),
        '{chairs}' => rand(4, 8),
        '{capacity}' => rand(6, 12),
        '{btu}' => rand(12000, 24000),
        '{room}' => getRandomValue(['اتاق خواب', 'پذیرایی', 'آشپزخانه']),
        '{job}' => getRandomValue(['مهندس', 'معلم', 'راننده', 'فروشنده']),
        '{salary}' => number_format(rand(20000, 80000)) . ' افغانی',
        '{benefits}' => getRandomValue(['بیمه', 'پاداش', 'مرخصی', 'ناهار']),
        '{company}' => getRandomValue(['شرکت معتبر', 'موسسه بزرگ', 'کارخانه']),
        '{field}' => getRandomValue(['ساختمان', 'IT', 'فروش', 'خدمات']),
        '{vehicle}' => getRandomValue(['تاکسی', 'کامیون', 'موتور']),
        '{years}' => rand(2, 10),
        '{industry}' => getRandomValue(['تولیدی', 'خدماتی', 'تجاری']),
        '{product}' => getRandomValue(['موبایل', 'لباس', 'غذا', 'کتاب']),
        '{device}' => getRandomValue(['موبایل', 'لپ‌تاپ', 'تلویزیون']),
        '{system}' => getRandomValue(['دوربین', 'آنتن', 'شبکه']),
        '{service}' => getRandomValue(['تمیزکاری', 'نقاشی', 'تعمیرات']),
        '{maintenance}' => getRandomValue(['کولر', 'یخچال', 'ماشین‌لباسشویی']),
        '{design}' => getRandomValue(['وب‌سایت', 'لوگو', 'پوستر']),
        '{platform}' => getRandomValue(['PlayStation', 'Xbox', 'PC']),
        '{title}' => getRandomValue(['FIFA', 'COD', 'GTA']),
        '{refresh}' => getRandomValue(['120', '144', '165']),
        '{theme}' => getRandomValue(['طبیعت', 'شهر', 'حیوانات']),
        '{equipment}' => getRandomValue(['تردمیل', 'دوچرخه ثابت', 'وزنه']),
        '{gears}' => rand(18, 27),
        '{duration}' => getRandomValue(['یک ماهه', 'سه ماهه', 'شش ماهه']),
        '{mp}' => rand(12, 48),
        '{gem}' => getRandomValue(['یاقوت', 'زمرد', 'الماس', 'فیروزه']),
        '{quality}' => getRandomValue(['HD', '4K', 'Full HD']),
        '{items}' => rand(3, 7),
        '{volumes}' => rand(3, 20),
        '{series}' => getRandomValue(['علمی', 'تاریخی', 'ادبی']),
        '{genre}' => getRandomValue(['ماجراجویی', 'عاشقانه', 'جنگی'])
    ];
    
    return str_replace(array_keys($replacements), array_values($replacements), $text);
}

function generateItems() {
    global $categories, $provinces, $sellers;
    
    clearAllData();
    
    $itemsGenerated = 0;
    
    echo "Starting comprehensive data generation...\n";
    echo "Target: " . (count($provinces) * count($categories) * 10) . " items\n\n";
    
    foreach ($provinces as $cityKey => $cityName) {
        echo "Processing city: $cityName ($cityKey)\n";
        
        foreach ($categories as $categoryKey => $categoryData) {
            echo "  Category: $categoryKey - ";
            
            for ($i = 0; $i < 10; $i++) {
                $template = getRandomValue($categoryData['items']);
                $seller = getRandomValue($sellers);
                
                $title = processDynamicContent($template['title'], $categoryKey);
                $description = processDynamicContent($template['desc'], $categoryKey);
                $price = rand($template['price'][0], $template['price'][1]);
                
                $itemData = [
                    'title' => $title,
                    'description' => $description,
                    'price' => $price,
                    'category' => $categoryKey,
                    'city' => $cityKey,
                    'seller_name' => $seller['name'],
                    'seller_phone' => $seller['phone'],
                    'image_path' => getImageForCategory($categoryKey)
                ];
                
                $result = addItem($itemData);
                if ($result) {
                    $itemsGenerated++;
                } else {
                    echo "Error adding item: " . print_r($itemData, true) . "\n";
                }
            }
            echo "10 items added\n";
        }
        echo "Completed $cityName: " . (count($categories) * 10) . " items\n\n";
    }
    
    echo "\nData generation completed!\n";
    echo "Total items generated: $itemsGenerated\n";
    echo "Distribution: " . count($provinces) . " provinces × " . count($categories) . " categories × 10 items each\n";
    
    // Test the data
    testGeneratedData();
}

function testGeneratedData() {
    echo "\n=== Testing Generated Data ===\n";
    
    try {
        $pdo = initDatabase();
        
        // Count total items
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM items");
        $total = $stmt->fetch()['total'];
        echo "Total items in database: $total\n";
        
        // Count by category
        $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM items GROUP BY category ORDER BY category");
        while ($row = $stmt->fetch()) {
            echo "  {$row['category']}: {$row['count']} items\n";
        }
        
        // Count by city
        echo "\nItems by province:\n";
        $stmt = $pdo->query("SELECT city, COUNT(*) as count FROM items GROUP BY city ORDER BY city");
        while ($row = $stmt->fetch()) {
            echo "  {$row['city']}: {$row['count']} items\n";
        }
        
        // Sample items
        echo "\nSample items:\n";
        $stmt = $pdo->query("SELECT title, category, city, price FROM items ORDER BY RANDOM() LIMIT 5");
        while ($row = $stmt->fetch()) {
            echo "  {$row['title']} - {$row['category']} - {$row['city']} - {$row['price']} افغانی\n";
        }
        
    } catch (Exception $e) {
        echo "Error testing data: " . $e->getMessage() . "\n";
    }
}

// Run the generation
if (php_sapi_name() === 'cli') {
    generateItems();
} else {
    echo "This script should be run from command line.\n";
}
?>