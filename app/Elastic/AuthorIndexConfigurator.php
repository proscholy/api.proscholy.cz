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
            'tokenizer' => [
                "my_tokenizer" => [
                    "type" => "edge_ngram",
                    "max_gram" => 10,
                    "token_chars" => [
                        "letter",
                        "digit"
                    ]
                ]
            ],

            // each analyzer uses custom-defined filters, char_filters and tokenizers from above
            // or pre-defined elastic ones (such as 'standard', 'trim', 'unique', ...)
            // see the Elasticsearch docs for more

            // these analyzers can be referenced in the model mapping (see $mapping in <Model>SearchableTrait.php)

            'analyzer' => [
                'name_analyzer' => [
                    'tokenizer' => 'my_tokenizer',
                    'filter' => [
                        'lowercase',
                        'asciifolding'
                    ]
                ]
            ]
        ]
    ];
}
