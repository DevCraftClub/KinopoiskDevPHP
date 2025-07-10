<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Attributes\Validation;
use KinopoiskDev\Exceptions\ValidationException;

/**
 * Базовый интерфейс для всех моделей данных
 *
 * Определяет контракт для моделей, работающих с данными API Kinopoisk.dev.
 * Включает методы для создания объектов из массивов данных, преобразования
 * в массивы, валидации и сериализации.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 2.0.0
 */
interface BaseModel {

	/**
	 * Создает экземпляр модели из массива данных
	 *
	 * @param   array<string, mixed>  $data  Данные для создания объекта
	 *
	 * @return static Экземпляр модели
	 * @throws ValidationException При некорректных данных
	 */
	public static function fromArray(array $data): static;

	/**
	 * Преобразует объект в массив
	 *
	 * @param   bool  $includeNulls  Включать ли null значения
	 *
	 * @return array<string, mixed> Данные объекта в виде массива
	 */
	public function toArray(bool $includeNulls = true): array;

	/**
	 * Валидирует данные модели
	 *
	 * @return bool True если данные валидны
	 * @throws ValidationException При ошибке валидации
	 */
	public function validate(): bool;

	/**
	 * Возвращает JSON представление объекта
	 *
	 * @param   int  $flags  Флаги для json_encode
	 *
	 * @return string JSON строка
	 * @throws \JsonException При ошибке сериализации
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string;

	/**
	 * Создает объект из JSON строки
	 *
	 * @param   string  $json  JSON строка
	 *
	 * @return static Экземпляр модели
	 * @throws \JsonException При ошибке парсинга
	 * @throws ValidationException При некорректных данных
	 */
	public static function fromJson(string $json): static;
}