<?php

declare(strict_types=1);

namespace KinopoiskDev\Services;

use KinopoiskDev\Contracts\CacheInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * Сервис для работы с кэшем
 *
 * Реализация интерфейса кэширования с использованием PSR-6 Cache.
 * Обеспечивает типобезопасную работу с различными драйверами кэша.
 * Поддерживает все основные операции кэширования: получение, сохранение,
 * удаление, проверка существования и массовые операции.
 *
 * @package KinopoiskDev\Services
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * 
 * @see \KinopoiskDev\Contracts\CacheInterface Интерфейс кэширования
 * @see \Psr\Cache\CacheItemPoolInterface PSR-6 интерфейс кэша
 * 
 * @example
 * ```php
 * // Создание с файловым кэшем
 * $cache = new CacheService(new FilesystemAdapter());
 * 
 * // Сохранение данных
 * $cache->set('movie_123', $movieData, 3600);
 * 
 * // Получение данных
 * $data = $cache->get('movie_123');
 * 
 * // Проверка существования
 * if ($cache->has('movie_123')) {
 *     // Данные есть в кэше
 * }
 * ```
 */
final  class CacheService implements CacheInterface {

	/** @var CacheItemPoolInterface PSR-6 адаптер кэша */
	private CacheItemPoolInterface $cache;

	/**
	 * Конструктор сервиса кэширования
	 *
	 * Создает новый экземпляр сервиса кэширования с указанным
	 * PSR-6 адаптером кэша.
	 *
	 * @param   CacheItemPoolInterface $cache PSR-6 кэш адаптер (FilesystemAdapter, RedisAdapter и т.д.)
	 * 
	 * @example
	 * ```php
	 * use Symfony\Component\Cache\Adapter\FilesystemAdapter;
	 * use Symfony\Component\Cache\Adapter\RedisAdapter;
	 * 
	 * // Файловый кэш
	 * $cache = new CacheService(new FilesystemAdapter());
	 * 
	 * // Redis кэш
	 * $redis = new \Redis();
	 * $redis->connect('127.0.0.1', 6379);
	 * $cache = new CacheService(new RedisAdapter($redis));
	 * ```
	 */
	public function __construct(
		CacheItemPoolInterface $cache,
	) {
		$this->cache = $cache;
	}

	/**
	 * {@inheritDoc}
	 * 
	 * Получает значение из кэша по ключу. Возвращает null, если
	 * ключ не найден или произошла ошибка при обращении к кэшу.
	 * Автоматически нормализует ключ для соответствия PSR-6.
	 *
	 * @param   string $key Ключ кэша для получения значения
	 *
	 * @return mixed|null Значение из кэша или null если не найдено
	 * 
	 * @example
	 * ```php
	 * $movie = $cache->get('movie_123');
	 * if ($movie !== null) {
	 *     // Используем данные из кэша
	 * }
	 * ```
	 */
	public function get(string $key): mixed {
		try {
			$item = $this->cache->getItem($this->normalizeKey($key));
			return $item->isHit() ? $item->get() : null;
		} catch (InvalidArgumentException) {
			return null;
		}
	}

	/**
	 * {@inheritDoc}
	 * 
	 * Сохраняет значение в кэш с указанным временем жизни.
	 * Автоматически нормализует ключ и обрабатывает ошибки.
	 * Возвращает true при успешном сохранении, false при ошибке.
	 *
	 * @param   string $key   Ключ кэша для сохранения
	 * @param   mixed  $value Значение для сохранения в кэше
	 * @param   int    $ttl   Время жизни в секундах (по умолчанию 1 час)
	 *
	 * @return bool True при успешном сохранении, false при ошибке
	 * 
	 * @example
	 * ```php
	 * // Сохранение на 1 час
	 * $success = $cache->set('movie_123', $movieData);
	 * 
	 * // Сохранение на 30 минут
	 * $success = $cache->set('movie_123', $movieData, 1800);
	 * ```
	 */
	public function set(string $key, mixed $value, int $ttl = 3600): bool {
		try {
			$item = $this->cache->getItem($this->normalizeKey($key));
			$item->set($value);
			$item->expiresAfter($ttl);

			return $this->cache->save($item);
		} catch (InvalidArgumentException) {
			return false;
		}
	}

	/**
	 * {@inheritDoc}
	 * 
	 * Удаляет значение из кэша по ключу. Возвращает true при
	 * успешном удалении или если ключ не существовал, false при ошибке.
	 *
	 * @param   string $key Ключ кэша для удаления
	 *
	 * @return bool True при успешном удалении, false при ошибке
	 * 
	 * @example
	 * ```php
	 * $deleted = $cache->delete('movie_123');
	 * if ($deleted) {
	 *     echo "Ключ удален из кэша";
	 * }
	 * ```
	 */
	public function delete(string $key): bool {
		try {
			return $this->cache->deleteItem($this->normalizeKey($key));
		} catch (InvalidArgumentException) {
			return false;
		}
	}

