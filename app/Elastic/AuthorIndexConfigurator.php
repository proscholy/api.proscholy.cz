<?php

namespace App\Elastic;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class AuthorIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var array
     */
    protected $settings = [
        'analysis' => [
            'filter' => [
                'czech_stemmer' => [
                    'type' => 'stemmer',
                    'language' => 'czech'
                ],
            ],
            'analyzer' => [
                'name_analyzer' => [
                    'tokenizer' => 'my_tokenizer',
                    'filter' => [
                        'lowercase',
                        'asciifolding'
                    ]
                ]
            ],
            'tokenizer' => [
                "my_tokenizer" => [
                    "type" => "edge_ngram",
                    "max_gram" => 10,
                    "token_chars" => [
                        "letter",
                        "digit"
                    ]
                ]
            ]
        ]
    ];
}
