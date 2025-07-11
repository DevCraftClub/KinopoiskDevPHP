<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Enums\SortDirection;

/**
 * @group unit
 * @group enums
 * @group sort-field
 */
class SortFieldTest extends TestCase
{
    public function test_all_sort_fields_have_correct_values(): void
    {
        $this->assertEquals('id', SortField::ID->value);
        $this->assertEquals('name', SortField::NAME->value);
        $this->assertEquals('enName', SortField::EN_NAME->value);
        $this->assertEquals('alternativeName', SortField::ALTERNATIVE_NAME->value);
        $this->assertEquals('year', SortField::YEAR->value);
        $this->assertEquals('createdAt', SortField::CREATED_AT->value);
        $this->assertEquals('updatedAt', SortField::UPDATED_AT->value);
        $this->assertEquals('rating.kp', SortField::RATING_KP->value);
        $this->assertEquals('rating.imdb', SortField::RATING_IMDB->value);
        $this->assertEquals('rating.tmdb', SortField::RATING_TMDB->value);
        $this->assertEquals('rating.filmCritics', SortField::RATING_FILM_CRITICS->value);
        $this->assertEquals('rating.russianFilmCritics', SortField::RATING_RUSSIAN_FILM_CRITICS->value);
        $this->assertEquals('rating.await', SortField::RATING_AWAIT->value);
        $this->assertEquals('votes.kp', SortField::VOTES_KP->value);
        $this->assertEquals('votes.imdb', SortField::VOTES_IMDB->value);
        $this->assertEquals('votes.tmdb', SortField::VOTES_TMDB->value);
        $this->assertEquals('votes.filmCritics', SortField::VOTES_FILM_CRITICS->value);
        $this->assertEquals('votes.russianFilmCritics', SortField::VOTES_RUSSIAN_FILM_CRITICS->value);
        $this->assertEquals('votes.await', SortField::VOTES_AWAIT->value);
        $this->assertEquals('movieLength', SortField::MOVIE_LENGTH->value);
        $this->assertEquals('seriesLength', SortField::SERIES_LENGTH->value);
        $this->assertEquals('totalSeriesLength', SortField::TOTAL_SERIES_LENGTH->value);
        $this->assertEquals('ageRating', SortField::AGE_RATING->value);
        $this->assertEquals('top10', SortField::TOP_10->value);
        $this->assertEquals('top250', SortField::TOP_250->value);
        $this->assertEquals('premiere.world', SortField::PREMIERE_WORLD->value);
        $this->assertEquals('premiere.russia', SortField::PREMIERE_RUSSIA->value);
        $this->assertEquals('premiere.usa', SortField::PREMIERE_USA->value);
        $this->assertEquals('type', SortField::TYPE->value);
        $this->assertEquals('title', SortField::TITLE->value);
    }

    public function test_getRatingFields_returns_all_rating_fields(): void
    {
        $ratingFields = SortField::getRatingFields();
        $expectedFields = [
            SortField::RATING_KP,
            SortField::RATING_IMDB,
            SortField::RATING_TMDB,
            SortField::RATING_FILM_CRITICS,
            SortField::RATING_RUSSIAN_FILM_CRITICS,
            SortField::RATING_AWAIT,
        ];
        
        $this->assertEquals($expectedFields, $ratingFields);
        $this->assertCount(6, $ratingFields);
    }

    public function test_getVotesFields_returns_all_votes_fields(): void
    {
        $votesFields = SortField::getVotesFields();
        $expectedFields = [
            SortField::VOTES_KP,
            SortField::VOTES_IMDB,
            SortField::VOTES_TMDB,
            SortField::VOTES_FILM_CRITICS,
            SortField::VOTES_RUSSIAN_FILM_CRITICS,
            SortField::VOTES_AWAIT,
        ];
        
        $this->assertEquals($expectedFields, $votesFields);
        $this->assertCount(6, $votesFields);
    }

