<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ZIFA Platform Configuration
    |--------------------------------------------------------------------------
    */

    'name' => 'ZIFA Connect',
    'organization' => 'Zimbabwe Football Association',
    'country' => 'Zimbabwe',
    'currency' => 'USD',
    'timezone' => 'Africa/Harare',

    'registration' => [
        'player' => [
            'fee_usd' => 50,
            'renewal_fee_usd' => 30,
            'required_documents' => ['birth_certificate', 'national_id', 'photo', 'medical'],
        ],
        'club' => [
            'affiliation_fee_usd' => 500,
            'renewal_fee_usd' => 300,
            'required_documents' => ['constitution', 'registration_certificate'],
        ],
        'official' => [
            'fee_usd' => 30,
            'required_documents' => ['national_id', 'photo', 'certificate'],
        ],
        'referee' => [
            'fee_usd' => 50,
            'required_documents' => ['national_id', 'photo', 'fitness_certificate'],
        ],
    ],

    'transfer' => [
        'local_admin_fee_usd' => 100,
        'international_admin_fee_usd' => 500,
        'windows' => [
            'summer' => ['start' => '01-01', 'end' => '01-31'],
            'winter' => ['start' => '07-01', 'end' => '07-31'],
        ],
    ],

    'competition' => [
        'entry_fee_premier_usd' => 1000,
        'entry_fee_division_one_usd' => 500,
        'entry_fee_division_two_usd' => 250,
    ],

    'id_format' => [
        'player' => 'ZFA-P-%06d',
        'club' => 'ZFA-C-%04d',
        'official' => 'ZFA-O-%05d',
        'referee' => 'ZFA-R-%05d',
        'invoice' => 'INV-%Y%m%d-%06d',
        'transfer' => 'TRF-%Y%m%d-%05d',
    ],

    'seasons' => [
        'current' => date('Y'),
        'format' => 'Y', // or 'Y/Y' for split seasons
    ],
];
