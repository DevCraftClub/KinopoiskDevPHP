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
 *
 * @package KinopoiskDev\Services
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
final readonly class CacheService implements CacheInterface {

	/**
	 * @param   CacheItemPoolInterface $cache PSR-6 кэш адаптер
	 */
	public function __construct(
		private CacheItemPoolInterface $cache,
	) {}

	/**
	 * {@inheritDoc}
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
	 */
	public function clear(): bool {
		return $this->cache->clear();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMultiple(array $keys): array {
		try {
			$normalizedKeys = array_map($this->normalizeKey(...), $keys);
			$items = $this->cache->getItems($normalizedKeys);
			$result = [];

			foreach ($items as $key => $item) {
				if ($item->isHit()) {
					$result[$key] = $item->get();
				}
			}

			return $result;
		} catch (InvalidArgumentException) {
			return [];
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMultiple(array $values, int $ttl = 3600): bool {
		try {
			$items = [];

			foreach ($values as $key => $value) {
				$item = $this->cache->getItem($this->normalizeKey($key));
				$item->set($value);
				$item->expiresAfter($ttl);
				$items[] = $item;
			}

			return $this->cache->saveDeferred(...$items) && $this->cache->commit();
		} catch (InvalidArgumentException) {
			return false;
		}
	}

	/**
	 * Нормализует ключ кэша для соответствия PSR-6
	 *
	 * @param   string $key Исходный ключ
	 *
	 * @return string Нормализованный ключ
	 */
	private function normalizeKey(string $key): string {
		// Удаляем недопустимые символы для PSR-6
		return preg_replace('/[^a-zA-Z0-9_.-]/', '_', $key);
	}
}