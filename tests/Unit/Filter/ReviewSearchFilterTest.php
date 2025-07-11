<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Filter;

use KinopoiskDev\Filter\ReviewSearchFilter;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group filter
 * @group review-search-filter
 */
class ReviewSearchFilterTest extends TestCase {

	private ReviewSearchFilter $filter;

	public function test_author_filter(): void {
		$this->filter->author('Иван Иванов', 'regex');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('author.regex', $filters);
		$this->assertEquals('Иван Иванов', $filters['author.regex']);
	}

	public function test_author_filter_with_default_operator(): void {
		$this->filter->author('Петр Петров');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('author.regex', $filters);
		$this->assertEquals('Петр Петров', $filters['author.regex']);
	}

	public function test_author_filter_with_different_operator(): void {
		$this->filter->author('Сидор Сидоров', 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('author.eq', $filters);
		$this->assertEquals('Сидор Сидоров', $filters['author.eq']);
	}

	public function test_review_filter(): void {
		$this->filter->review('Отличный фильм!', 'regex');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('review.regex', $filters);
		$this->assertEquals('Отличный фильм!', $filters['review.regex']);
	}

	public function test_review_filter_with_default_operator(): void {
		$this->filter->review('Хороший фильм');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('review.regex', $filters);
		$this->assertEquals('Хороший фильм', $filters['review.regex']);
	}

	public function test_review_filter_with_different_operator(): void {
		$this->filter->review('Плохой фильм', 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('review.eq', $filters);
		$this->assertEquals('Плохой фильм', $filters['review.eq']);
	}

	public function test_title_filter(): void {
		$this->filter->title('Мой отзыв', 'regex');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title.regex', $filters);
		$this->assertEquals('Мой отзыв', $filters['title.regex']);
	}

	public function test_title_filter_with_default_operator(): void {
		$this->filter->title('Отзыв о фильме');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title.regex', $filters);
		$this->assertEquals('Отзыв о фильме', $filters['title.regex']);
	}

	public function test_title_filter_with_different_operator(): void {
		$this->filter->title('Заголовок', 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title.eq', $filters);
		$this->assertEquals('Заголовок', $filters['title.eq']);
	}

	public function test_onlyPositive_filter(): void {
		$this->filter->onlyPositive();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type.eq', $filters);
		$this->assertEquals('Позитивный', $filters['type.eq']);
	}

	public function test_onlyNegative_filter(): void {
		$this->filter->onlyNegative();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type.eq', $filters);
		$this->assertEquals('Негативный', $filters['type.eq']);
	}

	public function test_onlyNeutral_filter(): void {
		$this->filter->onlyNeutral();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type.eq', $filters);
		$this->assertEquals('Нейтральный', $filters['type.eq']);
	}

	public function test_chaining_filters(): void {
		$this->filter
			->author('Иван Иванов')
			->review('Отличный фильм')
			->title('Мой отзыв')
			->onlyPositive();

		$filters = $this->filter->getFilters();

		$this->assertArrayHasKey('author.regex', $filters);
		$this->assertEquals('Иван Иванов', $filters['author.regex']);

		$this->assertArrayHasKey('review.regex', $filters);
		$this->assertEquals('Отличный фильм', $filters['review.regex']);

		$this->assertArrayHasKey('title.regex', $filters);
		$this->assertEquals('Мой отзыв', $filters['title.regex']);

		$this->assertArrayHasKey('type.eq', $filters);
		$this->assertEquals('Позитивный', $filters['type.eq']);
	}

	public function test_clear_filters(): void {
		$this->filter->author('Иван')->review('Отличный');

		$this->assertNotEmpty($this->filter->getFilters());

		$this->filter->reset();

		$this->assertEmpty($this->filter->getFilters());
	}

	public function test_inherited_methods_from_movie_filter(): void {
		// Тестируем методы, унаследованные от MovieFilter
		$this->filter->name('Отзыв');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('name.eq', $filters);
		$this->assertEquals('Отзыв', $filters['name.eq']);
	}

	public function test_not_null_fields(): void {
		$this->filter->notNullFields(['author', 'review', 'title']);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('author.ne', $filters);
		$this->assertArrayHasKey('review.ne', $filters);
		$this->assertArrayHasKey('title.ne', $filters);
	}

	protected function setUp(): void {
		$this->filter = new ReviewSearchFilter();
	}

}