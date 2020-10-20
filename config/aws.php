<?php

use Aws\Laravel\AwsServiceProvider;

return [
    'credentials' => [
        'key'    => "AKIAIX5X7LUUELBTZH5A",
        'secret' => "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",
    ],
    'region' => 'us-west-2',
    'version' => 'latest',

    // You can override settings for specific services
    'Ses' => [
        'region' => 'us-west-2',
    ],
];

