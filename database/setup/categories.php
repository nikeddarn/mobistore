<?php

/**
 * Array of base categories tree.
 */

$bue = config('bue');
$features = config('features');

return [
    'title_ru' => 'продукция',
    'title_ua' => 'продукция',
    'title_en' => 'products',
    'folder' => 'products',
    'breadcrumb' => '',

    'children' => [
        [
            'url' => 'screens',
            'breadcrumb' => 'screens',
            'title_en' => 'Screens',
            'title_ru' => 'Дисплеи',
            'title_ua' => 'Дисплеї',
            'page_title_en' => 'Displays and touch screens for smartphones and tablets',
            'page_title_ru' => 'Дисплеи и сенсорные экраны к смартфонам и планшетам',
            'page_title_ua' => 'Дисплеї та сенсорні екрани до смартфонів і планшетів',
            'meta_title_en' => 'Displays, touchscreens for smartphones and tablets in Kiev',
            'meta_title_ru' => 'Дисплеи, сенсорные экраны для смартфонов и планшетов в Киеве',
            'meta_title_ua' => 'Дисплеї, сенсорні екрани для смартфонів і планшетів в Києві',
            'meta_description_en' => 'Displays and touch screens wholesale and retail for smartphones and tablets of any brands:
' . $features . $bue,
            'meta_description_ru' => 'Дисплеи и сенсорные экраны оптом и в розницу для смартфонов и планшетов любых брендов:' . $features . $bue,
            'meta_description_ua' => 'Дисплеї і сенсорні екрани оптом і в роздріб для смартфонів і планшетів будь-яких брендів:' . $features . $bue,
            'meta_keywords_en' => 'Displays for smartphones and tablets, touch screens for smartphones and tablets, buy wholesale and retail in Kiev',
            'meta_keywords_ru' => 'Дисплеи для смартфонов и планшетов, сенсорные экраны для смартфонов и планшетов, купить оптом и в розницу в Киеве',
            'meta_keywords_ua' => 'Дисплеї для смартфонів і планшетів, сенсорні екрани для смартфонів і планшетів, купити оптом і в роздріб в Києві',
            'summary_en' => '<p>The display (touch screen) in a modern smartphone is the main node for interaction with the user. It serves to output visual information and to enter data at the same time. Therefore, his choice should be approached with special care. There are several types of modern displays:</p><ul><li><strong>TFT screens </strong>is obsolete technology. Disadvantages: the need for backlighting, limited contrast.</li><li><strong>OLED screens </strong>do not need backlighting. They have natural black color of image and they are thinner.</li><li><strong>SuperAMOLED screens &mdash; </strong>are the thinnest, they have build-in sensor.</li></ul><p>Possible problems of touch screens exploitation:</p><ul><li>Wiping, cracking or complete destruction of the protective glass or display.</li><li>Ingress of water.</li><li>Screen burn. This is usually considered a factory defect and does not depend on the operating conditions.</li></ul><p>In our store you can buy wholesale and retail modern high quality displays for smartphones and tablets of all famous brands. All of our displays are 100% tested by the manufacturer, the amount of defective screens is minimal.</p>',
            'summary_ru' => '<p>Дисплей (сенсорный экран) в современном смартфоне является основным узлом для взаимодействия с пользователем. Он служит одновременно для вывода визуальной информации и для ввода данных. Поэтому к его выбору нужно подходить с особой тщательностью. Существует несколько типов современных дисплеев:</p><ul><li><strong>TFT экраны &mdash; </strong>устаревшая технология. Недостатки: необходимость подсветки, ограниченная контрастность.</li><li><strong>OLED экраны &mdash; </strong>отсутствие подсветки, натуральный черный цвет, уменьшенная толщина.</li><li><strong>SuperAMOLED экраны &mdash; </strong>самый тонкий, сенсор встроен в дисплей.</li></ul><p>Возможные проблемы при эксплуатации сенсорных экранов:</p><ul><li>Потертости, трещины или полное разрушение защитного стекла либо дисплея.</li><li>Попадание влаги внутрь</li><li>&laquo;Выгорание&raquo; экрана. Обычно это считается заводским браком и не зависит от условий эксплуатации.</li></ul><p>В нашем магазине вы можете приобрести оптом и в розницу современные дисплеи высокого качества для смартфонов и планшетов всех известных торговых марок. Все наши дисплеи проходят 100% проверку у производителя, количество брака минимально.</p>',
            'summary_ua' => '<p> Дисплей (сенсорний екран) в сучасному смартфоні є основним вузлом для взаємодії з користувачем. Він служить одночасно для виведення візуальної інформації і для введення даних. Тому до його вибору потрібно підходити з особливою ретельністю. Існує кілька типів сучасних дисплеїв: </p><ul><li><strong>TFT екрани &mdash; </strong> застаріла технологія. Недоліки: необхідність підсвічування, обмежена контрастність.</li><li><strong>OLED екрани &mdash; </strong>відсутність підсвічування, натуральний чорний колір, зменшена товщина. </li><li><strong>SuperAMOLED екрани &mdash; </strong>найтонший, сенсор вбудований в дисплей. </li></ul> <p>Можливі проблеми при експлуатації сенсорних екранів:</p><ul><li>Потертості, тріщини або повне руйнування захисного скла або дисплея . </li><li>Попадання вологи всередину.</li><li>&laquo;Вигорання&raquo; екрану. Зазвичай це вважається заводським браком і не залежить від умов експлуатації. </li></ul><p>В нашому магазині ви можете придбати оптом і в роздріб сучасні дисплеї високої якості для смартфонів і планшетів всіх відомих торгових марок. Всі наші дисплеї проходять 100% перевірку у виробника, кількість браку мінімальна.</p>',
        ],
//        [
//            'url' => 'touchscreens',
//            'title_en' => 'Touchscreens',
//            'title_ru' => 'Тачскрины',
//            'title_ua' => 'Тачскріни',
//            'meta_title_en' => 'Touchscreens, sensors for smartphones and tablets in Kiev',
//            'meta_title_ru' => 'Тачскрины, сенсоры для смартфонов и планшетов в Киеве',
//            'meta_title_ua' => 'Тачскріни, сенсори для смартфонів і планшетів в Києві',
//            'meta_description_en' => 'Touchscreens and sensors wholesale and retail for smartphones and tablets of any brands:',
//            'meta_description_ru' => 'Тачскрины и сенсоры оптом и в розницу для смартфонов и планшетов любых брендов:',
//            'meta_description_ua' => 'Тачскріни і сенсори оптом і в роздріб для смартфонів і планшетів будь-яких брендів:',
//            'meta_keywords_en' => 'Touchscreens for smartphones and tablets, sensors for smartphones and tablets, buy wholesale and retail in Kiev',
//            'meta_keywords_ru' => 'Тачскрины для смартфонов и планшетов, сенсоры для смартфонов и планшетов, купить оптом и в розницу в киеве',
//            'meta_keywords_ua' => 'Тачскріни для смартфонів і планшетів, сенсори для смартфонів і планшетів, купити оптом і в роздріб в Києві',
//            'summary_en' => '',
//            'summary_ru' => '',
//            'summary_ua' => '',
//        ],
//        [
//            'url' => 'cases',
//            'title_en' => 'Сase parts',
//            'title_ru' => 'Корпусные части',
//            'title_ua' => 'Корпусні частини',
//            'meta_title_en' => '',
//            'meta_title_ru' => '',
//            'meta_title_ua' => '',
//            'meta_description_en' => '',
//            'meta_description_ru' => '',
//            'meta_description_ua' => '',
//            'meta_keywords_en' => '',
//            'meta_keywords_ru' => '',
//            'meta_keywords_ua' => '',
//            'summary_en' => '',
//            'summary_ru' => '',
//            'summary_ua' => '',
//
//            'children' => [
//                [
//                    'title_ru' => 'Полный корпусной набор',
//                    'title_ua' => 'Повний корпусний набір',
//                    'title_en' => 'Full case set',
//                    'folder' => 'case-set',
//                ],
//                [
//                    'title_ru' => 'Задние крышки',
//                    'title_ua' => 'Задні кришки',
//                    'title_en' => 'Rear covers',
//                    'folder' => 'case-rear-covers',
//                ],
//                [
//                    'title_ru' => 'Кнопки',
//                    'title_ua' => 'Кнопки',
//                    'title_en' => 'Buttons',
//                    'folder' => 'case-rear-covers',
//                ],
//                [
//                    'title_ru' => 'Средняя часть',
//                    'title_ua' => 'Середня частина',
//                    'title_en' => 'Middle part',
//                    'folder' => 'case-middle-parts',
//                ],
//                [
//                    'title_ru' => 'Стекла',
//                    'title_ua' => 'Стекла',
//                    'title_en' => 'Glasses',
//                    'folder' => 'case-glasses',
//                ],
//                [
//                    'title_ru' => 'Рамки дисплея',
//                    'title_ua' => 'Рамки дисплея',
//                    'title_en' => 'Display frame',
//                    'folder' => 'case-display-frame',
//                ],
//                [
//                    'title_ru' => 'Заглушки',
//                    'title_ua' => 'Заглушки',
//                    'title_en' => 'Plugs',
//                    'folder' => 'case-plugs',
//                ],
//                [
//                    'title_ru' => 'Винты',
//                    'title_ua' => 'Гвинти',
//                    'title_en' => 'Screws',
//                    'folder' => 'case-screws',
//                ],
//            ],
//        ],
//        [
//            'title_ru' => 'Аккумуляторы',
//            'title_ua' => 'Акумулятори',
//            'title_en' => 'Batteries',
//            'folder' => 'batteries',
//
//            'children' => [
//                [
//                    'title_ru' => 'Аккумуляторные батареи',
//                    'title_ua' => 'Акумуляторні батареї',
//                    'title_en' => 'Rechargeable batteries',
//                    'folder' => 'rechargeable batteries',
//                ],
//                [
//                    'title_ru' => 'Внешние аккумуляторы',
//                    'title_ua' => 'Зовнішні акумулятори',
//                    'title_en' => 'Power-banks',
//                    'folder' => 'power-banks',
//                ],
//            ],
//        ],
//        [
//            'title_ru' => 'Шлейфы',
//            'title_ua' => 'Шлейфи',
//            'title_en' => 'Interfaces',
//            'folder' => 'interfaces',
//        ],
//        [
//            'title_ru' => 'Разъемы',
//            'title_ua' => 'Роз\'єми',
//            'title_en' => 'Сonnectors',
//            'folder' => 'connectors',
//        ],
//        [
//            'title_ru' => 'Джойстики и кнопки',
//            'title_ua' => 'Джойстики та кнопки',
//            'title_en' => 'Joysticks and buttons',
//            'folder' => 'buttons',
//        ],
//        [
//            'title_ru' => 'СИМ коннекторы и лотки',
//            'title_ua' => 'СІМ коннектори і лотки',
//            'title_en' => 'SIM connectors and trays',
//            'folder' => 'sim',
//        ],
//        [
//            'title_ru' => 'Динамики',
//            'title_ua' => 'динаміки',
//            'title_en' => 'Speakers',
//            'folder' => 'speakers',
//
//            'children' => [
//                [
//                    'title_ru' => 'Динамики слуховые',
//                    'title_ua' => 'Динаміки слухові',
//                    'title_en' => 'Ear speakers',
//                    'folder' => 'speakers-ear',
//                ],
//                [
//                    'title_ru' => 'Динамики полифонические',
//                    'title_ua' => 'Динаміки поліфонічні',
//                    'title_en' => 'Polyphonic speakers',
//                    'folder' => 'speakers-polyphonic',
//                ],
//            ],
//        ],
//        [
//            'title_ru' => 'Микрофоны',
//            'title_ua' => 'Мікрофони',
//            'title_en' => 'Microphones',
//            'folder' => 'microphones',
//        ],
//        [
//            'title_ru' => 'Камеры',
//            'title_ua' => 'Камери',
//            'title_en' => 'Cameras',
//            'folder' => 'cameras',
//        ],
//        [
//            'title_ru' => 'Микросхемы и платы',
//            'title_ua' => 'Мікросхеми та плати',
//            'title_en' => 'Microchips and boards',
//            'folder' => 'chips',
//
//            'children' => [
//                [
//                    'title_ru' => 'Материнские платы',
//                    'title_ua' => 'Материнські плати',
//                    'title_en' => 'Mother boards',
//                    'folder' => 'boards',
//                ],
//                [
//                    'title_ru' => 'Контроллер питания',
//                    'title_ua' => 'Контролер живлення',
//                    'title_en' => 'Power controller',
//                    'folder' => 'power-controller-chips',
//                ],
//                [
//                    'title_ru' => 'Контроллер Wi-Fi',
//                    'title_ua' => 'Контролер Wi-Fi',
//                    'title_en' => 'Wi-Fi controller',
//                    'folder' => 'wifi-controller-chips',
//                ],
//                [
//                    'title_ru' => 'Контроллер дисплея',
//                    'title_ua' => 'Контролер дисплея',
//                    'title_en' => 'Display controller',
//                    'folder' => 'display-controller-chips',
//                ],
//                [
//                    'title_ru' => 'Контроллер тачскрина',
//                    'title_ua' => 'контролер тачскріна',
//                    'title_en' => 'Touchscreen controller',
//                    'folder' => 'touchscreen-controller-chips',
//                ],
//                [
//                    'title_ru' => 'Аудио контроллер',
//                    'title_ua' => 'Аудіо контролер',
//                    'title_en' => 'Audio controller',
//                    'folder' => 'audio-controller-chips',
//                ],
//                [
//                    'title_ru' => 'Аудио кодек',
//                    'title_ua' => 'Аудіо кодек',
//                    'title_en' => 'Audio codec',
//                    'folder' => 'audio-codec-chips',
//                ],
//                [
//                    'title_ru' => 'USB контроллер',
//                    'title_ua' => 'USB контролер',
//                    'title_en' => 'USB controller',
//                    'folder' => 'usb-controller-chips',
//                ],
//                [
//                    'title_ru' => 'Контроллер подсветки',
//                    'title_ua' => 'Контролер підсвітки',
//                    'title_en' => 'Backlight controller',
//                    'folder' => 'backlight-controller-chips',
//                ],
//                [
//                    'title_ru' => 'Усилитель мощности',
//                    'title_ua' => 'Підсилювач потужності',
//                    'title_en' => 'Amplifier',
//                    'folder' => 'amplifier-chips',
//                ],
//            ],
//        ],
//        [
//            'title_ru' => 'Аксессуары',
//            'title_ua' => 'Аксессуары',
//            'title_en' => 'Accessory',
//            'folder' => 'accessory',
//
//            'children' => [
//                [
//                    'title_ru' => 'Чехлы',
//                    'title_ua' => 'Чохли',
//                    'title_en' => 'Covers',
//                    'folder' => 'covers',
//                ],
//                [
//                    'title_ru' => 'Защитные пленки и стекла',
//                    'title_ua' => 'Захисні плівки та стекла',
//                    'title_en' => 'Protective sticker and glass',
//                    'folder' => 'sticker',
//                ],
//                [
//                    'title_ru' => 'Кабели, переходники, зарядки',
//                    'title_ua' => 'Кабелі, перехідники, зарядки',
//                    'title_en' => 'Cables, adapters, chargers',
//                    'folder' => 'cables',
//                ],
//                [
//                    'title_ru' => 'наушники',
//                    'title_ua' => 'наушники',
//                    'title_en' => 'headphones',
//                    'folder' => 'headphones',
//                ],
//            ],
//        ],
//        [
//            'title_ru' => 'Гаджеты',
//            'title_ua' => 'Гаджети',
//            'title_en' => 'Gadgets',
//            'folder' => 'gadgets',
//
//            'children' => [
//                [
//                    'title_ru' => 'Фитнес-браслеты',
//                    'title_ua' => 'Фітнес-браслети',
//                    'title_en' => 'Fitness bracelets',
//                    'folder' => 'fitness',
//                ],
//            ],
//        ],
    ],
];