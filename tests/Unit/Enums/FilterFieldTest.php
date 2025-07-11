<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use KinopoiskDev\Enums\FilterField;
use KinopoiskDev\Enums\FilterOperator;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group enums
 * @group filter-field
 */
class FilterFieldTest extends TestCase {

	public function test_all_filter_fields_have_correct_values(): void {
		// Основные поля
		$this->assertEquals('id', FilterField::ID->value);
		$this->assertEquals('externalId', FilterField::EXTERNAL_ID->value);
		$this->assertEquals('name', FilterField::NAME->value);
		$this->assertEquals('enName', FilterField::EN_NAME->value);
		$this->assertEquals('alternativeName', FilterField::ALTERNATIVE_NAME->value);
		$this->assertEquals('names.name', FilterField::NAMES->value);
		$this->assertEquals('description', FilterField::DESCRIPTION->value);
		$this->assertEquals('shortDescription', FilterField::SHORT_DESCRIPTION->value);
		$this->assertEquals('slogan', FilterField::SLOGAN->value);

		// Типы и статусы
		$this->assertEquals('type', FilterField::TYPE->value);
		$this->assertEquals('typeNumber', FilterField::TYPE_NUMBER->value);
		$this->assertEquals('isSeries', FilterField::IS_SERIES->value);
		$this->assertEquals('status', FilterField::STATUS->value);

		// Даты и годы
		$this->assertEquals('year', FilterField::YEAR->value);
		$this->assertEquals('releaseYears', FilterField::RELEASE_YEARS->value);
		$this->assertEquals('updatedAt', FilterField::UPDATED_AT->value);
		$this->assertEquals('createdAt', FilterField::CREATED_AT->value);

		// Рейтинги
		$this->assertEquals('rating.kp', FilterField::RATING_KP->value);
		$this->assertEquals('rating.imdb', FilterField::RATING_IMDB->value);
		$this->assertEquals('rating.tmdb', FilterField::RATING_TMDB->value);
		$this->assertEquals('rating.filmCritics', FilterField::RATING_FILM_CRITICS->value);
		$this->assertEquals('rating.russianFilmCritics', FilterField::RATING_RUSSIAN_FILM_CRITICS->value);
		$this->assertEquals('rating.await', FilterField::RATING_AWAIT->value);
		$this->assertEquals('ratingMpaa', FilterField::RATING_MPAA->value);
		$this->assertEquals('ageRating', FilterField::AGE_RATING->value);

		// Голоса
		$this->assertEquals('votes.kp', FilterField::VOTES_KP->value);
		$this->assertEquals('votes.imdb', FilterField::VOTES_IMDB->value);
		$this->assertEquals('votes.tmdb', FilterField::VOTES_TMDB->value);
		$this->assertEquals('votes.filmCritics', FilterField::VOTES_FILM_CRITICS->value);
		$this->assertEquals('votes.russianFilmCritics', FilterField::VOTES_RUSSIAN_FILM_CRITICS->value);
		$this->assertEquals('votes.await', FilterField::VOTES_AWAIT->value);

		// Длительность
		$this->assertEquals('movieLength', FilterField::MOVIE_LENGTH->value);
		$this->assertEquals('seriesLength', FilterField::SERIES_LENGTH->value);
		$this->assertEquals('totalSeriesLength', FilterField::TOTAL_SERIES_LENGTH->value);

		// Жанры и страны
		$this->assertEquals('genres.name', FilterField::GENRES->value);
		$this->assertEquals('countries.name', FilterField::COUNTRIES->value);

		// Изображения
		$this->assertEquals('poster', FilterField::POSTER->value);
		$this->assertEquals('backdrop', FilterField::BACKDROP->value);
		$this->assertEquals('logo', FilterField::LOGO->value);

		// Дополнительные поля
		$this->assertEquals('ticketsOnSale', FilterField::TICKETS_ON_SALE->value);
		$this->assertEquals('videos', FilterField::VIDEOS->value);
		$this->assertEquals('networks', FilterField::NETWORKS->value);
		$this->assertEquals('persons', FilterField::PERSONS->value);
		$this->assertEquals('persons.name', FilterField::PERSONS_NAME->value);
		$this->assertEquals('persons.id', FilterField::PERSONS_ID->value);
		$this->assertEquals('persons.profession', FilterField::PERSONS_PROFESSION->value);
		$this->assertEquals('facts', FilterField::FACTS->value);
		$this->assertEquals('fees', FilterField::FEES->value);
		$this->assertEquals('premiere', FilterField::PREMIERE->value);
		$this->assertEquals('premiere.world', FilterField::PREMIERE_WORLD->value);
		$this->assertEquals('premiere.russia', FilterField::PREMIERE_RUSSIA->value);
		$this->assertEquals('premiere.usa', FilterField::PREMIERE_USA->value);
		$this->assertEquals('similarMovies', FilterField::SIMILAR_MOVIES->value);
		$this->assertEquals('sequelsAndPrequels', FilterField::SEQUELS_AND_PREQUELS->value);
		$this->assertEquals('watchability', FilterField::WATCHABILITY->value);
		$this->assertEquals('lists', FilterField::LISTS->value);
		$this->assertEquals('top10', FilterField::TOP_10->value);
		$this->assertEquals('top250', FilterField::TOP_250->value);
		$this->assertEquals('seasonsInfo', FilterField::SEASONS_INFO->value);
		$this->assertEquals('budget', FilterField::BUDGET->value);
		$this->assertEquals('audience', FilterField::AUDIENCE->value);
	}