    public function test_getDescription_returns_correct_descriptions(): void
    {
        $this->assertEquals('ID фильма', SortField::ID->getDescription());
        $this->assertEquals('Название (русское)', SortField::NAME->getDescription());
        $this->assertEquals('Название (английское)', SortField::EN_NAME->getDescription());
        $this->assertEquals('Альтернативное название', SortField::ALTERNATIVE_NAME->getDescription());
        $this->assertEquals('Год выпуска', SortField::YEAR->getDescription());
        $this->assertEquals('Дата создания', SortField::CREATED_AT->getDescription());
        $this->assertEquals('Дата обновления', SortField::UPDATED_AT->getDescription());
        $this->assertEquals('Рейтинг Кинопоиска', SortField::RATING_KP->getDescription());
        $this->assertEquals('Рейтинг IMDB', SortField::RATING_IMDB->getDescription());
        $this->assertEquals('Рейтинг TMDB', SortField::RATING_TMDB->getDescription());
        $this->assertEquals('Рейтинг кинокритиков', SortField::RATING_FILM_CRITICS->getDescription());
        $this->assertEquals('Рейтинг российских кинокритиков', SortField::RATING_RUSSIAN_FILM_CRITICS->getDescription());
        $this->assertEquals('Рейтинг ожидания', SortField::RATING_AWAIT->getDescription());
        $this->assertEquals('Голоса Кинопоиска', SortField::VOTES_KP->getDescription());
        $this->assertEquals('Голоса IMDB', SortField::VOTES_IMDB->getDescription());
        $this->assertEquals('Голоса TMDB', SortField::VOTES_TMDB->getDescription());
        $this->assertEquals('Голоса кинокритиков', SortField::VOTES_FILM_CRITICS->getDescription());
        $this->assertEquals('Голоса российских кинокритиков', SortField::VOTES_RUSSIAN_FILM_CRITICS->getDescription());
        $this->assertEquals('Голоса ожидания', SortField::VOTES_AWAIT->getDescription());
        $this->assertEquals('Длительность фильма', SortField::MOVIE_LENGTH->getDescription());
        $this->assertEquals('Длительность серии', SortField::SERIES_LENGTH->getDescription());
        $this->assertEquals('Общая длительность сериала', SortField::TOTAL_SERIES_LENGTH->getDescription());
        $this->assertEquals('Возрастной рейтинг', SortField::AGE_RATING->getDescription());
        $this->assertEquals('Позиция в топ-10', SortField::TOP_10->getDescription());
        $this->assertEquals('Позиция в топ-250', SortField::TOP_250->getDescription());
        $this->assertEquals('Дата мировой премьеры', SortField::PREMIERE_WORLD->getDescription());
        $this->assertEquals('Дата премьеры в России', SortField::PREMIERE_RUSSIA->getDescription());
        $this->assertEquals('Дата премьеры в США', SortField::PREMIERE_USA->getDescription());
        $this->assertEquals('Название', SortField::TITLE->getDescription());
        $this->assertEquals('Тип', SortField::TYPE->getDescription());
    }

    public function test_isRatingField_returns_correct_values(): void
    {
        $this->assertTrue(SortField::RATING_KP->isRatingField());
        $this->assertTrue(SortField::RATING_IMDB->isRatingField());
        $this->assertTrue(SortField::RATING_TMDB->isRatingField());
        $this->assertTrue(SortField::RATING_FILM_CRITICS->isRatingField());
        $this->assertTrue(SortField::RATING_RUSSIAN_FILM_CRITICS->isRatingField());
        $this->assertTrue(SortField::RATING_AWAIT->isRatingField());
        
        $this->assertFalse(SortField::ID->isRatingField());
        $this->assertFalse(SortField::NAME->isRatingField());
        $this->assertFalse(SortField::VOTES_KP->isRatingField());
    }

    public function test_isVotesField_returns_correct_values(): void
    {
        $this->assertTrue(SortField::VOTES_KP->isVotesField());
        $this->assertTrue(SortField::VOTES_IMDB->isVotesField());
        $this->assertTrue(SortField::VOTES_TMDB->isVotesField());
        $this->assertTrue(SortField::VOTES_FILM_CRITICS->isVotesField());
        $this->assertTrue(SortField::VOTES_RUSSIAN_FILM_CRITICS->isVotesField());
        $this->assertTrue(SortField::VOTES_AWAIT->isVotesField());
        
        $this->assertFalse(SortField::ID->isVotesField());
        $this->assertFalse(SortField::NAME->isVotesField());
        $this->assertFalse(SortField::RATING_KP->isVotesField());
    }

