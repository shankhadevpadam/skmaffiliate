<?php

namespace App\Services;

use Automattic\WooCommerce\Client;

class WooCommerceService
{
    public Client $client;

    public function __construct()
    {
        $this->client = new Client(
            'https://skmfurniture.test/',
            'ck_cf91c552d3f4934c34e3e4f2040927c926ef093e',
            'cs_238d5991ef37880a9c14c6410f0315db00e4553a',
            [
                'version' => 'wc/v3',
                'timeout' => 60,
            ]
        );
    }
}
