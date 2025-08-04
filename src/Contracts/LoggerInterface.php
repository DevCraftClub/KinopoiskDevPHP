<?php

declare(strict_types=1);

namespace KinopoiskDev\Contracts;

/**
 * Интерфейс для логирования
 *
 * Определяет контракт для ведения журнала событий
 * с поддержкой различных уровней логирования. Основан на
 * стандартах PSR-3 Logger Interface для обеспечения совместимости
 * с различными системами логирования.
 *
 * @package KinopoiskDev\Contracts
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     https://www.php-fig.org/psr/psr-3/ PSR-3 Logger Interface
 *
 * @example
 * ```php
 * class CustomLogger implements LoggerInterface {
 *     public function debug(string $message, array $context = []): void {
 *         echo "[DEBUG] {$message}\n";
 *     }
 *
 *     public function info(string $message, array $context = []): void {
 *         echo "[INFO] {$message}\n";
 *     }
 *
 *     // ... остальные методы
 * }
 *
 * $kinopoisk = new Kinopoisk(apiToken: 'token', logger: new CustomLogger());
 * ```
 */
interface LoggerInterface {

	/**
	 * Записывает сообщение уровня DEBUG
	 *
	 * Используется для детальной отладочной информации,
	 * которая полезна при разработке и диагностике проблем.
	 * Эти сообщения обычно не записываются в продакшене.
	 *
	 * @param   string                $message  Сообщение для записи в лог
	 * @param   array<string, mixed>  $context  Контекстные данные (параметры запроса, ID пользователя и т.д.)
	 *
	 * @return void
	 *
	 * @example
	 * ```php
	 * $logger->debug('HTTP request started', [
	 *     'method' => 'GET',
	 *     'url' => '/api/movie/123',
	 *     'user_id' => 456
	 * ]);
	 * ```
	 */
	public function debug(string $message, array $context = []): void;

	/**
	 * Записывает информационное сообщение
	 *
	 * Используется для записи общей информации о работе приложения,
	 * такой как инициализация, успешные операции, статистика.
	 *
	 * @param   string                $message  Сообщение для записи в лог
	 * @param   array<string, mixed>  $context  Контекстные данные
	 *
	 * @return void
	 *
	 * @example
	 * ```php
	 * $logger->info('Movie retrieved successfully', [
	 *     'movie_id' => 123,
	 *     'response_time' => 0.15
	 * ]);
	 * ```
	 */
	public function info(string $message, array $context = []): void;

	/**
	 * Записывает предупреждение
	 *
	 * Используется для записи предупреждений, которые не являются
	 * критическими ошибками, но требуют внимания. Например,
	 * устаревшие API вызовы, неоптимальные запросы.
	 *
	 * @param   string                $message  Сообщение для записи в лог
	 * @param   array<string, mixed>  $context  Контекстные данные
	 *
	 * @return void
	 *
	 * @example
	 * ```php
	 * $logger->warning('API rate limit approaching', [
	 *     'current_requests' => 95,
	 *     'limit' => 100,
	 *     'reset_time' => '2024-01-01T00:00:00Z'
	 * ]);
	 * ```
	 */
	public function warning(string $message, array $context = []): void;

	/**
	 * Записывает сообщение об ошибке
	 *
	 * Используется для записи ошибок, которые не позволяют
	 * выполнить запрошенную операцию, но не приводят к
	 * полной остановке приложения.
	 *
	 * @param   string                $message  Сообщение для записи в лог
	 * @param   array<string, mixed>  $context  Контекстные данные
	 *
	 * @return void
	 *
	 * @example
	 * ```php
	 * $logger->error('Failed to retrieve movie', [
	 *     'movie_id' => 123,
	 *     'error_code' => 404,
	 *     'error_message' => 'Movie not found'
	 * ]);
	 * ```
	 */
	public function error(string $message, array $context = []): void;

	/**
	 * Записывает критическое сообщение
	 *
	 * Используется для записи критических ошибок, которые
	 * могут привести к нестабильной работе приложения
	 * или требуют немедленного вмешательства.
	 *
	 * @param   string                $message  Сообщение для записи в лог
	 * @param   array<string, mixed>  $context  Контекстные данные
	 *
	 * @return void
	 *
	 * @example
	 * ```php
	 * $logger->critical('Database connection failed', [
	 *     'host' => 'localhost',
	 *     'port' => 3306,
	 *     'error' => 'Connection refused'
	 * ]);
	 * ```
	 */
	public function critical(string $message, array $context = []): void;

}