    public function test_getDataType_returns_correct_types(): void
    {
        // Числовые поля
        $this->assertEquals('number', SortField::ID->getDataType());
        $this->assertEquals('number', SortField::YEAR->getDataType());
        $this->assertEquals('number', SortField::MOVIE_LENGTH->getDataType());
        $this->assertEquals('number', SortField::SERIES_LENGTH->getDataType());
        $this->assertEquals('number', SortField::TOTAL_SERIES_LENGTH->getDataType());
        $this->assertEquals('number', SortField::AGE_RATING->getDataType());
        $this->assertEquals('number', SortField::TOP_10->getDataType());
        $this->assertEquals('number', SortField::TOP_250->getDataType());
        $this->assertEquals('number', SortField::RATING_KP->getDataType());
        $this->assertEquals('number', SortField::RATING_IMDB->getDataType());
        $this->assertEquals('number', SortField::RATING_TMDB->getDataType());
        $this->assertEquals('number', SortField::RATING_FILM_CRITICS->getDataType());
        $this->assertEquals('number', SortField::RATING_RUSSIAN_FILM_CRITICS->getDataType());
        $this->assertEquals('number', SortField::RATING_AWAIT->getDataType());
        $this->assertEquals('number', SortField::VOTES_KP->getDataType());
        $this->assertEquals('number', SortField::VOTES_IMDB->getDataType());
        $this->assertEquals('number', SortField::VOTES_TMDB->getDataType());
        $this->assertEquals('number', SortField::VOTES_FILM_CRITICS->getDataType());
        $this->assertEquals('number', SortField::VOTES_RUSSIAN_FILM_CRITICS->getDataType());
        $this->assertEquals('number', SortField::VOTES_AWAIT->getDataType());
        
        // Поля дат
        $this->assertEquals('date', SortField::CREATED_AT->getDataType());
        $this->assertEquals('date', SortField::UPDATED_AT->getDataType());
        $this->assertEquals('date', SortField::PREMIERE_WORLD->getDataType());
        $this->assertEquals('date', SortField::PREMIERE_RUSSIA->getDataType());
        $this->assertEquals('date', SortField::PREMIERE_USA->getDataType());
        
        // Строковые поля
        $this->assertEquals('string', SortField::NAME->getDataType());
        $this->assertEquals('string', SortField::EN_NAME->getDataType());
        $this->assertEquals('string', SortField::ALTERNATIVE_NAME->getDataType());
        $this->assertEquals('string', SortField::TYPE->getDataType());
        $this->assertEquals('string', SortField::TITLE->getDataType());
    }

    public function test_isNumericField_returns_correct_values(): void
    {
        $this->assertTrue(SortField::ID->isNumericField());
        $this->assertTrue(SortField::YEAR->isNumericField());
        $this->assertTrue(SortField::RATING_KP->isNumericField());
        $this->assertTrue(SortField::VOTES_KP->isNumericField());
        
        $this->assertFalse(SortField::NAME->isNumericField());
        $this->assertFalse(SortField::CREATED_AT->isNumericField());
    }

    public function test_isDateField_returns_correct_values(): void
    {
        $this->assertTrue(SortField::CREATED_AT->isDateField());
        $this->assertTrue(SortField::UPDATED_AT->isDateField());
        $this->assertTrue(SortField::PREMIERE_WORLD->isDateField());
        $this->assertTrue(SortField::PREMIERE_RUSSIA->isDateField());
        $this->assertTrue(SortField::PREMIERE_USA->isDateField());
        
        $this->assertFalse(SortField::ID->isDateField());
        $this->assertFalse(SortField::NAME->isDateField());
        $this->assertFalse(SortField::RATING_KP->isDateField());
    }

