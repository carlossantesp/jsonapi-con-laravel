<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_filter_articles_by_title()
    {
        factory(Article::class)->create([
            'title' => 'Aprende Laravel desde cero'
        ]);

        factory(Article::class)->create([
            'title' => 'Other Article'
        ]);

        $url = route('api.v1.articles.index', ['filter[title]' => 'Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel desde cero')
            ->assertDontSee('Other Article');
    }

    /**
     * @test
     */
    public function can_filter_articles_by_content()
    {
        factory(Article::class)->create([
            'content' => '<div>Aprende Laravel desde cero</div>'
        ]);

        factory(Article::class)->create([
            'content' => '<div>Other Article</div>'
        ]);

        $url = route('api.v1.articles.index', ['filter[content]' => 'Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel desde cero')
            ->assertDontSee('Other Article');
    }

    /**
     * @test
     */
    public function can_filter_articles_by_year()
    {
        factory(Article::class)->create([
            'title' => 'Article from 2020',
            'created_at' => now()->year(2020)
        ]);

        factory(Article::class)->create([
            'title' => 'Article from 2021',
            'created_at' => now()->year(2021)
        ]);

        $url = route('api.v1.articles.index', ['filter[year]' => 2020]);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Article from 2020')
            ->assertDontSee('Article from 2021');
    }

    /**
     * @test
     */
    public function can_filter_articles_by_month()
    {
        factory(Article::class)->create([
            'title' => 'Article from February',
            'created_at' => now()->month(2)
        ]);

        factory(Article::class)->create([
            'title' => 'Other article from February',
            'created_at' => now()->month(2)
        ]);

        factory(Article::class)->create([
            'title' => 'Article from January',
            'created_at' => now()->month(1)
        ]);

        $url = route('api.v1.articles.index', ['filter[month]' => 2]);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article from February')
            ->assertSee('Other article from February')
            ->assertDontSee('Article from January');
    }

    /**
     * @test
     */
    public function cannot_filter_articles_by_unknown_filters()
    {
        factory(Article::class)->create();

        $url = route('api.v1.articles.index', ['filter[unknown]' => 2]);

        $this->jsonApi()->get($url)
            ->assertStatus(400);
    }

    /**
     * @test
     */
    public function can_search_articles_by_title_and_content()
    {
        factory(Article::class)->create([
            'title' => 'Article Aprendible',
            'content' => 'Content'
        ]);

        factory(Article::class)->create([
            'title' => 'Another Article',
            'content' => 'Content Aprendible...'
        ]);

        factory(Article::class)->create([
            'title' => 'Title 2',
            'content' => 'Content 2'
        ]);

        $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Article Aprendible')
            ->assertSee('Another Article')
            ->assertDontSee('Title 2');
    }

    /**
     * @test
     */
    public function can_search_articles_by_title_and_content_with_multiple_terms()
    {
        factory(Article::class)->create([
            'title' => 'Article Aprendible',
            'content' => 'Content'
        ]);

        factory(Article::class)->create([
            'title' => 'Another Article',
            'content' => 'Content Aprendible...'
        ]);
        factory(Article::class)->create([
            'title' => 'Another Laravel Article',
            'content' => 'Content'
        ]);

        factory(Article::class)->create([
            'title' => 'Title 2',
            'content' => 'Content 2'
        ]);

        $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(3, 'data')
            ->assertSee('Article Aprendible')
            ->assertSee('Another Article')
            ->assertSee('Another Laravel Article')
            ->assertDontSee('Title 2');
    }
}
