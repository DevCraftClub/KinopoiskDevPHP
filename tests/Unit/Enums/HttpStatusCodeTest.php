<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Enums\HttpStatusCode;

/**
 * @group unit
 * @group enums
 * @group http-status-code
 */
class HttpStatusCodeTest extends TestCase
{
    public function test_all_http_status_codes_have_correct_values(): void
    {
        $this->assertEquals(200, HttpStatusCode::OK->value);
        $this->assertEquals(401, HttpStatusCode::UNAUTHORIZED->value);
        $this->assertEquals(403, HttpStatusCode::FORBIDDEN->value);
        $this->assertEquals(404, HttpStatusCode::NOT_FOUND->value);
        $this->assertEquals(500, HttpStatusCode::INTERNAL_SERVER_ERROR->value);
    }

    public function test_getDescription_returns_correct_descriptions(): void
    {
        $this->assertEquals('Успешный запрос', HttpStatusCode::OK->getDescription());
        $this->assertEquals('Неавторизован', HttpStatusCode::UNAUTHORIZED->getDescription());
        $this->assertEquals('Доступ запрещён', HttpStatusCode::FORBIDDEN->getDescription());
        $this->assertEquals('Не найдено', HttpStatusCode::NOT_FOUND->getDescription());
        $this->assertEquals('Внутренняя ошибка сервера', HttpStatusCode::INTERNAL_SERVER_ERROR->getDescription());
    }

    public function test_isError_returns_correct_values(): void
    {
        $this->assertFalse(HttpStatusCode::OK->isError());
        $this->assertTrue(HttpStatusCode::UNAUTHORIZED->isError());
        $this->assertTrue(HttpStatusCode::FORBIDDEN->isError());
        $this->assertTrue(HttpStatusCode::NOT_FOUND->isError());
        $this->assertTrue(HttpStatusCode::INTERNAL_SERVER_ERROR->isError());
    }

    public function test_isSuccess_returns_correct_values(): void
    {
        $this->assertTrue(HttpStatusCode::OK->isSuccess());
        $this->assertFalse(HttpStatusCode::UNAUTHORIZED->isSuccess());
        $this->assertFalse(HttpStatusCode::FORBIDDEN->isSuccess());
        $this->assertFalse(HttpStatusCode::NOT_FOUND->isSuccess());
        $this->assertFalse(HttpStatusCode::INTERNAL_SERVER_ERROR->isSuccess());
    }

    public function test_http_status_code_can_be_created_from_int(): void
    {
        $this->assertEquals(HttpStatusCode::OK, HttpStatusCode::from(200));
        $this->assertEquals(HttpStatusCode::UNAUTHORIZED, HttpStatusCode::from(401));
        $this->assertEquals(HttpStatusCode::FORBIDDEN, HttpStatusCode::from(403));
        $this->assertEquals(HttpStatusCode::NOT_FOUND, HttpStatusCode::from(404));
        $this->assertEquals(HttpStatusCode::INTERNAL_SERVER_ERROR, HttpStatusCode::from(500));
    }

    public function test_http_status_code_can_be_compared(): void
    {
        $this->assertTrue(HttpStatusCode::OK === HttpStatusCode::from(200));
        $this->assertFalse(HttpStatusCode::OK === HttpStatusCode::UNAUTHORIZED);
    }

    public function test_all_cases_are_covered(): void
    {
        $cases = HttpStatusCode::cases();
        $this->assertCount(5, $cases);
        
        $expectedValues = [200, 401, 403, 404, 500];
        $actualValues = array_map(fn($case) => $case->value, $cases);
        
        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_error_codes_are_properly_identified(): void
    {
        $errorCodes = [401, 403, 404, 500];
        $successCodes = [200];
        
        foreach ($errorCodes as $code) {
            $statusCode = HttpStatusCode::from($code);
            $this->assertTrue($statusCode->isError(), "Status code {$code} should be identified as error");
            $this->assertFalse($statusCode->isSuccess(), "Status code {$code} should not be identified as success");
        }
        
        foreach ($successCodes as $code) {
            $statusCode = HttpStatusCode::from($code);
            $this->assertFalse($statusCode->isError(), "Status code {$code} should not be identified as error");
            $this->assertTrue($statusCode->isSuccess(), "Status code {$code} should be identified as success");
        }
    }
} 