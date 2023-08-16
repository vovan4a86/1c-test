<?php

/**
 * This file is part of bigperson/laravel-exchange1c package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use App\Category;
use App\Product;
use Illuminate\Support\Facades\Log;

return [
    'exchange_path' => '1c_exchange',
    'import_dir'    => public_path('big_person_1c_exchange/'),
    'name'         => 'admin',
    'password'      => 'admin',
    'use_zip'       => false,
    'file_part'     => 0,
    'models'        => [
        \Bigperson\Exchange1C\Interfaces\GroupInterface::class   => Category::class,
        \Bigperson\Exchange1C\Interfaces\ProductInterface::class => Product::class,
//        \Bigperson\Exchange1C\Interfaces\OfferInterface::class   => \App\Models\Offer::class,
    ],
    'log_channel' => 'daily',
    'queue'       => 'default',
    'auth'        => [
        'custom'   => false,
        'callback' => function ($name, $password) {
            if ($name == 'admin' && $password == 'admin') {
                Log::notice('Check user password done');
                return true;
            }

            Log::critical('Check user password false');
            return false;
        },
    ],
];
