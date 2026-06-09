<?php

use Maatwebsite\Excel\Excel;

return [

    /*
    |--------------------------------------------------------------------------
    | Exports
    |--------------------------------------------------------------------------
    */

    "exports" => [

        "chunk_size" => 1000,
        "pre_calculate_formulas" => false,
        "strict_null_comparison" => false,

        /*
        |--------------------------------------------------------------------------
        | CSV Settings
        |--------------------------------------------------------------------------
        */

        "csv" => [

            "delimiter" => ",",
            "enclosure" => '"',
            "line_ending" => PHP_EOL,
            "use_bom" => false,
            "include_separator_line" => false,
            "excel_compatibility" => false,
            "output_encoding" => "",
            "test_auto_detect" => true,
        ],

        /*
        |--------------------------------------------------------------------------
        | Worksheet Properties
        |--------------------------------------------------------------------------
        */

        "properties" => [

            "creator" => "",
            "lastModifiedBy" => "",
            "title" => "",
            "description" => "",
            "subject" => "",
            "keywords" => "",
            "category" => "",
            "manager" => "",
            "company" => "",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Imports
    |--------------------------------------------------------------------------
    */

    "imports" => [

        "read_only" => true,
        "ignore_empty" => false,

        /*
        |--------------------------------------------------------------------------
        | Heading Row Formatter
        |--------------------------------------------------------------------------
        */

        "heading_row" => [

            "formatter" => "slug",
        ],

        /*
        |--------------------------------------------------------------------------
        | CSV Settings
        |--------------------------------------------------------------------------
        */

        "csv" => [

            "delimiter" => null,
            "enclosure" => '"',
            "escape_character" => "\\",
            "contiguous" => false,
            "input_encoding" => "UTF-8",
        ],

        /*
        |--------------------------------------------------------------------------
        | Worksheet Properties
        |--------------------------------------------------------------------------
        */

        "properties" => [

            "creator" => "",
            "lastModifiedBy" => "",
            "title" => "",
            "description" => "",
            "subject" => "",
            "keywords" => "",
            "category" => "",
            "manager" => "",
            "company" => "",
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Extension Detector
    |--------------------------------------------------------------------------
    */

    "extension_detector" => [

        "xlsx" => Excel::XLSX,
        "xlsm" => Excel::XLSX,
        "xltx" => Excel::XLSX,
        "xltm" => Excel::XLSX,
        "xls" => Excel::XLS,
        "xlt" => Excel::XLS,
        "ods" => Excel::ODS,
        "ots" => Excel::ODS,
        "slk" => Excel::SLK,
        "xml" => Excel::XML,
        "htm" => Excel::HTML,
        "html" => Excel::HTML,
        "csv" => Excel::CSV,
        "tsv" => Excel::TSV,
        "pdf" => Excel::DOMPDF,
        "gnumeric" => Excel::GNUMERIC,
    ],

    /*
    |--------------------------------------------------------------------------
    | Value Binder
    |--------------------------------------------------------------------------
    */

    "value_binder" => [

        "default" => Maatwebsite\Excel\DefaultValueBinder::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    */

    "cache" => [

        "driver" => "memory",

        "batch" => [

            "memory_limit" => 60000,
        ],

        "illuminate" => [

            "store" => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Handler
    |--------------------------------------------------------------------------
    */

    "transactions" => [

        "handler" => "db",

        "db" => [

            "connection" => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Temporary Files
    |--------------------------------------------------------------------------
    */

    "temporary_files" => [

        "local_path" => storage_path("framework/cache/import-export"),
        "remote_disk" => null,
        "remote_prefix" => null,
        "force_resync_remote" => null,
    ],

];