	public function test_getFieldType_returns_correct_types(): void {
		// Числовые поля
		$this->assertEquals('number', FilterField::ID->getFieldType());
		$this->assertEquals('number', FilterField::TYPE_NUMBER->getFieldType());
		$this->assertEquals('number', FilterField::YEAR->getFieldType());
		$this->assertEquals('number', FilterField::RATING_KP->getFieldType());
		$this->assertEquals('number', FilterField::RATING_IMDB->getFieldType());
		$this->assertEquals('number', FilterField::RATING_TMDB->getFieldType());
		$this->assertEquals('number', FilterField::RATING_FILM_CRITICS->getFieldType());
		$this->assertEquals('number', FilterField::RATING_RUSSIAN_FILM_CRITICS->getFieldType());
		$this->assertEquals('number', FilterField::RATING_AWAIT->getFieldType());
		$this->assertEquals('number', FilterField::AGE_RATING->getFieldType());
		$this->assertEquals('number', FilterField::VOTES_KP->getFieldType());
		$this->assertEquals('number', FilterField::VOTES_IMDB->getFieldType());
		$this->assertEquals('number', FilterField::VOTES_TMDB->getFieldType());
		$this->assertEquals('number', FilterField::VOTES_FILM_CRITICS->getFieldType());
		$this->assertEquals('number', FilterField::VOTES_RUSSIAN_FILM_CRITICS->getFieldType());
		$this->assertEquals('number', FilterField::VOTES_AWAIT->getFieldType());
		$this->assertEquals('number', FilterField::MOVIE_LENGTH->getFieldType());
		$this->assertEquals('number', FilterField::SERIES_LENGTH->getFieldType());
		$this->assertEquals('number', FilterField::TOTAL_SERIES_LENGTH->getFieldType());
		$this->assertEquals('number', FilterField::TOP_10->getFieldType());
		$this->assertEquals('number', FilterField::TOP_250->getFieldType());
		$this->assertEquals('number', FilterField::PERSONS_ID->getFieldType());

		// Булевы поля
		$this->assertEquals('boolean', FilterField::IS_SERIES->getFieldType());
		$this->assertEquals('boolean', FilterField::TICKETS_ON_SALE->getFieldType());

		// Текстовые поля
		$this->assertEquals('text', FilterField::NAME->getFieldType());
		$this->assertEquals('text', FilterField::EN_NAME->getFieldType());
		$this->assertEquals('text', FilterField::ALTERNATIVE_NAME->getFieldType());
		$this->assertEquals('text', FilterField::NAMES->getFieldType());
		$this->assertEquals('text', FilterField::DESCRIPTION->getFieldType());
		$this->assertEquals('text', FilterField::SHORT_DESCRIPTION->getFieldType());
		$this->assertEquals('text', FilterField::SLOGAN->getFieldType());
		$this->assertEquals('text', FilterField::PERSONS_NAME->getFieldType());

		// Поля дат
		$this->assertEquals('date', FilterField::UPDATED_AT->getFieldType());
		$this->assertEquals('date', FilterField::CREATED_AT->getFieldType());
		$this->assertEquals('date', FilterField::PREMIERE_WORLD->getFieldType());
		$this->assertEquals('date', FilterField::PREMIERE_RUSSIA->getFieldType());
		$this->assertEquals('date', FilterField::PREMIERE_USA->getFieldType());

		// Поля для включения/исключения
		$this->assertEquals('include_exclude', FilterField::GENRES->getFieldType());
		$this->assertEquals('include_exclude', FilterField::COUNTRIES->getFieldType());

		// Объектные поля
		$this->assertEquals('object', FilterField::EXTERNAL_ID->getFieldType());
		$this->assertEquals('object', FilterField::RELEASE_YEARS->getFieldType());
		$this->assertEquals('object', FilterField::POSTER->getFieldType());
		$this->assertEquals('object', FilterField::BACKDROP->getFieldType());
		$this->assertEquals('object', FilterField::LOGO->getFieldType());
		$this->assertEquals('object', FilterField::VIDEOS->getFieldType());
		$this->assertEquals('object', FilterField::NETWORKS->getFieldType());
		$this->assertEquals('object', FilterField::PERSONS->getFieldType());
		$this->assertEquals('object', FilterField::FACTS->getFieldType());
		$this->assertEquals('object', FilterField::FEES->getFieldType());
		$this->assertEquals('object', FilterField::PREMIERE->getFieldType());
		$this->assertEquals('object', FilterField::SIMILAR_MOVIES->getFieldType());
		$this->assertEquals('object', FilterField::SEQUELS_AND_PREQUELS->getFieldType());
		$this->assertEquals('object', FilterField::WATCHABILITY->getFieldType());
		$this->assertEquals('object', FilterField::LISTS->getFieldType());
		$this->assertEquals('object', FilterField::SEASONS_INFO->getFieldType());
		$this->assertEquals('object', FilterField::BUDGET->getFieldType());
		$this->assertEquals('object', FilterField::AUDIENCE->getFieldType());

		// Строковые поля (по умолчанию)
		$this->assertEquals('string', FilterField::TYPE->getFieldType());
		$this->assertEquals('string', FilterField::STATUS->getFieldType());
		$this->assertEquals('string', FilterField::RATING_MPAA->getFieldType());
		$this->assertEquals('string', FilterField::PERSONS_PROFESSION->getFieldType());
	}