	/**
	 * {@inheritDoc}
	 * 
	 * Проверяет наличие ключа в кэше. Возвращает true, если
	 * ключ существует и не истек, false в противном случае.
	 *
	 * @param   string $key Ключ кэша для проверки
	 *
	 * @return bool True если ключ существует, false если нет или произошла ошибка
	 * 
	 * @example
	 * ```php
	 * if ($cache->has('movie_123')) {
	 *     // Ключ существует в кэше
	 *     $data = $cache->get('movie_123');
	 * } else {
	 *     // Ключа нет, загружаем данные
	 *     $data = loadMovieFromDatabase(123);
	 *     $cache->set('movie_123', $data);
	 * }
	 * ```
	 */
	public function has(string $key): bool {
		try {
			return $this->cache->hasItem($this->normalizeKey($key));
		} catch (InvalidArgumentException) {
			return false;
		}
	}

	/**
	 * {@inheritDoc}
	 * 
	 * Очищает весь кэш. Удаляет все сохраненные ключи и значения.
	 * Возвращает true при успешной очистке, false при ошибке.
	 *
	 * @return bool True при успешной очистке, false при ошибке
	 * 
	 * @example
	 * ```php
	 * $cleared = $cache->clear();
	 * if ($cleared) {
	 *     echo "Весь кэш очищен";
	 * }
	 * ```
	 */
	public function clear(): bool {
		try {
			return $this->cache->clear();
		} catch (InvalidArgumentException) {
			return false;
		}
	}

	/**
	 * {@inheritDoc}
	 * 
	 * Получает множественные значения по ключам. Возвращает
	 * ассоциативный массив найденных ключей и их значений.
	 * Ключи, которые не найдены, не включаются в результат.
	 *
	 * @param   array<string> $keys Массив ключей для получения
	 *
	 * @return array<string, mixed> Ассоциативный массив ключ => значение
	 * 
	 * @example
	 * ```php
	 * $keys = ['movie_123', 'movie_456', 'movie_789'];
	 * $movies = $cache->getMultiple($keys);
	 * // Результат: ['movie_123' => $data1, 'movie_456' => $data2]
	 * ```
	 */
	public function getMultiple(array $keys): array {
		$result = [];
		try {
			$normalizedKeys = array_map($this->normalizeKey(...), $keys);
			$items = iterator_to_array($this->cache->getItems($normalizedKeys));
			foreach ($keys as $i => $origKey) {
				$normKey = $normalizedKeys[$i];
				$item = $items[$normKey] ?? null;
				if ($item && $item->isHit()) {
					$result[$origKey] = $item->get();
				} else {
					$result[$origKey] = null;
				}
			}
			return $result;
		} catch (InvalidArgumentException) {
			foreach ($keys as $origKey) {
				$result[$origKey] = null;
			}
			return $result;
		}
	}

	/**
	 * {@inheritDoc}
	 * 
	 * Сохраняет множественные значения в кэш. Использует
	 * отложенное сохранение для оптимизации производительности.
	 * Возвращает true при успешном сохранении всех значений.
	 *
	 * @param   array<string, mixed> $values Ассоциативный массив ключ => значение
	 * @param   int                  $ttl    Время жизни в секундах (по умолчанию 1 час)
	 *
	 * @return bool True при успешном сохранении, false при ошибке
	 * 
	 * @example
	 * ```php
	 * $movies = [
	 *     'movie_123' => $movieData1,
	 *     'movie_456' => $movieData2,
	 *     'movie_789' => $movieData3
	 * ];
	 * $success = $cache->setMultiple($movies, 1800); // 30 минут
	 * ```
	 */
	public function setMultiple(array $values, int $ttl = 3600): bool {
		if (empty($values)) {
			return true;
		}
		try {
			$allSuccess = true;
			$items = [];
			foreach ($values as $key => $value) {
				$item = $this->cache->getItem($this->normalizeKey($key));
				$item->set($value);
				$item->expiresAfter($ttl);
				$items[] = $item;
			}
			foreach ($items as $item) {
				if (!$this->cache->saveDeferred($item)) {
					$allSuccess = false;
				}
			}
			if (!$this->cache->commit()) {
				$allSuccess = false;
			}
			return $allSuccess;
		} catch (InvalidArgumentException) {
			return false;
		}
	}

	/**
	 * Удаляет несколько ключей из кэша
	 *
	 * @param array $keys Массив ключей для удаления
	 * @return bool True, если все ключи успешно удалены, False при ошибке
	 */
	public function deleteMultiple(array $keys): bool
	{
		$allSuccess = true;
		foreach ($keys as $key) {
			try {
				$success = $this->cache->deleteItem($this->normalizeKey($key));
				if (!$success) {
					$allSuccess = false;
				}
			} catch (InvalidArgumentException) {
				$allSuccess = false;
			}
		}
		return $allSuccess;
	}

	/**
	 * Нормализует ключ кэша для соответствия PSR-6
	 *
	 * Преобразует ключ кэша в формат, совместимый с PSR-6.
	 * Заменяет недопустимые символы на подчеркивания.
	 *
	 * @param   string  $key  Исходный ключ кэша
	 *
	 * @return string Нормализованный ключ, совместимый с PSR-6
	 * 
	 * @internal Внутренний метод, используется только внутри класса
	 * 
	 * @example
	 * ```php
	 * // Внутреннее использование
	 * $normalized = $this->normalizeKey('movie:123:data');
	 * // Результат: 'movie_123_data'
	 * ```
	 */
	private function normalizeKey(string $key): string {
		// Удаляем недопустимые символы для PSR-6
		return preg_replace('/[^a-zA-Z0-9_.-]/', '_', $key) ?? $key;
	}
}