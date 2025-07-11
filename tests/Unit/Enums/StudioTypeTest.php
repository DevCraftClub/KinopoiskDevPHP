<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Enums\StudioType;

/**
 * @group unit
 * @group enums
 * @group studio-type
 */
class StudioTypeTest extends TestCase
{
    public function test_all_studio_types_have_correct_values(): void
    {
        $this->assertEquals('Производство', StudioType::PRODUCTION->value);
        $this->assertEquals('Спецэффекты', StudioType::SPECIAL_EFFECTS->value);
        $this->assertEquals('Прокат', StudioType::DISTRIBUTION->value);
        $this->assertEquals('Студия дубляжа', StudioType::DUBBING_STUDIO->value);
    }

    public function test_getAllTypes_returns_all_types(): void
    {
        $types = StudioType::getAllTypes();
        $expectedTypes = ['Производство', 'Спецэффекты', 'Прокат', 'Студия дубляжа'];
        
        $this->assertEquals($expectedTypes, $types);
        $this->assertCount(4, $types);
    }

    public function test_isValidType_returns_correct_values(): void
    {
        $this->assertTrue(StudioType::isValidType('Производство'));
        $this->assertTrue(StudioType::isValidType('Спецэффекты'));
        $this->assertTrue(StudioType::isValidType('Прокат'));
        $this->assertTrue(StudioType::isValidType('Студия дубляжа'));
        
        $this->assertFalse(StudioType::isValidType('Неизвестный тип'));
        $this->assertFalse(StudioType::isValidType(''));
        $this->assertFalse(StudioType::isValidType('production'));
    }

    public function test_fromString_returns_correct_instances(): void
    {
        $this->assertEquals(StudioType::PRODUCTION, StudioType::fromString('Производство'));
        $this->assertEquals(StudioType::SPECIAL_EFFECTS, StudioType::fromString('Спецэффекты'));
        $this->assertEquals(StudioType::DISTRIBUTION, StudioType::fromString('Прокат'));
        $this->assertEquals(StudioType::DUBBING_STUDIO, StudioType::fromString('Студия дубляжа'));
        
        $this->assertNull(StudioType::fromString('Неизвестный тип'));
        $this->assertNull(StudioType::fromString(''));
        $this->assertNull(StudioType::fromString('production'));
    }

    public function test_getDescription_returns_correct_descriptions(): void
    {
        $this->assertEquals('Кинокомпания, занимающаяся производством фильмов и сериалов', StudioType::PRODUCTION->getDescription());
        $this->assertEquals('Студия, специализирующаяся на создании визуальных и компьютерных эффектов', StudioType::SPECIAL_EFFECTS->getDescription());
        $this->assertEquals('Дистрибьюторская компания, занимающаяся прокатом и распространением фильмов', StudioType::DISTRIBUTION->getDescription());
        $this->assertEquals('Студия, занимающаяся озвучиванием, дубляжом и локализацией контента', StudioType::DUBBING_STUDIO->getDescription());
    }

    public function test_getEnglishName_returns_correct_names(): void
    {
        $this->assertEquals('Production', StudioType::PRODUCTION->getEnglishName());
        $this->assertEquals('Special Effects', StudioType::SPECIAL_EFFECTS->getEnglishName());
        $this->assertEquals('Distribution', StudioType::DISTRIBUTION->getEnglishName());
        $this->assertEquals('Dubbing Studio', StudioType::DUBBING_STUDIO->getEnglishName());
    }

    public function test_studio_type_can_be_created_from_string(): void
    {
        $this->assertEquals(StudioType::PRODUCTION, StudioType::from('Производство'));
        $this->assertEquals(StudioType::SPECIAL_EFFECTS, StudioType::from('Спецэффекты'));
        $this->assertEquals(StudioType::DISTRIBUTION, StudioType::from('Прокат'));
        $this->assertEquals(StudioType::DUBBING_STUDIO, StudioType::from('Студия дубляжа'));
    }

    public function test_studio_type_can_be_compared(): void
    {
        $this->assertTrue(StudioType::PRODUCTION === StudioType::from('Производство'));
        $this->assertFalse(StudioType::PRODUCTION === StudioType::SPECIAL_EFFECTS);
    }

    public function test_all_cases_are_covered(): void
    {
        $cases = StudioType::cases();
        $this->assertCount(4, $cases);
        
        $expectedValues = ['Производство', 'Спецэффекты', 'Прокат', 'Студия дубляжа'];
        $actualValues = array_map(fn($case) => $case->value, $cases);
        
        $this->assertEquals($expectedValues, $actualValues);
    }
} 