<?php

return [
    /**
     * The book title.
     */
    'title' => 'Laravel Queues in Action',

    /**
     * The author name.
     */
    'author' => 'Mohamed Said',

    /**
     * The list of fonts to be used in the different themes.
     */
    'fonts' => [
        //        'calibri' => 'Calibri-Regular.ttf',
        //        'times' => 'times-regular.ttf',
    ],

    /**
     * Document Dimensions.
     */
    'document' => [
        'format' => [210, 297],
        'margin_left' => 27,
        'margin_right' => 27,
        'margin_bottom' => 14,
        'margin_top' => 14,
    ],

    /**
     * Cover photo position and dimensions
     */
    'cover' => [
        'position' => 'position: absolute; left:0; right: 0; top: -.2; bottom: 0;',
        'dimensions' => 'width: 210mm; height: 297mm; margin: 0;',
    ],

    /**
     * Location of asset files for book content
     */
    'assets_path' => __DIR__.'/assets',

    /**
     * Location of markdown files for book content
     */
    'content_path' => __DIR__.'/content',

    /**
     * Looking of export folder for built book / sample
     */
    'export_path' => __DIR__.'/export',

    /**
     * Page ranges to be used with the sample command.
     */
    'sample' => [
        [1, 3],
        [10, 18],
    ],

    /**
     * A notice printed at the final page of a generated sample.
     */
    'sample_notice' => 'This is a sample from "Laravel Queues in Action" by Mohamed Said. <br> 
                        For more information, <a href="https://www.learn-laravel-queues.com/">Click here</a>.',

    /**
     * These decorators will run in this order over the HTML content that has been generated during markdown processing.
     * The output of the decorators stack will then be sent over to PDF generation.
     */
    'html_decorators' => [
        new \Ibis\Decorators\BlockQuoteClass('quote'),
        new \Ibis\Decorators\BlockQuoteClassWithPrefix('notice'),
        new \Ibis\Decorators\BlockQuoteClassWithPrefix('warning'),
        new \Ibis\Decorators\PageBreak(),
    ],
];
