<?php

namespace KinopoiskDev\Models;

/**
 * Класс для представления географического места, связанного с персоной
 *
 * Представляет место рождения или смерти персоны в системе Kinopoisk.dev.
 * Используется для хранения и обработки географической информации о персонах,
 * включая города, страны или другие места, связанные с жизнью человека.
 * Класс предоставляет простой интерфейс для работы с текстовыми данными
 * о местах в контексте биографической информации.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Models\Person Для работы с персонами
 * @see     \KinopoiskDev\Models\PersonPlaceValue Для более детальной географической информации
 */
 class PersonPlace implements BaseModel {

	/**
	 * Строковое значение места рождения или смерти персоны
	 *
	 * Содержит текстовое описание географического места, связанного с персоной.
	 * Может содержать название города, страны или полный адрес места рождения/смерти.
	 * Значение доступно только для чтения после создания объекта.
	 *
	 * @var string $value Место рождения/смерти персоны (например, "Москва, Россия")
	 */
	public function __construct(
		public string $value,
	) {}

	/**
	 * Возвращает строковое представление места персоны
	 *
	 * Магический метод для получения строкового представления объекта PersonPlace.
	 * Используется при приведении объекта к строке или при выводе объекта в контексте,
	 * где требуется строковое значение. Возвращает непосредственно значение места
	 * без дополнительного форматирования.
	 *
	 * @see PersonPlace::toArray() Для получения данных в формате массива
	 * @see PersonPlace::$value Для доступа к свойству места напрямую
	 *
	 * @return string Строковое представление места рождения/смерти персоны
	 */
	public function __toString(): string {
		return $this->value;
	}

	/**
	 * Создает объект PersonPlace из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса PersonPlace из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно извлекает значение места из массива
	 * и создает новый объект с соответствующими данными. Используется для
	 * десериализации данных API в объекты модели.
	 *
	 * @see PersonPlace::toArray() Для обратного преобразования в массив
	 * @see PersonPlace::__construct() Для создания объекта с параметрами
	 *
	 * @param   array  $data  Массив данных от API, содержащий ключи:
	 *                        - value: string - текстовое значение места рождения/смерти
	 *
	 * @return \KinopoiskDev\Models\PersonPlace Новый экземпляр класса PersonPlace с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			value: $data['value'],
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса PersonPlace в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API, кэшировании или экспорте данных.
	 * Возвращает массив с единственным ключом 'value'.
	 *
	 * @see PersonPlace::fromArray() Для создания объекта из массива
	 * @see PersonPlace::__toString() Для получения только текстового значения
	 *
	 * @return array Массив с данными о месте, содержащий:
	 *               - value: string - текстовое значение места рождения/смерти
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'value' => $this->value,
		];
	}


	/**
	 * Валидирует данные модели
	 *
	 * @return bool True если данные валидны
	 * @throws \KinopoiskDev\Exceptions\ValidationException При ошибке валидации
	 */
	public function validate(): bool {
		return true; // Basic validation - override in specific models if needed
	}

	/**
	 * Возвращает JSON представление объекта
	 *
	 * @param int $flags Флаги для json_encode
	 * @return string JSON строка
	 * @throws \JsonException При ошибке сериализации
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
		return json_encode($this->toArray(), $flags);
	}

	/**
	 * Создает объект из JSON строки
	 *
	 * @param string $json JSON строка
	 * @return static Экземпляр модели
	 * @throws \JsonException При ошибке парсинга
	 * @throws \KinopoiskDev\Exceptions\ValidationException При некорректных данных
	 */
	public static function fromJson(string $json): static {
		$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
		$instance = static::fromArray($data);
		$instance->validate();
		return $instance;
	}


}
