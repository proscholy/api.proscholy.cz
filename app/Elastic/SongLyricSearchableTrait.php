<?php

namespace App\Elastic;

use ScoutElastic\Searchable;

use Illuminate\Support\Arr;

trait SongLyricSearchableTrait
{
    use Searchable;

    protected $indexConfigurator = SongLyricIndexConfigurator::class;

    // the Elasticsearch metadata for attributes retrieved by toSearchableArray()
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'search_as_you_type',
                'analyzer' => 'name_analyzer_as_you_type',
            ],
            'lyrics' => [
                'type' => 'text',
                'analyzer' => 'czech_analyzer'
            ],
            'authors' => [
                'type' => 'text',
                'analyzer' => 'name_analyzer'
            ],
            'songook_records' => [
                'type' => 'nested',
                'properties' => [
                    'songbook_id' => [
                        'type' => 'keyword'
                    ],
                    'songbook_number' => [
                        'type' => 'keyword'
                    ],
                    'songbook_number_integer' => [
                        'type' => 'integer'
                    ],
                    'songbook_full_number' => [
                        'type' => 'keyword'
                    ]
                ]
            ],
            'tag_ids' => [
                'type' => 'keyword'
            ],
            'lang' => [
                'type' => 'keyword'
            ],
            // the 'text' type cannot be used for sorting, this is why a copy of name is included
            'name_keyword' => [
                'type' => 'keyword'
            ],
            'is_arrangement' => [
                'type' => 'boolean'
            ],
            'tag_instrumentation_ids' => [
                'type' => 'keyword'
            ],
            'tag_period_ids' => [
                'type' => 'keyword'
            ],
            'song_number' => [
                'type' => 'keyword'
            ],
            'song_number_integer' => [
                'type' => 'integer'
            ],
            'only_regenschori' => [
                'type' => 'boolean'
            ],
            'has_media_files_externals' => [
                'type' => 'boolean'
            ],
            'has_score_files_externals' => [
                'type' => 'boolean'
            ],
            'has_lyrics' => [
                'type' => 'boolean'
            ],
            'has_chords' => [
                'type' => 'boolean'
            ]
        ]
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray()
    {
        $songbook_records = $this->songbook_records()->get()->map(function ($sb) {
            return [
                'songbook_id' => $sb->id,
                'songbook_number' => $sb->pivot->number,
                'songbook_number_integer' => (int)preg_replace('/\D/', '', $sb->pivot->number),
                'songbook_full_number' => [$sb->pivot->songbook->shortcut . $sb->pivot->number, $sb->pivot->songbook->shortcut . ' ' . $sb->pivot->number],
            ];
        });

        $all_authors = $this->authors()->with('memberships')->get();
        foreach ($all_authors as $author) {
            $all_authors = $all_authors->concat($author->memberships);
        }

        // get all song's tags
        $tag_ids = $this->tags()->select('tags.id')->get()->pluck('id');

        $interestingExternals = $this->externals()
            ->with('tags')
            ->whereHas(
                'tags',
                function ($q) {
                    return $q->instrumentation();
                }
            )
            ->get();

        $interestingFiles = $this->files()
            ->with('tags')
            ->whereHas(
                'tags',
                function ($q) {
                    return $q->instrumentation();
                }
            )
            ->get();

        // adjoin externals' instrumentation tags
        foreach ($interestingExternals as $external) {
            $tag_ids = $tag_ids->concat(
                $external->tags()->instrumentation()->select('id')->get()->pluck('id')
            );
        }

        $names = [$this->name];
        if ($this->secondary_name_1) $names[] = $this->secondary_name_1;
        if ($this->secondary_name_2) $names[] = $this->secondary_name_2;

        $arr = [
            'name' => $names,
            // name_keyword is used for sorting
            'name_keyword' => $this->name,
            'song_number' => $this->song_number,
            'song_number_integer' => (int)$this->song_number,
            'lyrics' => $this->lyrics_no_chords_no_comments,
            'authors' => $all_authors->pluck('name'),
            'songbook_records' => $songbook_records,
            'lang' => $this->lang,
            'is_arrangement' => $this->is_arrangement,
            'only_regenschori' => (bool)$this->only_regenschori,
            'tag_ids' => $tag_ids,
            'has_media_files_externals' => $this->externals()->media()->count() + $this->files()->audio()->count() > 0,
            'has_score_files_externals' => $this->scoreExternals()->count() + $this->scoreFiles()->count() > 0,
            'has_lyrics' => $this->has_lyrics, // a computed attribute
            'has_chords' => (bool)$this->has_chords, // an actual field precomputed in SongLyricSaved event
            'has_lilypond' => $this->lilypond != null
        ];

        return $arr;
    }
}
