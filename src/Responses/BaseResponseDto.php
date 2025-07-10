<?php

namespace KinopoiskDev\Responses;

/**
 * Базовый класс для всех DTO ответов API
 *
 * Обеспечивает единообразный интерфейс для всех объектов передачи данных ответов,
 * предоставляя стандартные методы для создания из массива и преобразования в массив.
 * Все конкретные DTO ответов должны наследоваться от этого класса.
 *
 * @package   KinopoiskDev\Responses
 * @since     1.0.0
 * @author    Maxim Harder
 *
 * @version   1.0.0
 * @see       \KinopoiskDev\Responses\Api\MovieDocsResponseDto
 * @see       \KinopoiskDev\Responses\Api\PersonDocsResponseDto
 * @see       \KinopoiskDev\Responses\Api\SearchMovieResponseDto
 */
abstract class BaseResponseDto {

	/**
	 * Создает экземпляр DTO из массива данных
	 *
	 * Фабричный метод для создания объекта DTO из ассоциативного массива,
	 * полученного из API ответа. Каждый дочерний класс должен реализовать
	 * этот метод в соответствии со своей структурой данных.
	 *
	 * @param   array<string, mixed>  $data  Ассоциативный массив с данными для создания DTO
	 *
	 * @return static Экземпляр конкретного DTO класса
	 */
	abstract public static function fromArray(array $data): static;

	/**
	 * Преобразует DTO в ассоциативный массив
	 *
	 * Метод для сериализации объекта DTO в массив, пригодный для
	 * передачи в JSON или другие форматы. Структура массива должна
	 * соответствовать формату API ответа.
	 *
	 * @return array Ассоциативный массив с данными DTO
	 */
	abstract public function toArray(): array;

}