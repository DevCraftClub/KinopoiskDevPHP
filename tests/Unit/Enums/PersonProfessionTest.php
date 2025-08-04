<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use KinopoiskDev\Enums\PersonProfession;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group enums
 * @group person-profession
 */
class PersonProfessionTest extends TestCase {

	public function test_all_person_professions_have_correct_values(): void {
		$this->assertEquals('actor', PersonProfession::ACTOR->value);
		$this->assertEquals('director', PersonProfession::DIRECTOR->value);
		$this->assertEquals('writer', PersonProfession::WRITER->value);
		$this->assertEquals('producer', PersonProfession::PRODUCER->value);
		$this->assertEquals('composer', PersonProfession::COMPOSER->value);
		$this->assertEquals('operator', PersonProfession::OPERATOR->value);
		$this->assertEquals('design', PersonProfession::DESIGN->value);
		$this->assertEquals('editor', PersonProfession::EDITOR->value);
		$this->assertEquals('voice_actor', PersonProfession::VOICE_ACTOR->value);
		$this->assertEquals('other', PersonProfession::OTHER->value);
	}

	public function test_fromRussianName_returns_correct_professions(): void {
		$this->assertEquals(PersonProfession::ACTOR, PersonProfession::fromRussianName('актер'));
		$this->assertEquals(PersonProfession::ACTOR, PersonProfession::fromRussianName('актеры'));
		$this->assertEquals(PersonProfession::DIRECTOR, PersonProfession::fromRussianName('режиссер'));
		$this->assertEquals(PersonProfession::DIRECTOR, PersonProfession::fromRussianName('режиссеры'));
		$this->assertEquals(PersonProfession::WRITER, PersonProfession::fromRussianName('сценарист'));
		$this->assertEquals(PersonProfession::WRITER, PersonProfession::fromRussianName('сценаристы'));
		$this->assertEquals(PersonProfession::PRODUCER, PersonProfession::fromRussianName('продюсер'));
		$this->assertEquals(PersonProfession::PRODUCER, PersonProfession::fromRussianName('продюсеры'));
		$this->assertEquals(PersonProfession::COMPOSER, PersonProfession::fromRussianName('композитор'));
		$this->assertEquals(PersonProfession::COMPOSER, PersonProfession::fromRussianName('композиторы'));
		$this->assertEquals(PersonProfession::OPERATOR, PersonProfession::fromRussianName('оператор'));
		$this->assertEquals(PersonProfession::OPERATOR, PersonProfession::fromRussianName('операторы'));
		$this->assertEquals(PersonProfession::DESIGN, PersonProfession::fromRussianName('художник'));
		$this->assertEquals(PersonProfession::DESIGN, PersonProfession::fromRussianName('художники'));
		$this->assertEquals(PersonProfession::EDITOR, PersonProfession::fromRussianName('монтажер'));
		$this->assertEquals(PersonProfession::EDITOR, PersonProfession::fromRussianName('монтажеры'));
		$this->assertEquals(PersonProfession::VOICE_ACTOR, PersonProfession::fromRussianName('актер дубляжа'));
		$this->assertEquals(PersonProfession::VOICE_ACTOR, PersonProfession::fromRussianName('актеры дубляжа'));
		$this->assertEquals(PersonProfession::VOICE_ACTOR, PersonProfession::fromRussianName('актёр дубляжа'));
		$this->assertEquals(PersonProfession::VOICE_ACTOR, PersonProfession::fromRussianName('актёры дубляжа'));
		$this->assertEquals(PersonProfession::OTHER, PersonProfession::fromRussianName('неизвестная профессия'));
	}

	public function test_getRussianName_returns_correct_names(): void {
		$this->assertEquals('актер', PersonProfession::ACTOR->getRussianName());
		$this->assertEquals('режиссер', PersonProfession::DIRECTOR->getRussianName());
		$this->assertEquals('сценарист', PersonProfession::WRITER->getRussianName());
		$this->assertEquals('продюсер', PersonProfession::PRODUCER->getRussianName());
		$this->assertEquals('композитор', PersonProfession::COMPOSER->getRussianName());
		$this->assertEquals('оператор', PersonProfession::OPERATOR->getRussianName());
		$this->assertEquals('художник', PersonProfession::DESIGN->getRussianName());
		$this->assertEquals('монтажер', PersonProfession::EDITOR->getRussianName());
		$this->assertEquals('актер дубляжа', PersonProfession::VOICE_ACTOR->getRussianName());
		$this->assertEquals('другое', PersonProfession::OTHER->getRussianName());
	}

