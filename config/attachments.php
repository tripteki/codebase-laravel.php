<?php

use App\Models\Attachment;

return [

    "types" => [

        "attachment" => Attachment::class,
    ],

    "min_file_size" => 0,
    "max_file_size" => 102400,
];
