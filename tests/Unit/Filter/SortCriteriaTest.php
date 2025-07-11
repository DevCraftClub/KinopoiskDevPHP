<?php

declare(strict_types=1);

namespace Tests\Unit\Filter;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Filter\SortCriteria;
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Enums\SortDirection;
use KinopoiskDev\Exceptions\ValidationException;

/**
 * @group unit
 * @group filter
 * @group sort-criteria
 */
class SortCriteriaTest extends TestCase
{
    private SortCriteria $sortCriteria;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sortCriteria = new SortCriteria();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_constructor_createsInstance(): void
    {
        $this->assertInstanceOf(SortCriteria::class, $this->sortCriteria);
    }

    public function test_constructor_withDefaultValues_setsDefaults(): void
    {
        $this->assertEquals(SortField::RATING, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::DESC, $this->sortCriteria->getDirection());
    }

    public function test_constructor_withCustomValues_setsValues(): void
    {
        $sortCriteria = new SortCriteria(SortField::YEAR, SortDirection::ASC);
        
        $this->assertEquals(SortField::YEAR, $sortCriteria->getField());
        $this->assertEquals(SortDirection::ASC, $sortCriteria->getDirection());
    }

    public function test_setField_withValidField_setsField(): void
    {
        $this->sortCriteria->setField(SortField::NAME);
        
        $this->assertEquals(SortField::NAME, $this->sortCriteria->getField());
    }

    public function test_setField_withNullField_throwsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Поле сортировки не может быть пустым');
        
