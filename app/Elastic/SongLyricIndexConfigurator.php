<?php

namespace App\Elastic;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class SongLyricIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var array
     */
    protected $settings = [
        'analysis' => [
            'filter' => [
                'czech_stop' => [
                    'type' => 'stop',
                    'stopwords' => '_czech_'
                ],
                'czech_stemmer' => [
                    'type' => 'stemmer',
                    'language' => 'czech'
                ],
                'phrases' => [
                    'type' => 'shingle',
                    "min_shingle_size" => 2,
                    "max_shingle_size" => 3
                ],
                'name_edge_ngram' => [
                    "type" => "edge_ngram",
                    "max_gram" => 10,
                    "token_chars" => [
                        "letter",
                        "digit"
                    ]
                ]
            ],
            'char_filter' => [
                'remove_commas' => [
                    'type' => 'mapping',
                    'mappings' => [
                        ', => '
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
            ],

            // each analyzer uses custom-defined filters, char_filters and tokenizers from above
            // or pre-defined elastic ones (such as 'standard', 'trim', 'unique', ...)
            // see the Elasticsearch docs for more

            // these analyzers can be referenced in the model mapping (see $mapping in <Model>SearchableTrait.php)

            'analyzer' => [
                'czech_analyzer' => [
                    'tokenizer' => 'standard',
                    'filter' => [
                        'czech_stemmer',
                        'asciifolding',
                        'lowercase',
                        'czech_stop',
                    ]
                ],
                'name_analyzer' => [
                    'tokenizer' => 'whitespace',
                    'filter' => [
                        'lowercase',
                        'asciifolding',
                        'phrases',
                        'name_edge_ngram',
                        'trim',
                        'unique'
                    ],
                    'char_filter' => [
                        'remove_commas'
                    ]
                ],

                'name_analyzer_as_you_type' => [
                    'tokenizer' => 'whitespace',
                    'filter' => [
                        'lowercase',
                        'asciifolding'
                    ],
                    'char_filter' => [
                        'remove_commas'
                    ]
                ],

                'text_analyzer_phrases' => [
                    'tokenizer' => 'standard',
                    'filter' => [
                        'lowercase',
                        'keyword_repeat',
                        'czech_stemmer',
                        'remove_duplicates',
                        // 'asciifolding',
                        // 'phrases'
                    ]
                ]
            ]
        ]
    ];
}