	public function test_getRussianPluralName_returns_correct_names(): void {
		$this->assertEquals('актеры', PersonProfession::ACTOR->getRussianPluralName());
		$this->assertEquals('режиссеры', PersonProfession::DIRECTOR->getRussianPluralName());
		$this->assertEquals('сценаристы', PersonProfession::WRITER->getRussianPluralName());
		$this->assertEquals('продюсеры', PersonProfession::PRODUCER->getRussianPluralName());
		$this->assertEquals('композиторы', PersonProfession::COMPOSER->getRussianPluralName());
		$this->assertEquals('операторы', PersonProfession::OPERATOR->getRussianPluralName());
		$this->assertEquals('художники', PersonProfession::DESIGN->getRussianPluralName());
		$this->assertEquals('монтажеры', PersonProfession::EDITOR->getRussianPluralName());
		$this->assertEquals('актеры дубляжа', PersonProfession::VOICE_ACTOR->getRussianPluralName());
		$this->assertEquals('другие', PersonProfession::OTHER->getRussianPluralName());
	}

	public function test_getEnglishName_returns_correct_names(): void {
		$this->assertEquals('actor', PersonProfession::ACTOR->getEnglishName());
		$this->assertEquals('director', PersonProfession::DIRECTOR->getEnglishName());
		$this->assertEquals('writer', PersonProfession::WRITER->getEnglishName());
		$this->assertEquals('producer', PersonProfession::PRODUCER->getEnglishName());
		$this->assertEquals('composer', PersonProfession::COMPOSER->getEnglishName());
		$this->assertEquals('operator', PersonProfession::OPERATOR->getEnglishName());
		$this->assertEquals('designer', PersonProfession::DESIGN->getEnglishName());
		$this->assertEquals('editor', PersonProfession::EDITOR->getEnglishName());
		$this->assertEquals('voice actor', PersonProfession::VOICE_ACTOR->getEnglishName());
		$this->assertEquals('other', PersonProfession::OTHER->getEnglishName());
	}

	public function test_getEnglishPluralName_returns_correct_names(): void {
		$this->assertEquals('actors', PersonProfession::ACTOR->getEnglishPluralName());
		$this->assertEquals('directors', PersonProfession::DIRECTOR->getEnglishPluralName());
		$this->assertEquals('writers', PersonProfession::WRITER->getEnglishPluralName());
		$this->assertEquals('producers', PersonProfession::PRODUCER->getEnglishPluralName());
		$this->assertEquals('composers', PersonProfession::COMPOSER->getEnglishPluralName());
		$this->assertEquals('operators', PersonProfession::OPERATOR->getEnglishPluralName());
		$this->assertEquals('designers', PersonProfession::DESIGN->getEnglishPluralName());
		$this->assertEquals('editors', PersonProfession::EDITOR->getEnglishPluralName());
		$this->assertEquals('voice actors', PersonProfession::VOICE_ACTOR->getEnglishPluralName());
		$this->assertEquals('others', PersonProfession::OTHER->getEnglishPluralName());
	}

	public function test_person_profession_can_be_created_from_string(): void {
		$this->assertEquals(PersonProfession::ACTOR, PersonProfession::from('actor'));
		$this->assertEquals(PersonProfession::DIRECTOR, PersonProfession::from('director'));
		$this->assertEquals(PersonProfession::WRITER, PersonProfession::from('writer'));
		$this->assertEquals(PersonProfession::PRODUCER, PersonProfession::from('producer'));
		$this->assertEquals(PersonProfession::COMPOSER, PersonProfession::from('composer'));
		$this->assertEquals(PersonProfession::OPERATOR, PersonProfession::from('operator'));
		$this->assertEquals(PersonProfession::DESIGN, PersonProfession::from('design'));
		$this->assertEquals(PersonProfession::EDITOR, PersonProfession::from('editor'));
		$this->assertEquals(PersonProfession::VOICE_ACTOR, PersonProfession::from('voice_actor'));
		$this->assertEquals(PersonProfession::OTHER, PersonProfession::from('other'));
	}

	public function test_all_cases_are_covered(): void {
		$cases = PersonProfession::cases();
		$this->assertCount(10, $cases);

		$expectedValues = ['actor', 'director', 'writer', 'producer', 'composer', 'operator', 'design', 'editor', 'voice_actor', 'other'];
		$actualValues   = array_map(fn ($case) => $case->value, $cases);

		$this->assertEquals($expectedValues, $actualValues);
	}

}