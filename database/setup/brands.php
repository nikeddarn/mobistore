<?php

/**
 * Array of brands
 */

$bue_en = trans('meta.phrases.bue', [], 'en');
$features_en = trans('meta.phrases.features', [], 'en');
$wholesaleRetail_en = trans('meta.phrases.wholesale_and_retail', [], 'en');
$originalCopy_en = trans('meta.phrases.original_and_copy', [], 'en');

$bue_ru = trans('meta.phrases.bue', [], 'ru');
$features_ru = trans('meta.phrases.features', [], 'ru');
$wholesaleRetail_ru = trans('meta.phrases.wholesale_and_retail', [], 'ru');
$originalCopy_ru = trans('meta.phrases.original_and_copy', [], 'ru');

$bue_ua = trans('meta.phrases.bue', [], 'ua');
$features_ua = trans('meta.phrases.features', [], 'ua');
$wholesaleRetail_ua = trans('meta.phrases.wholesale_and_retail', [], 'ua');
$originalCopy_ua = trans('meta.phrases.original_and_copy', [], 'ua');

return [
    [
        'title' => 'Apple',
        'breadcrumb' => 'apple',
        'url' => 'apple',
        'image' => 'apple.png',
        'priority' => 1,
        'meta_keywords_en' => 'Apple spare parts, iPhone, Ipod, Ipad, Apple Watch',
        'meta_keywords_ru' => 'Запчасти Apple, iPhone, Ipod, Ipad, Apple Watch',
        'meta_keywords_ua' => 'Запчастини Apple, iPhone, Ipod, Ipad, Apple Watch',
        'page_title_en' => 'Spare parts for smartphones and tablets Apple',
        'page_title_ru' => 'Запчасти к смартфонам и планшетам Apple',
        'page_title_ua' => 'Запчастини до смартфонів і планшетів Apple',
        'meta_title_en' => 'Spare parts for smartphones and tablets Apple ' . $wholesaleRetail_en,
        'meta_title_ru' => 'Запчасти к смартфонам и планшетам Apple ' . $wholesaleRetail_ru,
        'meta_title_ua' => 'Запчастини до смартфонів і планшетів Apple ' . $wholesaleRetail_ua,
        'meta_description_en' => 'Spare parts for smartphones and tablets Apple. ' . ucfirst($wholesaleRetail_en). ', ' . $originalCopy_en . '. ' . ucfirst($features_en) . ' &#8212; ' . $bue_en,
        'meta_description_ru' => 'Запчасти к смартфонам и планшетам Apple. ' . ucfirst($wholesaleRetail_ru). ', ' . $originalCopy_ru . '. ' . ucfirst($features_ru) . ' &#8212; ' . $bue_ru,
        'meta_description_ua' => 'Запчастини до смартфонів і планшетів Apple. ' . ucfirst($wholesaleRetail_ua). ', ' . $originalCopy_ua . '. ' . ucfirst($features_ua) . ' &#8212; ' . $bue_ua,
        'summary_en' => '<p>Due to the timely idea, original design and the highest quality of components, Apple\'s smartphones and tablets are in high demand and have become cult. Because of this, you can sell even the outdated Apple in any state.</p><p>However, even mobile devices Apple requires periodic replacement of parts. Mechanical damage to the display, water ingress, damage to the cables, buttons, microphones, speakers, blistering the battery - all this leads to the need to change parts in your smartphone or tablet.</p><p>' . config ('app.name'). ' store offer high-quality spare parts for Apple smartphones and tablets by low prices:</p><ul><li>Displays and touchscreens</li><li>Body parts</li><li>Interfaces, connectors</li><li>Butteries, power banks</li><li>Speakers, microphones, cameras</li><li>Chips, flash memory</li></ul><p>You can choose between the original spare part of Apple or a copy of high quality. Buying from us spare parts for Apple smartphones and tablets wholesale, you will save much money and time.</p>',
        'summary_ru' => '<p>Благодаря своевременной идее, оригинальному дизайну и высочайшему качеству комплектующих смартфоны и планшеты Apple пользуются высоким спросом и стали культовыми. Благодаря этому можно продать даже устаревший Apple в любом состоянии.</p><p>Тем не менее даже мобильные устройства Apple требует периодической замены запчастей. Механическое повреждение дисплея, попадание воды внутрь, повреждение шлейфов, кнопок, микрофонов, динамиков, вздутие аккумулятора - все это приводит к необходимости менять запчасти в вашем смартфоне или планшете.</p><p>Магазин ' . config ('app.name'). ' предлагает высококачественные запчасти к смартфонам и планшетам Apple по низким ценам:</p><ul><li>Дисплеи и тачскрины</li><li>Детали корпуса</li><li>Шлейфы, разъемы, коннекторы</li><li>Аккумуляторные батареи, зарядки, внешние источники питания</li><li>Динамики, микрофоны, камеры</li><li>Микросхемы, флеш память</li></ul><p>Вы можете выбрать между оригинальной запчастью Apple или копией высокого качества. Покупая у нас запасные части к смартфонам и планшетам Apple оптом, вы значительно сэкономите деньги и время.</p>',
        'summary_ua' => '<p>Завдяки своєчасній ідеї, оригінальному дизайну і високій якості комплектуючих смартфони і планшети Apple користуються високим попитом і стали культовими. Завдяки цьому можна продати навіть застарілий Apple в будь-якому стані. </p><p>Проте навіть мобільні пристрої Apple вимагають періодичної заміни запчастин. Механічне пошкодження дисплея, потрапляння води всередину, пошкодження шлейфів, кнопок, мікрофонів, динаміків, здуття акумулятора - все це призводить до необхідності міняти запчастини в вашому смартфоні або планшеті.</p><p>Магазин ' . config ('app.name'). ' пропонує високоякісні запчастини до смартфонів і планшетів Apple по низькими цінами:</p><ul><li>Дисплеї і тачскріни</li><li>Деталі корпусу</li><li>Шлейфи, роз\'єми, коннектор </li><li> Акумуляторні батареї, зарядки, зовнішні джерела живлення</li><li>Динаміки, мікрофони, камери</li><li>Мікросхеми, флеш пам\'ять</li></ul><p>Ви можете вибрати між оригінальною запчастиною Apple або копією високої якості. Купуючи у нас запасні частини до смартфонів і планшетів Apple оптом, ви значно заощадите гроші і час.</p>',
    ],
//    [
//        'title' => 'Acer',
//        'breadcrumb' => 'acer',
//        'url' => 'acer',
//        'image' => '/images/brands/acer.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'Acer smartphone, Acer tablet',
//        'meta_keywords_ru' => 'смартфон Acer, планшет Acer',
//        'meta_keywords_ua' => 'смартфон Acer, планшет Acer',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'Asus',
//        'breadcrumb' => 'asus',
//        'url' => 'asus',
//        'image' => '/images/brands/asus.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone Asus, tablet Asus',
//        'meta_keywords_ru' => 'смартфон Asus, планшет Asus',
//        'meta_keywords_ua' => 'смартфон Asus, планшет Asus',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'BlackBerry',
//        'breadcrumb' => 'blackberry',
//        'url' => 'blackberry',
//        'image' => '/images/brands/blackberry.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'BlackBerry smartphone, BlackBerry tablet',
//        'meta_keywords_ru' => 'смартфон BlackBerry, планшет BlackBerry',
//        'meta_keywords_ua' => 'смартфон BlackBerry, планшет BlackBerry',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'Fly',
//        'breadcrumb' => 'fly',
//        'url' => 'fly',
//        'image' => '/images/brands/fly.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone Fly, tablet Fly',
//        'meta_keywords_ru' => 'смартфон Fly, планшет Fly',
//        'meta_keywords_ua' => 'смартфон Fly, планшет Fly',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'HTC',
//        'breadcrumb' => 'htc',
//        'url' => 'htc',
//        'image' => '/images/brands/htc.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone HTC, tablet HTC',
//        'meta_keywords_ru' => 'смартфон HTC, планшет HTC',
//        'meta_keywords_ua' => 'смартфон HTC, планшет HTC',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'Huawei',
//        'breadcrumb' => 'huawei',
//        'url' => 'huawei',
//        'image' => '/images/brands/huawei.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone Huawei, tablet Huawei',
//        'meta_keywords_ru' => 'смартфон Huawei, планшет Huawei',
//        'meta_keywords_ua' => 'смартфон Huawei, планшет Huawei',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
    [
        'title' => 'Lenovo',
        'breadcrumb' => 'lenovo',
        'url' => 'lenovo',
        'image' => 'lenovo.png',
        'priority' => 2,
        'meta_keywords_en' => 'smartphone Lenovo, tablet Lenovo',
        'meta_keywords_ru' => 'смартфон Lenovo, планшет Lenovo',
        'meta_keywords_ua' => 'смартфон Lenovo, планшет Lenovo',
        'page_title_en' => 'Spare parts for smartphones and tablets Lenovo',
        'page_title_ru' => 'Запчасти к смартфонам и планшетам Lenovo',
        'page_title_ua' => 'Запчастини до смартфонів і планшетів Lenovo',
        'meta_title_en' => 'Spare parts for smartphones and tablets Lenovo ' . $wholesaleRetail_en,
        'meta_title_ru' => 'Запчасти к смартфонам и планшетам Lenovo ' . $wholesaleRetail_ru,
        'meta_title_ua' => 'Запчастини до смартфонів і планшетів Lenovo ' . $wholesaleRetail_ua,
        'meta_description_en' => 'Spare parts for smartphones and tablets Lenovo. ' . ucfirst($wholesaleRetail_en). ', ' . $originalCopy_en . '. ' . ucfirst($features_en) . ' &#8212; ' . $bue_en,
        'meta_description_ru' => 'Запчасти к смартфонам и планшетам Lenovo. ' . ucfirst($wholesaleRetail_ru). ', ' . $originalCopy_ru . '. ' . ucfirst($features_ru) . ' &#8212; ' . $bue_ru,
        'meta_description_ua' => 'Запчастини до смартфонів і планшетів Lenovo. ' . ucfirst($wholesaleRetail_ua). ', ' . $originalCopy_ua . '. ' . ucfirst($features_ua) . ' &#8212; ' . $bue_ua,
        'summary_en' => '<p>Lenovo has been on the market for more than 30 years. During this time, Lenovo captured 20-30% of the market of computers, smartphones and tablets. Lenovo Group Limited products are characterized by good quality of products, wide possibilities of hardware and software, and at an affordable price.</p><p>Thanks to a good price / quality ratio, Lenovo smartphones and tablets have become very popular in Ukraine.</p><p>When choosing parts for mobile phones and tablets Lenovo should pay attention to some features. Each model can differ not only in the size of the display, but in its interface. Buy displays and other spare parts only for the appropriate model of a smartphone or a Lenovo tablet.</p><p> '. config ('app.name'). ' offers parts for the most advanced models of mobile devices Lenovo. You can buy parts separately or accessories in the assembly.</p>',
        'summary_ru' => '<p>Компания Lenovo на рынке уже более 30 лет. За это время Lenovo захватила 20-30% рынка компьютеров, смартфонов и планшетов. Продукция Lenovo Group Limited отличается хорошим качеством изделий, широкими возможностями аппаратно-программного обеспечения, и при этом имеет приемлемую цену.</p><p>Благодаря хорошему соотношению цена / качество смартфоны и планшеты Lenovo получили большое распространение в Украине.</p><p>При выборе запчастей к мобильным телефонам и планшетам Lenovo следует обращать внимание на некоторые особенности. Каждая модель может различаться не только размером дисплея, а и его интерфейсом. Покупайте дисплеи и другие запчасти только к соответствующей модели смартфона или планшета Lenovo.</p><p>' . config ('app.name'). ' предлагает запчасти к наиболее ходовым моделям мобильных устройств Lenovo. Вы можете купить запчасти по отдельности или комплектующие в сборе.</p>',
        'summary_ua' => '<p>Компанія Lenovo на ринку вже більше 30 років. За цей час Lenovo захопила 20-30% ринку комп\'ютерів, смартфонів і планшетів. Продукція Lenovo Group Limited відрізняється гарною якістю виробів, широкими можливостями апаратно-програмного забезпечення, і при цьому має прийнятну ціну.</p><p>Завдяки оптимальному співвідношенню ціна / якість смартфони та планшети Lenovo отримали велике поширення в Україні.</p><p>При виборі запчастин до мобільних телефонів і планшетів Lenovo слід звертати увагу на деякі особливості. Кожна модель може відрізнятися не тільки розміром дисплея, а й його інтерфейсом. Купуйте дисплеї та інші запчастини тільки до відповідної моделі смартфона або планшета Lenovo.</p><p>'. config ( 'app.name'). ' пропонує запчастини до найбільш ходових моделей мобільних пристроїв Lenovo. Ви можете купити запчастини окремо або комплектуючі в зборі.</p>',
    ],
//    [
//        'title' => 'LG',
//        'breadcrumb' => 'lg',
//        'url' => 'lg',
//        'image' => '/images/brands/lg.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone LG, tablet LG',
//        'meta_keywords_ru' => 'смартфон LG, планшет LG',
//        'meta_keywords_ua' => 'смартфон LG, планшет LG',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'Meizu',
//        'breadcrumb' => 'meizu',
//        'url' => 'meizu',
//        'image' => '/images/brands/meizu.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone Meizu, tablet Meizu',
//        'meta_keywords_ru' => 'смартфон Meizu, планшет Meizu',
//        'meta_keywords_ua' => 'смартфон Meizu, планшет Meizu',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'Motorola',
//        'breadcrumb' => 'motorola',
//        'url' => 'motorola',
//        'image' => '/images/brands/motorola.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone Motorola, tablet Motorola',
//        'meta_keywords_ru' => 'смартфон Motorola, планшет Motorola',
//        'meta_keywords_ua' => 'смартфон Motorola, планшет Motorola',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'Nokia',
//        'breadcrumb' => 'nokia',
//        'url' => 'nokia',
//        'image' => '/images/brands/nokia.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone Nokia, tablet Nokia',
//        'meta_keywords_ru' => 'смартфон Nokia, планшет Nokia',
//        'meta_keywords_ua' => 'смартфон Nokia, планшет Nokia',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'OnePlus',
//        'breadcrumb' => 'oneplus',
//        'url' => 'oneplus',
//        'image' => '/images/brands/oneplus.jpg',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone OnePlus, tablet OnePlus',
//        'meta_keywords_ru' => 'смартфон OnePlus, планшет OnePlus',
//        'meta_keywords_ua' => 'смартфон OnePlus, планшет OnePlus',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'OPPO',
//        'breadcrumb' => 'oppo',
//        'url' => 'oppo',
//        'image' => '/images/brands/oppo.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone OPPO, tablet OPPO',
//        'meta_keywords_ru' => 'смартфон OPPO, планшет OPPO',
//        'meta_keywords_ua' => 'смартфон OPPO, планшет OPPO',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
    [
        'title' => 'Samsung',
        'breadcrumb' => 'samsung',
        'url' => 'samsung',
        'image' => 'samsung.png',
        'priority' => 2,
        'meta_keywords_en' => 'Samsung spare parts, components for smartphones and tablets Samsung',
        'meta_keywords_ru' => 'Запчасти Apple, компоненты к смартфонам и планшетам Samsung',
        'meta_keywords_ua' => 'Запчастини Apple, компоненти до смартфонів і планшетів Samsung',
        'page_title_en' => 'Spare parts for smartphones and tablets Samsung',
        'page_title_ru' => 'Запчасти к смартфонам и планшетам Samsung',
        'page_title_ua' => 'Запчастини до смартфонів і планшетів Samsung',
        'meta_title_en' => 'Spare parts for smartphones and tablets Samsung ' . $wholesaleRetail_en,
        'meta_title_ru' => 'Запчасти к смартфонам и планшетам Samsung ' . $wholesaleRetail_ru,
        'meta_title_ua' => 'Запчастини до смартфонів і планшетів Samsung ' . $wholesaleRetail_ua,
        'meta_description_en' => 'Spare parts for smartphones and tablets Samsung. ' . ucfirst($wholesaleRetail_en). ', ' . $originalCopy_en . '. ' . ucfirst($features_en) . ' &#8212; ' . $bue_en,
        'meta_description_ru' => 'Запчасти к смартфонам и планшетам Samsung. ' . ucfirst($wholesaleRetail_ru). ', ' . $originalCopy_ru . '. ' . ucfirst($features_ru) . ' &#8212; ' . $bue_ru,
        'meta_description_ua' => 'Запчастини до смартфонів і планшетів Samsung. ' . ucfirst($wholesaleRetail_ua). ', ' . $originalCopy_ua . '. ' . ucfirst($features_ua) . ' &#8212; ' . $bue_ua,
        'summary_en' => '<p>Samsung is one of the leaders in the market of mobile phones and tablets. The high quality of smartphones allowed us to capture a significant part of the market.</p><p>In the process of long-term operation, even the most high-quality spare parts break down. Constantly beating screens and touchscreens. Clogged with garbage speakers, microphones and cameras, broken buttons, scratched and bend the case.</p><p>Shop '. config ('app.name'). ' offers you branded spare parts for smartphones and tablets Samsung. If there is a desire to save money, you can buy and install the original spare part removed from the broken phone or purchase a copy of the highest quality. At us you will find components in gathering and spare parts separately to the majority of running models of mobile devices Samsung. Details can be purchased wholesale and retail. Wholesale purchase and supply of spare parts for Samsung devices on request is the best solution for service centers and other regular customers.</p>',
        'summary_ru' => '<p>Samsung - один из лидеров рынка мобильных телефонов и планшетов. Высокое качество смартфонов позволило захватить значительную часть рынка.</p><p>В процессе длительной эксплуатации ломаются даже самые качественные запчасти. Постоянно бьются экраны и тачскрины. Забиваются мусором динамики, микрофоны и камеры, ломаются кнопки, царапаются и гнутся корпуса.</p><p>Магазин '. config ( 'app.name'). ' предлагает вам фирменные запасные части к смартфонам и планшетам Samsung. Если есть желание сэкономить, можно купить и установить оригинальную запчасть, снятую с поломанного телефона или приобрести копию высшего качества. У нас вы найдете компоненты в сборе и запчасти отдельно к большинству ходовых моделей мобильных устройств Samsung. Детали можно приобрести оптом и в розницу. Оптовая покупка и поставка запчастей к устройствам Samsung под заказ - лучшее решение для сервисных центров и других постоянных покупателей.</p>',
        'summary_ua' => '<p>Samsung - один з лідерів ринку мобільних телефонів і планшетів. Висока якість смартфонів дозволило захопити значну частину ринку.</p><p>В процесі тривалої експлуатації ламаються навіть найякісніші запчастини. Постійно б\'ються екрани і тачскріни. Забиваються сміттям динаміки, мікрофони і камери, ламаються кнопки, дряпаються і гнуться корпуса.</p><p>Магазин '. config ( 'app.name'). ' пропонує вам фірмові запасні частини до смартфонів і планшетів Samsung. Якщо є бажання заощадити, можна купити і встановити оригінальну запчастину, зняту з поламаного телефону або придбати копію екстра-класу. У нас ви знайдете зібрані компоненти і запчастини окремо до більшості ходових моделей мобільних пристроїв Samsung. Деталі можна придбати оптом і в роздріб. Оптова купівля і постачання запчастин до пристроїв Samsung під замовлення - краще рішення для сервісних центрів та інших постійних покупців.</p>',
    ],
//    [
//        'title' => 'Sony',
//        'breadcrumb' => 'sony',
//        'url' => 'sony',
//        'image' => '/images/brands/sony.png',
//        'meta_keywords_en' => 'smartphone Sony, tablet Sony',
//        'meta_keywords_ru' => 'смартфон Sony, планшет Sony',
//        'meta_keywords_ua' => 'смартфон Sony, планшет Sony',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'Sony Ericsson',
//        'breadcrumb' => 'sony-ericsson',
//        'url' => 'sony-ericsson',
//        'image' => '/images/brands/sony-ericsson.jpg',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone Sony Ericsson, tablet Sony Ericsson',
//        'meta_keywords_ru' => 'смартфон Sony Ericsson, планшет Sony Ericsson',
//        'meta_keywords_ua' => 'смартфон Sony Ericsson, планшет Sony Ericsson',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'THL',
//        'breadcrumb' => 'thl',
//        'url' => 'thl',
//        'image' => '/images/brands/thl.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone THL, tablet THL',
//        'meta_keywords_ru' => 'смартфон THL, планшет THL',
//        'meta_keywords_ua' => 'смартфон THL, планшет THL',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'Xiaomi',
//        'breadcrumb' => 'xiaomi',
//        'url' => 'xiaomi',
//        'image' => '/images/brands/xiaomi.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone Xiaomi, tablet Xiaomi',
//        'meta_keywords_ru' => 'смартфон Xiaomi, планшет Xiaomi',
//        'meta_keywords_ua' => 'смартфон Xiaomi, планшет Xiaomi',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'Zopo',
//        'breadcrumb' => 'zopo',
//        'url' => 'zopo',
//        'image' => '/images/brands/zopo.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone Zopo, tablet Zopo',
//        'meta_keywords_ru' => 'смартфон Zopo, планшет Zopo',
//        'meta_keywords_ua' => 'смартфон Zopo, планшет Zopo',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
//    [
//        'title' => 'ZTE',
//        'breadcrumb' => 'zte',
//        'url' => 'zte',
//        'image' => '/images/brands/zte.png',
//        'priority' => 2,
//        'meta_keywords_en' => 'smartphone ZTE, tablet ZTE',
//        'meta_keywords_ru' => 'смартфон ZTE, планшет ZTE',
//        'meta_keywords_ua' => 'смартфон ZTE, планшет ZTE',
//        'page_title_en' => '',
//        'page_title_ru' => '',
//        'page_title_ua' => '',
//        'meta_title_en' => '',
//        'meta_title_ru' => '',
//        'meta_title_ua' => '',
//        'meta_description_en' => '',
//        'meta_description_ru' => '',
//        'meta_description_ua' => '',
//        'summary_en' => '',
//        'summary_ru' => '',
//        'summary_ua' => '',
//    ],
];