    public function test_getDefaultDirection_returns_correct_directions(): void
    {
        // По убыванию для рейтингов и голосов
        $this->assertEquals(SortDirection::DESC, SortField::RATING_KP->getDefaultDirection());
        $this->assertEquals(SortDirection::DESC, SortField::RATING_IMDB->getDefaultDirection());
        $this->assertEquals(SortDirection::DESC, SortField::VOTES_KP->getDefaultDirection());
        $this->assertEquals(SortDirection::DESC, SortField::VOTES_IMDB->getDefaultDirection());
        
        // По убыванию для года и ID
        $this->assertEquals(SortDirection::DESC, SortField::YEAR->getDefaultDirection());
        $this->assertEquals(SortDirection::DESC, SortField::ID->getDefaultDirection());
        
        // По убыванию для дат
        $this->assertEquals(SortDirection::DESC, SortField::CREATED_AT->getDefaultDirection());
        $this->assertEquals(SortDirection::DESC, SortField::UPDATED_AT->getDefaultDirection());
        $this->assertEquals(SortDirection::DESC, SortField::PREMIERE_WORLD->getDefaultDirection());
        
        // По возрастанию для топов
        $this->assertEquals(SortDirection::ASC, SortField::TOP_10->getDefaultDirection());
        $this->assertEquals(SortDirection::ASC, SortField::TOP_250->getDefaultDirection());
        
        // По возрастанию для длительности
        $this->assertEquals(SortDirection::ASC, SortField::MOVIE_LENGTH->getDefaultDirection());
        $this->assertEquals(SortDirection::ASC, SortField::SERIES_LENGTH->getDefaultDirection());
        $this->assertEquals(SortDirection::ASC, SortField::TOTAL_SERIES_LENGTH->getDefaultDirection());
        $this->assertEquals(SortDirection::ASC, SortField::AGE_RATING->getDefaultDirection());
        
        // По возрастанию для названий
        $this->assertEquals(SortDirection::ASC, SortField::NAME->getDefaultDirection());
        $this->assertEquals(SortDirection::ASC, SortField::EN_NAME->getDefaultDirection());
        $this->assertEquals(SortDirection::ASC, SortField::ALTERNATIVE_NAME->getDefaultDirection());
    }

    public function test_sort_field_can_be_created_from_string(): void
    {
        $this->assertEquals(SortField::ID, SortField::from('id'));
        $this->assertEquals(SortField::NAME, SortField::from('name'));
        $this->assertEquals(SortField::RATING_KP, SortField::from('rating.kp'));
        $this->assertEquals(SortField::VOTES_KP, SortField::from('votes.kp'));
    }

    public function test_sort_field_can_be_compared(): void
    {
        $this->assertTrue(SortField::ID === SortField::from('id'));
        $this->assertFalse(SortField::ID === SortField::NAME);
    }

    public function test_all_cases_are_covered(): void
    {
        $cases = SortField::cases();
        $this->assertCount(30, $cases);
        
        $expectedValues = [
            'id', 'name', 'enName', 'alternativeName', 'year', 'createdAt', 'updatedAt',
            'rating.kp', 'rating.imdb', 'rating.tmdb', 'rating.filmCritics', 'rating.russianFilmCritics', 'rating.await',
            'votes.kp', 'votes.imdb', 'votes.tmdb', 'votes.filmCritics', 'votes.russianFilmCritics', 'votes.await',
            'movieLength', 'seriesLength', 'totalSeriesLength', 'ageRating',
            'top10', 'top250',
            'premiere.world', 'premiere.russia', 'premiere.usa',
            'type', 'title'
        ];
        $actualValues = array_map(fn($case) => $case->value, $cases);
        
        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_getDescription_uses_cache(): void
    {
        // Первый вызов
        $result1 = SortField::ID->getDescription();
        $this->assertEquals('ID фильма', $result1);
        
        // Второй вызов должен использовать кеш
        $result2 = SortField::ID->getDescription();
        $this->assertEquals('ID фильма', $result2);
        $this->assertSame($result1, $result2);
    }
} 