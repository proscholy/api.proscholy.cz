<?php

namespace Tests\Elastic;

use Tests\TestCase;
use Elasticsearch\Client;

use ScoutElastic\IndexConfigurator;
use App\Elastic\AuthorIndexConfigurator;

class AuthorIndexTest extends TestCase
{
    protected Client $client;
    protected IndexConfigurator $index_config;

    protected $author_index_name = 'author_index_test';

    public function setUp(): void
    {
        parent::setUp();

        $this->client = app(Client::class);
        $this->author_index_config = app(AuthorIndexConfigurator::class);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateAuthorIndex()
    {
        $index = $this->client->indices()->create([
            'index' => $this->author_index_name,
            'body' => [
                'settings' => $this->author_index_config->getSettings()
            ]
        ]);

        $this->assertIsArray($index);
        $this->assertTrue($index['acknowledged']);
        $this->assertTrue($index['shards_acknowledged']);
        $this->assertEquals($index['index'], $this->author_index_name);
    }

    public function testAuthorNameAnalyzer()
    {
        $res = $this->client->indices()->analyze([
            'index' => $this->author_index_name,
            'body' => [
                'analyzer' => 'name_analyzer',
                'text' => 'Petr Koronthály'
            ]
        ]);

        $toks = array_map(fn ($tok) => $tok['token'], $res['tokens']);
        logger($toks);

        $this->assertContains('petr', $toks);
        $this->assertContains('pe', $toks);
        $this->assertContains('koron', $toks);
        $this->assertContains('koronthaly', $toks);
    }

    // todo: rewrite to tearDown?
    public function testDeleteAuthorIndex()
    {
        $res = $this->client->indices()->delete([
            'index' => $this->author_index_name
        ]);

        $this->assertIsArray($res);
        $this->assertTrue($res['acknowledged']);
    }
}
