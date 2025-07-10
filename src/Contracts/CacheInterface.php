<?php

declare(strict_types=1);

namespace KinopoiskDev\Contracts;

/**
 * Интерфейс для сервиса кэширования
 *
 * Определяет контракт для работы с различными системами кэширования
 * в приложении. Поддерживает базовые операции CRUD для кэша.
 *
 * @package KinopoiskDev\Contracts
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
interface CacheInterface {

	/**
	 * Получает значение из кэша по ключу
	 *
	 * @param   string $key Ключ кэша
	 *
	 * @return mixed|null Значение из кэша или null если не найдено
	 */
	public function get(string $key): mixed;

	/**
	 * Сохраняет значение в кэш
	 *
	 * @param   string $key   Ключ кэша
	 * @param   mixed  $value Значение для сохранения
	 * @param   int    $ttl   Время жизни в секундах
	 *
	 * @return bool True при успешном сохранении
	 */
	public function set(string $key, mixed $value, int $ttl = 3600): bool;

	/**
	 * Удаляет значение из кэша
	 *
	 * @param   string $key Ключ кэша
	 *
	 * @return bool True при успешном удалении
	 */
	public function delete(string $key): bool;

	/**
	 * Проверяет наличие ключа в кэше
	 *
	 * @param   string $key Ключ кэша
	 *
	 * @return bool True если ключ существует
	 */
	public function has(string $key): bool;

	/**
	 * Очищает весь кэш
	 *
	 * @return bool True при успешной очистке
	 */
	public function clear(): bool;

	/**
	 * Получает множественные значения по ключам
	 *
	 * @param   array $keys Массив ключей
	 *
	 * @return array Ассоциативный массив ключ => значение
	 */
	public function getMultiple(array<string> $keys): array<string, mixed>;

	/**
	 * Сохраняет множественные значения
	 *
	 * @param   array $values Ассоциативный массив ключ => значение
	 * @param   int   $ttl    Время жизни в секундах
	 *
	 * @return bool True при успешном сохранении
	 */
	public function setMultiple(array<string, mixed> $values, int $ttl = 3600): bool;
}