	public function test_supportsIncludeExclude_returns_correct_values(): void {
		$this->assertTrue(FilterField::GENRES->supportsIncludeExclude());
		$this->assertTrue(FilterField::COUNTRIES->supportsIncludeExclude());

		$this->assertFalse(FilterField::ID->supportsIncludeExclude());
		$this->assertFalse(FilterField::NAME->supportsIncludeExclude());
		$this->assertFalse(FilterField::RATING_KP->supportsIncludeExclude());
	}

	public function test_supportsRange_returns_correct_values(): void {
		$this->assertTrue(FilterField::ID->supportsRange());
		$this->assertTrue(FilterField::YEAR->supportsRange());
		$this->assertTrue(FilterField::RATING_KP->supportsRange());
		$this->assertTrue(FilterField::CREATED_AT->supportsRange());
		$this->assertTrue(FilterField::UPDATED_AT->supportsRange());

		$this->assertFalse(FilterField::NAME->supportsRange());
		$this->assertFalse(FilterField::IS_SERIES->supportsRange());
		$this->assertFalse(FilterField::GENRES->supportsRange());
	}

	public function test_getDefaultOperator_returns_correct_operators(): void {
		$this->assertEquals(FilterOperator::EQUALS, FilterField::ID->getDefaultOperator());
		$this->assertEquals(FilterOperator::REGEX, FilterField::NAME->getDefaultOperator());
		$this->assertEquals(FilterOperator::REGEX, FilterField::DESCRIPTION->getDefaultOperator());
		$this->assertEquals(FilterOperator::IN, FilterField::GENRES->getDefaultOperator());
		$this->assertEquals(FilterOperator::EQUALS, FilterField::CREATED_AT->getDefaultOperator());
	}

	public function test_getBaseField_returns_correct_base_fields(): void {
		$this->assertEquals('id', FilterField::ID->getBaseField());
		$this->assertEquals('name', FilterField::NAME->getBaseField());
		$this->assertEquals('rating', FilterField::RATING_KP->getBaseField());
		$this->assertEquals('votes', FilterField::VOTES_KP->getBaseField());
		$this->assertEquals('premiere', FilterField::PREMIERE_WORLD->getBaseField());
		$this->assertEquals('persons', FilterField::PERSONS_NAME->getBaseField());
	}

	public function test_getSubField_returns_correct_sub_fields(): void {
		$this->assertNull(FilterField::ID->getSubField());
		$this->assertNull(FilterField::NAME->getSubField());
		$this->assertEquals('kp', FilterField::RATING_KP->getSubField());
		$this->assertEquals('imdb', FilterField::RATING_IMDB->getSubField());
		$this->assertEquals('world', FilterField::PREMIERE_WORLD->getSubField());
		$this->assertEquals('name', FilterField::PERSONS_NAME->getSubField());
	}

	public function test_filter_field_can_be_created_from_string(): void {
		$this->assertEquals(FilterField::ID, FilterField::from('id'));
		$this->assertEquals(FilterField::NAME, FilterField::from('name'));
		$this->assertEquals(FilterField::RATING_KP, FilterField::from('rating.kp'));
		$this->assertEquals(FilterField::VOTES_KP, FilterField::from('votes.kp'));
	}

	public function test_filter_field_can_be_compared(): void {
		$this->assertTrue(FilterField::ID === FilterField::from('id'));
		$this->assertFalse(FilterField::ID === FilterField::NAME);
	}

	public function test_all_cases_are_covered(): void {
		$cases = FilterField::cases();
		$this->assertCount(61, $cases);

		// Проверяем, что все поля имеют уникальные значения
		$values       = array_map(fn ($case) => $case->value, $cases);
		$uniqueValues = array_unique($values);
		$this->assertCount(count($values), $uniqueValues, 'All filter field values should be unique');
	}

	public function test_getFieldType_uses_cache(): void {
		// Первый вызов
		$result1 = FilterField::ID->getFieldType();
		$this->assertEquals('number', $result1);

		// Второй вызов должен использовать кеш
		$result2 = FilterField::ID->getFieldType();
		$this->assertEquals('number', $result2);
		$this->assertSame($result1, $result2);
	}

}