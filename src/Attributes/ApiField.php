<?php

namespace KinopoiskDev\Attributes;

use Attribute;

/**
 * Атрибут для указания источника поля в API
 *
 * Позволяет настроить маппинг между свойствами модели и полями API.
 * Поддерживает указание имени поля в API, возможность null значений
 * и значения по умолчанию.
 *
 * @package KinopoiskDev\Attributes
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @example
 * ```php
 * #[ApiField(name: 'movie_title', nullable: false, default: 'Unknown')]
 * public string $title;
 *
 * #[ApiField(name: 'release_year', nullable: true)]
 * public ?int $year;
 * ```
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class ApiField {

	/**
	 * Конструктор атрибута API поля
	 *
	 * @param   string|null  $name      Имя поля в API (если null, используется имя свойства)
	 * @param   bool         $nullable  Разрешены ли null значения (по умолчанию true)
	 * @param   mixed        $default   Значение по умолчанию при отсутствии данных
	 */
	public function __construct(
		public ?string $name = NULL,
		public bool    $nullable = TRUE,
		public mixed   $default = NULL,
	) {}

}