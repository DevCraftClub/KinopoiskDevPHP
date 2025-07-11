<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Exceptions\ValidationException;

/**
 * Базовый интерфейс для всех моделей данных
 *
 * Определяет контракт для моделей, работающих с данными API Kinopoisk.dev.
 * Включает методы для создания объектов из массивов данных, преобразования
 * в массивы, валидации и сериализации. Все модели данных должны реализовывать
 * этот интерфейс для обеспечения единообразного API.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 2.0.0
 *
 * @see     \KinopoiskDev\Models\AbstractBaseModel Абстрактная реализация базовой модели
 * @see     \KinopoiskDev\Models\Movie Модель фильма
 * @see     \KinopoiskDev\Models\Person Модель персоны
 * @see     \KinopoiskDev\Models\Studio Модель студии
 *
 * @example
 * ```php
 * class Movie implements BaseModel {
 *     public function __construct(
 *         public string $title,
 *         public int $year
 *     ) {}
 *
 *     public static function fromArray(array $data): static {
 *         return new static($data['title'], $data['year']);
 *     }
 *
 *     public function toArray(bool $includeNulls = true): array {
 *         return [
 *             'title' => $this->title,
 *             'year' => $this->year
 *         ];
 *     }
 *
 *     public function validate(): bool {
 *         return !empty($this->title) && $this->year > 1900;
 *     }
 *
 *     public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
 *         return json_encode($this->toArray(), $flags);
 *     }
 *
 *     public static function fromJson(string $json): static {
 *         $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
 *         return static::fromArray($data);
 *     }
 * }
 * ```
 */
interface BaseModel {

	/**
	 * Создает экземпляр модели из массива данных
	 *
	 * Фабричный метод для создания объекта модели из ассоциативного массива,
	 * полученного из API ответа. Должен обрабатывать маппинг полей API
	 * на свойства модели и выполнять базовую валидацию данных.
	 *
	 * @param   array<string, mixed>  $data  Ассоциативный массив с данными для создания объекта
	 *
	 * @return static Экземпляр модели с заполненными данными
	 * @throws ValidationException При некорректных или неполных данных
	 *
	 * @example
	 * ```php
	 * $apiData = [
	 *     'title' => 'The Matrix',
	 *     'year' => 1999,
	 *     'rating' => 8.7
	 * ];
	 *
	 * $movie = Movie::fromArray($apiData);
	 * echo $movie->title; // The Matrix
	 * ```
	 */
	public static function fromArray(array $data): static;

	/**
	 * Создает объект из JSON строки
	 *
	 * Десериализует JSON строку в объект модели. Парсит JSON,
	 * создает массив данных и использует fromArray для создания объекта.
	 *
	 * @param   string  $json  JSON строка с данными объекта
	 *
	 * @return static Экземпляр модели с заполненными данными
	 * @throws \JsonException При ошибке парсинга JSON строки
	 * @throws ValidationException При некорректных данных после парсинга
	 *
	 * @example
	 * ```php
	 * $json = '{"title":"The Matrix","year":1999}';
	 * $movie = Movie::fromJson($json);
	 * echo $movie->title; // The Matrix
	 * echo $movie->year;  // 1999
	 * ```
	 */
	public static function fromJson(string $json): static;

	/**
	 * Преобразует объект в массив
	 *
	 * Сериализует объект модели в ассоциативный массив для передачи
	 * в API или сохранения в базу данных. Поддерживает контроль
	 * включения null значений.
	 *
	 * @param   bool  $includeNulls  Включать ли null значения в результат (по умолчанию true)
	 *
	 * @return array<string, mixed> Данные объекта в виде ассоциативного массива
	 *
	 * @example
	 * ```php
	 * $movie = new Movie('The Matrix', 1999);
	 *
	 * // С null значениями
	 * $array = $movie->toArray(true);
	 * // Результат: ['title' => 'The Matrix', 'year' => 1999, 'rating' => null]
	 *
	 * // Без null значений
	 * $array = $movie->toArray(false);
	 * // Результат: ['title' => 'The Matrix', 'year' => 1999]
	 * ```
	 */
	public function toArray(bool $includeNulls = TRUE): array;

	/**
	 * Валидирует данные модели
	 *
	 * Проверяет корректность данных модели согласно бизнес-правилам
	 * и ограничениям. Может использовать атрибуты Validation для
	 * автоматической валидации свойств.
	 *
	 * @return bool True если данные валидны, false в противном случае
	 * @throws ValidationException При ошибке валидации с детальным описанием проблем
	 *
	 * @example
	 * ```php
	 * $movie = new Movie('', -1000); // Некорректные данные
	 *
	 * try {
	 *     $movie->validate();
	 *     echo "Модель валидна";
	 * } catch (ValidationException $e) {
	 *     echo "Ошибки валидации: " . $e->getMessage();
	 * }
	 * ```
	 */
	public function validate(): bool;

	/**
	 * Возвращает JSON представление объекта
	 *
	 * Сериализует объект модели в JSON строку для передачи
	 * по сети или сохранения в файл. Поддерживает настройку
	 * флагов кодирования JSON.
	 *
	 * @param   int  $flags  Флаги для json_encode (по умолчанию JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)
	 *
	 * @return string JSON строка с данными объекта
	 * @throws \JsonException При ошибке сериализации в JSON
	 *
	 * @example
	 * ```php
	 * $movie = new Movie('The Matrix', 1999);
	 * $json = $movie->toJson();
	 * // Результат: '{"title":"The Matrix","year":1999}'
	 *
	 * // С дополнительными флагами
	 * $json = $movie->toJson(JSON_PRETTY_PRINT);
	 * ```
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE): string;

}