        $this->sortCriteria->setField(null);
    }

    public function test_setDirection_withValidDirection_setsDirection(): void
    {
        $this->sortCriteria->setDirection(SortDirection::ASC);
        
        $this->assertEquals(SortDirection::ASC, $this->sortCriteria->getDirection());
    }

    public function test_setDirection_withNullDirection_throwsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Направление сортировки не может быть пустым');
        
        $this->sortCriteria->setDirection(null);
    }

    public function test_sortByRating_setsRatingField(): void
    {
        $this->sortCriteria->sortByRating();
        
        $this->assertEquals(SortField::RATING, $this->sortCriteria->getField());
    }

    public function test_sortByRating_withDirection_setsRatingFieldAndDirection(): void
    {
        $this->sortCriteria->sortByRating(SortDirection::ASC);
        
        $this->assertEquals(SortField::RATING, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::ASC, $this->sortCriteria->getDirection());
    }

    public function test_sortByYear_setsYearField(): void
    {
        $this->sortCriteria->sortByYear();
        
        $this->assertEquals(SortField::YEAR, $this->sortCriteria->getField());
    }

    public function test_sortByYear_withDirection_setsYearFieldAndDirection(): void
    {
        $this->sortCriteria->sortByYear(SortDirection::DESC);
        
        $this->assertEquals(SortField::YEAR, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::DESC, $this->sortCriteria->getDirection());
    }

    public function test_sortByName_setsNameField(): void
    {
        $this->sortCriteria->sortByName();
        
        $this->assertEquals(SortField::NAME, $this->sortCriteria->getField());
    }

    public function test_sortByName_withDirection_setsNameFieldAndDirection(): void
    {
        $this->sortCriteria->sortByName(SortDirection::ASC);
        
        $this->assertEquals(SortField::NAME, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::ASC, $this->sortCriteria->getDirection());
    }

    public function test_sortByVotes_setsVotesField(): void
    {
        $this->sortCriteria->sortByVotes();
        
        $this->assertEquals(SortField::VOTES, $this->sortCriteria->getField());
    }

    public function test_sortByVotes_withDirection_setsVotesFieldAndDirection(): void
    {
        $this->sortCriteria->sortByVotes(SortDirection::DESC);
        
        $this->assertEquals(SortField::VOTES, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::DESC, $this->sortCriteria->getDirection());
    }

    public function test_sortByMovieLength_setsMovieLengthField(): void
    {
        $this->sortCriteria->sortByMovieLength();
        
        $this->assertEquals(SortField::MOVIE_LENGTH, $this->sortCriteria->getField());
    }

    public function test_sortByMovieLength_withDirection_setsMovieLengthFieldAndDirection(): void
    {
        $this->sortCriteria->sortByMovieLength(SortDirection::ASC);
        
        $this->assertEquals(SortField::MOVIE_LENGTH, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::ASC, $this->sortCriteria->getDirection());
    }

    public function test_sortByPremiere_setsPremiereField(): void
    {
        $this->sortCriteria->sortByPremiere();
        
        $this->assertEquals(SortField::PREMIERE, $this->sortCriteria->getField());
    }

    public function test_sortByPremiere_withDirection_setsPremiereFieldAndDirection(): void
    {
        $this->sortCriteria->sortByPremiere(SortDirection::DESC);
        
        $this->assertEquals(SortField::PREMIERE, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::DESC, $this->sortCriteria->getDirection());
    }

    public function test_sortByBudget_setsBudgetField(): void
    {
        $this->sortCriteria->sortByBudget();
        
        $this->assertEquals(SortField::BUDGET, $this->sortCriteria->getField());
    }

    public function test_sortByBudget_withDirection_setsBudgetFieldAndDirection(): void
    {
        $this->sortCriteria->sortByBudget(SortDirection::ASC);
        
        $this->assertEquals(SortField::BUDGET, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::ASC, $this->sortCriteria->getDirection());
    }

    public function test_sortByFees_setsFeesField(): void
    {
        $this->sortCriteria->sortByFees();
        
        $this->assertEquals(SortField::FEES, $this->sortCriteria->getField());
    }

    public function test_sortByFees_withDirection_setsFeesFieldAndDirection(): void
    {
        $this->sortCriteria->sortByFees(SortDirection::DESC);
        
        $this->assertEquals(SortField::FEES, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::DESC, $this->sortCriteria->getDirection());
    }

    public function test_sortByAgeRating_setsAgeRatingField(): void
    {
        $this->sortCriteria->sortByAgeRating();
        
        $this->assertEquals(SortField::AGE_RATING, $this->sortCriteria->getField());
    }

    public function test_sortByAgeRating_withDirection_setsAgeRatingFieldAndDirection(): void
    {
        $this->sortCriteria->sortByAgeRating(SortDirection::ASC);
        
        $this->assertEquals(SortField::AGE_RATING, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::ASC, $this->sortCriteria->getDirection());
    }

    public function test_sortByTop10_setsTop10Field(): void
    {
        $this->sortCriteria->sortByTop10();
        
        $this->assertEquals(SortField::TOP10, $this->sortCriteria->getField());
    }

    public function test_sortByTop10_withDirection_setsTop10FieldAndDirection(): void
    {
        $this->sortCriteria->sortByTop10(SortDirection::DESC);
        
        $this->assertEquals(SortField::TOP10, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::DESC, $this->sortCriteria->getDirection());
    }

    public function test_sortByTop250_setsTop250Field(): void
    {
        $this->sortCriteria->sortByTop250();
        
        $this->assertEquals(SortField::TOP250, $this->sortCriteria->getField());
    }

    public function test_sortByTop250_withDirection_setsTop250FieldAndDirection(): void
    {
        $this->sortCriteria->sortByTop250(SortDirection::ASC);
        
        $this->assertEquals(SortField::TOP250, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::ASC, $this->sortCriteria->getDirection());
    }

    public function test_sortByCreatedAt_setsCreatedAtField(): void
    {
        $this->sortCriteria->sortByCreatedAt();
        
        $this->assertEquals(SortField::CREATED_AT, $this->sortCriteria->getField());
    }

    public function test_sortByCreatedAt_withDirection_setsCreatedAtFieldAndDirection(): void
    {
        $this->sortCriteria->sortByCreatedAt(SortDirection::DESC);
        
        $this->assertEquals(SortField::CREATED_AT, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::DESC, $this->sortCriteria->getDirection());
    }

    public function test_sortByUpdatedAt_setsUpdatedAtField(): void
    {
        $this->sortCriteria->sortByUpdatedAt();
        
        $this->assertEquals(SortField::UPDATED_AT, $this->sortCriteria->getField());
    }

    public function test_sortByUpdatedAt_withDirection_setsUpdatedAtFieldAndDirection(): void
    {
        $this->sortCriteria->sortByUpdatedAt(SortDirection::ASC);
        
        $this->assertEquals(SortField::UPDATED_AT, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::ASC, $this->sortCriteria->getDirection());
    }

    public function test_asc_setsAscDirection(): void
    {
        $this->sortCriteria->asc();
        
        $this->assertEquals(SortDirection::ASC, $this->sortCriteria->getDirection());
    }

    public function test_desc_setsDescDirection(): void
    {
        $this->sortCriteria->desc();
        
        $this->assertEquals(SortDirection::DESC, $this->sortCriteria->getDirection());
    }

    public function test_toArray_returnsArrayRepresentation(): void
    {
        $this->sortCriteria->sortByRating(SortDirection::ASC);
        
        $array = $this->sortCriteria->toArray();
        
        $this->assertIsArray($array);
        $this->assertArrayHasKey('sortField', $array);
        $this->assertArrayHasKey('sortType', $array);
        $this->assertEquals('rating.kp', $array['sortField']);
        $this->assertEquals(1, $array['sortType']);
    }

    public function test_toArray_withDescDirection_returnsCorrectArray(): void
    {
        $this->sortCriteria->sortByYear(SortDirection::DESC);
        
        $array = $this->sortCriteria->toArray();
        
        $this->assertEquals('year', $array['sortField']);
        $this->assertEquals(-1, $array['sortType']);
    }

    public function test_fromArray_withValidData_createsInstance(): void
    {
        $data = [
            'sortField' => 'name',
            'sortType' => 1
        ];
        
        $sortCriteria = SortCriteria::fromArray($data);
        
        $this->assertInstanceOf(SortCriteria::class, $sortCriteria);
        $this->assertEquals(SortField::NAME, $sortCriteria->getField());
        $this->assertEquals(SortDirection::ASC, $sortCriteria->getDirection());
    }

    public function test_fromArray_withDescData_createsInstance(): void
    {
        $data = [
            'sortField' => 'rating.kp',
            'sortType' => -1
        ];
        
        $sortCriteria = SortCriteria::fromArray($data);
        
        $this->assertInstanceOf(SortCriteria::class, $sortCriteria);
        $this->assertEquals(SortField::RATING, $sortCriteria->getField());
        $this->assertEquals(SortDirection::DESC, $sortCriteria->getDirection());
    }

    public function test_fromArray_withInvalidField_throwsException(): void
    {
        $data = [
            'sortField' => 'invalid_field',
            'sortType' => 1
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Неподдерживаемое поле сортировки: invalid_field');
        
        SortCriteria::fromArray($data);
    }

    public function test_fromArray_withInvalidSortType_throwsException(): void
    {
        $data = [
            'sortField' => 'name',
            'sortType' => 0
        ];
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Неподдерживаемый тип сортировки: 0');
        
        SortCriteria::fromArray($data);
    }

    public function test_validate_withValidData_returnsTrue(): void
    {
        $this->sortCriteria->sortByRating(SortDirection::ASC);
        
        $result = $this->sortCriteria->validate();
        
        $this->assertTrue($result);
    }

    public function test_validate_withInvalidField_throwsException(): void
    {
        $this->sortCriteria->setField(null);
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Поле сортировки не может быть пустым');
        
        $this->sortCriteria->validate();
    }

    public function test_validate_withInvalidDirection_throwsException(): void
    {
        $this->sortCriteria->setDirection(null);
        
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Направление сортировки не может быть пустым');
        
        $this->sortCriteria->validate();
    }

    public function test_fluentInterface_returnsSelf(): void
    {
        $result = $this->sortCriteria
            ->sortByRating()
            ->desc();
        
        $this->assertSame($this->sortCriteria, $result);
        $this->assertEquals(SortField::RATING, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::DESC, $this->sortCriteria->getDirection());
    }

    public function test_getSortFieldValue_returnsCorrectValue(): void
    {
        $this->sortCriteria->sortByRating();
        $this->assertEquals('rating.kp', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByYear();
        $this->assertEquals('year', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByName();
        $this->assertEquals('name', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByVotes();
        $this->assertEquals('votes.kp', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByMovieLength();
        $this->assertEquals('movieLength', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByPremiere();
        $this->assertEquals('premiere.world', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByBudget();
        $this->assertEquals('budget.value', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByFees();
        $this->assertEquals('fees.world.value', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByAgeRating();
        $this->assertEquals('ageRating', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByTop10();
        $this->assertEquals('top10', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByTop250();
        $this->assertEquals('top250', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByCreatedAt();
        $this->assertEquals('createdAt', $this->sortCriteria->getSortFieldValue());
        
        $this->sortCriteria->sortByUpdatedAt();
        $this->assertEquals('updatedAt', $this->sortCriteria->getSortFieldValue());
    }

    public function test_getSortTypeValue_returnsCorrectValue(): void
    {
        $this->sortCriteria->asc();
        $this->assertEquals(1, $this->sortCriteria->getSortTypeValue());
        
        $this->sortCriteria->desc();
        $this->assertEquals(-1, $this->sortCriteria->getSortTypeValue());
    }

    public function test_isAscending_returnsCorrectValue(): void
    {
        $this->sortCriteria->asc();
        $this->assertTrue($this->sortCriteria->isAscending());
        $this->assertFalse($this->sortCriteria->isDescending());
        
        $this->sortCriteria->desc();
        $this->assertFalse($this->sortCriteria->isAscending());
        $this->assertTrue($this->sortCriteria->isDescending());
    }

    public function test_isDescending_returnsCorrectValue(): void
    {
        $this->sortCriteria->desc();
        $this->assertTrue($this->sortCriteria->isDescending());
        $this->assertFalse($this->sortCriteria->isAscending());
        
        $this->sortCriteria->asc();
        $this->assertFalse($this->sortCriteria->isDescending());
        $this->assertTrue($this->sortCriteria->isAscending());
    }

    public function test_reset_resetsToDefaults(): void
    {
        $this->sortCriteria->sortByName(SortDirection::ASC);
        
        $this->assertEquals(SortField::NAME, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::ASC, $this->sortCriteria->getDirection());
        
        $this->sortCriteria->reset();
        
        $this->assertEquals(SortField::RATING, $this->sortCriteria->getField());
        $this->assertEquals(SortDirection::DESC, $this->sortCriteria->getDirection());
    }
} 