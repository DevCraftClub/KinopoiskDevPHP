<?php

namespace KinopoiskDev\Models;

/**
 * Класс для представления места смерти персоны
 *
 * Специализированный класс для хранения информации о месте смерти персоны.
 * Наследуется от PersonPlace и предоставляет типизированный интерфейс
 * для работы с данными о месте смерти в контексте API Kinopoisk.dev.
 * Класс не добавляет дополнительной функциональности, полностью наследуя
 * поведение от родительского класса PersonPlace.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @see     \KinopoiskDev\Models\PersonPlace Родительский класс для работы с местами персон
 * @see     \KinopoiskDev\Models\BirthPlace Класс для места рождения персоны
 * @see     \KinopoiskDev\Models\Person Основной класс модели персоны
 */
 class DeathPlace extends PersonPlace {}