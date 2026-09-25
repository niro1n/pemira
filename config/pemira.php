<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Official Committee Contact Persons (PEMIRA PNB)
    |--------------------------------------------------------------------------
    |
    | Official contacts for Ketua Panitia (Diana) and Humas (Sintya).
    |
    */
    'contacts' => [
        'ketua_panitia' => [
            'name' => 'Diana',
            'role' => 'Ketua Panitia',
            'phone' => '+62 897-0898-383',
            'clean_phone' => '628970898383',
            'whatsapp_number' => env('KETUA_PANITIA_WHATSAPP', '628970898383'),
        ],
        'humas' => [
            'name' => 'Sintya',
            'role' => 'Humas',
            'phone' => '+62 813-3753-4761',
            'clean_phone' => '6281337534761',
            'whatsapp_number' => env('HUMAS_WHATSAPP', '6281337534761'),
        ],
    ],
];
