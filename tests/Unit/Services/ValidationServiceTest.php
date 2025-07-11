<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Services\ValidationService;
use KinopoiskDev\Exceptions\ValidationException;

/**
 * @group unit
 * @group services
 * @group validation-service
 */
class ValidationServiceTest extends TestCase
{
    private ValidationService $validationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validationService = new ValidationService();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_constructor_createsInstance(): void
    {
        $this->assertInstanceOf(ValidationService::class, $this->validationService);
    }

    public function test_validateApiToken_withValidToken_returnsTrue(): void
    {
        $validTokens = [
            $_ENV['KINOPOISK_API_TOKEN'],
            'XYZ9ABC-1DEF2GHI-3JKL4MNO-5PQR6STU',
            '12345678-9ABC-DEF0-1234-567890ABCDEF'
        ];

        foreach ($validTokens as $token) {
            $result = $this->validationService->validateApiToken($token);
            $this->assertTrue($result);
        }
    }

    public function test_validateApiToken_withInvalidToken_throwsException(): void
    {
        $invalidTokens = [
            'invalid-token',
            'ABC1DEF2GH3IJK4LM5NOP6QR7STU', // No hyphens
            'ABC1DEF-2GH3IJK-4LM5NOP-6QR7ST', // Too short
            'ABC1DEF-2GH3IJK-4LM5NOP-6QR7STUV', // Too long
            'abc1def-2gh3ijk-4lm5nop-6qr7stu', // Lowercase
            'ABC1DEF-2GH3IJK-4LM5NOP-6QR7STU-EXTRA' // Extra part
        ];

        foreach ($invalidTokens as $token) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('API токен должен быть в формате: XXXX-XXXX-XXXX-XXXX');
            
            $this->validationService->validateApiToken($token);
        }
    }

    public function test_validateApiToken_withEmptyToken_throwsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('API токен не может быть пустым');
        
        $this->validationService->validateApiToken('');
    }

    public function test_validateApiToken_withNullToken_throwsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('API токен не может быть пустым');
        
        $this->validationService->validateApiToken(null);
    }

    public function test_validateHttpMethod_withValidMethods_returnsTrue(): void
    {
        $validMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];

        foreach ($validMethods as $method) {
            $result = $this->validationService->validateHttpMethod($method);
            $this->assertTrue($result);
        }
    }

    public function test_validateHttpMethod_withInvalidMethod_throwsException(): void
    {
        $invalidMethods = [
            'INVALID',
            'get', // lowercase
            'Get', // mixed case
            'HEAD',
            'OPTIONS',
            'TRACE'
        ];

        foreach ($invalidMethods as $method) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage("Неподдерживаемый HTTP метод: {$method}");
            
            $this->validationService->validateHttpMethod($method);
        }
    }

    public function test_validateHttpMethod_withEmptyMethod_throwsException(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('HTTP метод не может быть пустым');
        
        $this->validationService->validateHttpMethod('');
    }

    public function test_validateEndpoint_withValidEndpoints_returnsTrue(): void
    {
        $validEndpoints = [
            'movie/123',
            'person/456',
            'studio/789',
            'movie',
            'person',
            'studio',
            'movie/random',
            'person/search'
        ];

        foreach ($validEndpoints as $endpoint) {
            $result = $this->validationService->validateEndpoint($endpoint);
            $this->assertTrue($result);
        }
    }

    public function test_validateEndpoint_withInvalidEndpoint_throwsException(): void
    {
        $invalidEndpoints = [
            '', // Empty
            '   ', // Whitespace only
            'invalid/endpoint/with/many/parts',
            'movie//123', // Double slash
            '/movie/123', // Leading slash
            'movie/123/', // Trailing slash
        ];

        foreach ($invalidEndpoints as $endpoint) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage("Некорректный endpoint: {$endpoint}");
            
            $this->validationService->validateEndpoint($endpoint);
        }
    }

    public function test_validateYear_withValidYears_returnsTrue(): void
    {
        $validYears = [1888, 1900, 1950, 2000, 2023, 2030];

        foreach ($validYears as $year) {
            $result = $this->validationService->validateYear($year);
            $this->assertTrue($result);
        }
    }

    public function test_validateYear_withInvalidYears_throwsException(): void
    {
        $invalidYears = [
            1887, // Too early
            2031, // Too late
            -1, // Negative
            0, // Zero
            9999, // Too far in future
        ];

        foreach ($invalidYears as $year) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Год должен быть в диапазоне от 1888 до 2030');
            
            $this->validationService->validateYear($year);
        }
    }

    public function test_validateRating_withValidRatings_returnsTrue(): void
    {
        $validRatings = [0.0, 1.0, 5.5, 8.5, 10.0];

        foreach ($validRatings as $rating) {
            $result = $this->validationService->validateRating($rating);
            $this->assertTrue($result);
        }
    }

    public function test_validateRating_withInvalidRatings_throwsException(): void
    {
        $invalidRatings = [
            -0.1, // Negative
            10.1, // Too high
            11.0, // Too high
            -1.0, // Too low
        ];

        foreach ($invalidRatings as $rating) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Рейтинг должен быть в диапазоне от 0.0 до 10.0');
            
            $this->validationService->validateRating($rating);
        }
    }

    public function test_validateLimit_withValidLimits_returnsTrue(): void
    {
        $validLimits = [1, 10, 50, 100, 250];

        foreach ($validLimits as $limit) {
            $result = $this->validationService->validateLimit($limit);
            $this->assertTrue($result);
        }
    }

    public function test_validateLimit_withInvalidLimits_throwsException(): void
    {
        $invalidLimits = [
            0, // Zero
            -1, // Negative
            251, // Too high
            1000, // Too high
        ];

        foreach ($invalidLimits as $limit) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Лимит должен быть в диапазоне от 1 до 250');
            
            $this->validationService->validateLimit($limit);
        }
    }

    public function test_validatePage_withValidPages_returnsTrue(): void
    {
        $validPages = [1, 2, 10, 100, 1000];

        foreach ($validPages as $page) {
            $result = $this->validationService->validatePage($page);
            $this->assertTrue($result);
        }
    }

    public function test_validatePage_withInvalidPages_throwsException(): void
    {
        $invalidPages = [
            0, // Zero
            -1, // Negative
            -10, // Negative
        ];

        foreach ($invalidPages as $page) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Номер страницы должен быть больше 0');
            
            $this->validationService->validatePage($page);
        }
    }

    public function test_validateMovieId_withValidIds_returnsTrue(): void
    {
        $validIds = [1, 123, 456789, 999999];

        foreach ($validIds as $id) {
            $result = $this->validationService->validateMovieId($id);
            $this->assertTrue($result);
        }
    }

    public function test_validateMovieId_withInvalidIds_throwsException(): void
    {
        $invalidIds = [
            0, // Zero
            -1, // Negative
            -123, // Negative
        ];

        foreach ($invalidIds as $id) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('ID фильма должен быть положительным числом');
            
            $this->validationService->validateMovieId($id);
        }
    }

    public function test_validatePersonId_withValidIds_returnsTrue(): void
    {
        $validIds = [1, 123, 456789, 999999];

        foreach ($validIds as $id) {
            $result = $this->validationService->validatePersonId($id);
            $this->assertTrue($result);
        }
    }

    public function test_validatePersonId_withInvalidIds_throwsException(): void
    {
        $invalidIds = [
            0, // Zero
            -1, // Negative
            -123, // Negative
        ];

        foreach ($invalidIds as $id) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('ID персоны должен быть положительным числом');
            
            $this->validationService->validatePersonId($id);
        }
    }

    public function test_validateGenre_withValidGenres_returnsTrue(): void
    {
        $validGenres = [
            'Action',
            'Drama',
            'Comedy',
            'Horror',
            'Thriller',
            'Romance',
            'Sci-Fi'
        ];

        foreach ($validGenres as $genre) {
            $result = $this->validationService->validateGenre($genre);
            $this->assertTrue($result);
        }
    }

    public function test_validateGenre_withInvalidGenres_throwsException(): void
    {
        $invalidGenres = [
            '', // Empty
            '   ', // Whitespace only
            'invalid-genre',
            '123',
            'Action!',
        ];

        foreach ($invalidGenres as $genre) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Название жанра не может быть пустым');
            
            $this->validationService->validateGenre($genre);
        }
    }

    public function test_validateCountry_withValidCountries_returnsTrue(): void
    {
        $validCountries = [
            'USA',
            'UK',
            'Russia',
            'France',
            'Germany',
            'Japan',
            'China'
        ];

        foreach ($validCountries as $country) {
            $result = $this->validationService->validateCountry($country);
            $this->assertTrue($result);
        }
    }

    public function test_validateCountry_withInvalidCountries_throwsException(): void
    {
        $invalidCountries = [
            '', // Empty
            '   ', // Whitespace only
            'invalid-country',
            '123',
            'USA!',
        ];

        foreach ($invalidCountries as $country) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Название страны не может быть пустым');
            
            $this->validationService->validateCountry($country);
        }
    }

    public function test_validateProfession_withValidProfessions_returnsTrue(): void
    {
        $validProfessions = [
            'актер',
            'режиссер',
            'продюсер',
            'сценарист',
            'оператор',
            'композитор'
        ];

        foreach ($validProfessions as $profession) {
            $result = $this->validationService->validateProfession($profession);
            $this->assertTrue($result);
        }
    }

    public function test_validateProfession_withInvalidProfessions_throwsException(): void
    {
        $invalidProfessions = [
            '', // Empty
            '   ', // Whitespace only
            'invalid-profession',
            '123',
            'actor', // English
            'режиссер!',
        ];

        foreach ($invalidProfessions as $profession) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Профессия не может быть пустой');
            
            $this->validationService->validateProfession($profession);
        }
    }

    public function test_validateSearchQuery_withValidQueries_returnsTrue(): void
    {
        $validQueries = [
            'Test Movie',
            'John Doe',
            'Action',
            '2023',
            'The Matrix',
            'Christopher Nolan'
        ];

        foreach ($validQueries as $query) {
            $result = $this->validationService->validateSearchQuery($query);
            $this->assertTrue($result);
        }
    }

    public function test_validateSearchQuery_withInvalidQueries_throwsException(): void
    {
        $invalidQueries = [
            '', // Empty
            '   ', // Whitespace only
            'a', // Too short
            'ab', // Too short
        ];

        foreach ($invalidQueries as $query) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Поисковый запрос должен содержать минимум 3 символа');
            
            $this->validationService->validateSearchQuery($query);
        }
    }

    public function test_validateDate_withValidDates_returnsTrue(): void
    {
        $validDates = [
            '2023-01-01',
            '2023-12-31',
            '2020-02-29', // Leap year
            '1990-06-15',
            '2030-01-01'
        ];

        foreach ($validDates as $date) {
            $result = $this->validationService->validateDate($date);
            $this->assertTrue($result);
        }
    }

    public function test_validateDate_withInvalidDates_throwsException(): void
    {
        $invalidDates = [
            '', // Empty
            '2023-13-01', // Invalid month
            '2023-00-01', // Invalid month
            '2023-01-32', // Invalid day
            '2023-01-00', // Invalid day
            '2023-02-30', // Invalid day for February
            '2023/01/01', // Wrong format
            '01-01-2023', // Wrong format
            '2023-1-1', // Missing leading zeros
        ];

        foreach ($invalidDates as $date) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Некорректный формат даты. Используйте формат YYYY-MM-DD');
            
            $this->validationService->validateDate($date);
        }
    }

    public function test_validateDateRange_withValidRanges_returnsTrue(): void
    {
        $validRanges = [
            ['2023-01-01', '2023-12-31'],
            ['2020-01-01', '2023-12-31'],
            ['1990-01-01', '2023-12-31'],
        ];

        foreach ($validRanges as [$startDate, $endDate]) {
            $result = $this->validationService->validateDateRange($startDate, $endDate);
            $this->assertTrue($result);
        }
    }

    public function test_validateDateRange_withInvalidRanges_throwsException(): void
    {
        $invalidRanges = [
            ['2023-12-31', '2023-01-01'], // End before start
            ['2023-01-01', '2022-12-31'], // End before start
        ];

        foreach ($invalidRanges as [$startDate, $endDate]) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Дата окончания должна быть позже даты начала');
            
            $this->validationService->validateDateRange($startDate, $endDate);
        }
    }

    public function test_validateArray_withValidArrays_returnsTrue(): void
    {
        $validArrays = [
            ['item1', 'item2'],
            ['Action', 'Drama', 'Comedy'],
            [1, 2, 3],
            []
        ];

        foreach ($validArrays as $array) {
            $result = $this->validationService->validateArray($array);
            $this->assertTrue($result);
        }
    }

    public function test_validateArray_withInvalidArrays_throwsException(): void
    {
        $invalidArrays = [
            null,
            'not an array',
            123,
            true,
            false
        ];

        foreach ($invalidArrays as $array) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Значение должно быть массивом');
            
            $this->validationService->validateArray($array);
        }
    }

    public function test_validateNotEmptyArray_withValidArrays_returnsTrue(): void
    {
        $validArrays = [
            ['item1', 'item2'],
            ['Action', 'Drama', 'Comedy'],
            [1, 2, 3]
        ];

        foreach ($validArrays as $array) {
            $result = $this->validationService->validateNotEmptyArray($array);
            $this->assertTrue($result);
        }
    }

    public function test_validateNotEmptyArray_withEmptyArrays_throwsException(): void
    {
        $emptyArrays = [
            [],
            [null],
            [''],
            ['   ']
        ];

        foreach ($emptyArrays as $array) {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Массив не может быть пустым');
            
            $this->validationService->validateNotEmptyArray($array);
        }
    }
} 