<?php

declare(strict_types=1);

namespace KinopoiskDev\Contracts;

/**
 * Интерфейс для логирования
 *
 * Определяет контракт для ведения журнала событий
 * с поддержкой различных уровней логирования.
 *
 * @package KinopoiskDev\Contracts
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
interface LoggerInterface {

	/**
	 * Записывает сообщение уровня DEBUG
	 *
	 * @param   string $message Сообщение для записи
	 * @param   array  $context Контекстные данные
	 *
	 * @return void
	 */
	public function debug(string $message, array<string, mixed> $context = []): void;

	/**
	 * Записывает информационное сообщение
	 *
	 * @param   string $message Сообщение для записи
	 * @param   array  $context Контекстные данные
	 *
	 * @return void
	 */
	public function info(string $message, array<string, mixed> $context = []): void;

	/**
	 * Записывает предупреждение
	 *
	 * @param   string $message Сообщение для записи
	 * @param   array  $context Контекстные данные
	 *
	 * @return void
	 */
	public function warning(string $message, array<string, mixed> $context = []): void;

	/**
	 * Записывает сообщение об ошибке
	 *
	 * @param   string $message Сообщение для записи
	 * @param   array  $context Контекстные данные
	 *
	 * @return void
	 */
	public function error(string $message, array<string, mixed> $context = []): void;

	/**
	 * Записывает критическое сообщение
	 *
	 * @param   string $message Сообщение для записи
	 * @param   array  $context Контекстные данные
	 *
	 * @return void
	 */
	public function critical(string $message, array<string, mixed> $context = []): void;
}