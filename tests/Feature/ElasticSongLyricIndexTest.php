<?php

namespace Tests\Feature;

use Tests\TestCase;
use Elasticsearch\Client;

use ScoutElastic\IndexConfigurator;
use App\SongLyricIndexConfigurator;

class ElasticSongLyricIndexTest extends TestCase
{
    protected Client $client;
    protected IndexConfigurator $index_config;

    protected $index_name = 'song_lyric_index_test';

    public function setUp(): void
    {
        parent::setUp();

        $this->client = app(Client::class);
        $this->index_config = app(SongLyricIndexConfigurator::class);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateIndex()
    {
        $index = $this->client->indices()->create([
            'index' => $this->index_name,
            'body' => [
                'settings' => $this->index_config->getSettings()
            ]
        ]);

        $this->assertIsArray($index);
        $this->assertTrue($index['acknowledged']);
        $this->assertTrue($index['shards_acknowledged']);
        $this->assertEquals($index['index'], $this->index_name);
    }

    public function testSongLyricNameAnalyzer()
    {
        $res = $this->client->indices()->analyze([
            'index' => $this->index_name,
            'body' => [
                'analyzer' => 'name_analyzer',
                'text' => 'Chval ho, ó, duše má'
            ]
        ]);

        $toks = array_map(fn ($tok) => $tok['token'], $res['tokens']);
        logger($toks);

        // $this->assertContains('petr', $toks);
        // $this->assertContains('pe', $toks);
        // $this->assertContains('koron', $toks);
        // $this->assertContains('koronthaly', $toks);

        $res = $this->client->indices()->analyze([
            'index' => $this->index_name,
            'body' => [
                'analyzer' => 'name_analyzer',
                'text' => '10,000 reasons'
            ]
        ]);

        $toks = array_map(fn ($tok) => $tok['token'], $res['tokens']);
        logger($toks);

        // $this->assertContains('petr', $toks);
        // $this->assertContains('pe', $toks);
        // $this->assertContains('koron', $toks);
        // $this->assertContains('koronthaly', $toks);

    }

    public function testDeleteIndex()
    {
        $res = $this->client->indices()->delete([
            'index' => $this->index_name
        ]);

        $this->assertIsArray($res);
        $this->assertTrue($res['acknowledged']);
    }
}
