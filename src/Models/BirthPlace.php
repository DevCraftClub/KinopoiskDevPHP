<?php

namespace KinopoiskDev\Models;

/**
 * Класс для представления места рождения персоны
 *
 * Специализированный класс для представления места рождения персоны, наследующий
 * от базового класса PersonPlace. Предоставляет семантическое разделение между
 * различными типами мест, связанных с персоной (рождение, смерть и т.д.).
 * Наследует все методы и свойства родительского класса без изменений.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @see     \KinopoiskDev\Models\PersonPlace Базовый класс для места персоны
 * @see     \KinopoiskDev\Models\Person Класс персоны, использующий места рождения
 *
 * @example
 * ```php
 * // Создание места рождения из массива
 * $birthPlace = BirthPlace::fromArray(['value' => 'Москва, Россия']);
 *
 * // Получение строкового представления
 * echo $birthPlace; // "Москва, Россия"
 *
 * // Доступ к значению напрямую
 * echo $birthPlace->value; // "Москва, Россия"
 * ```
 */
readonly class BirthPlace extends PersonPlace {}