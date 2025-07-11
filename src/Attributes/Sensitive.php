<?php

declare(strict_types=1);

namespace KinopoiskDev\Attributes;

use Attribute;


/**
 * Атрибут для конфиденциальных полей
 *
 * Позволяет пометить поля как конфиденциальные для контроля
 * их отображения в различных контекстах (JSON, массивы, логи).
 *
 * @package KinopoiskDev\Attributes
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @example
 * ```php
 * #[Sensitive(hideInJson: true, hideInArray: false)]
 * public string $apiToken;
 *
 * #[Sensitive(hideInJson: true, hideInArray: true)]
 * public string $password;
 * ```
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Sensitive {

	/**
	 * Конструктор атрибута конфиденциального поля
	 *
	 * @param   bool  $hideInJson   Скрывать ли поле в JSON сериализации (по умолчанию true)
	 * @param   bool  $hideInArray  Скрывать ли поле в массивах (по умолчанию false)
	 */
	public function __construct(
		public bool $hideInJson = TRUE,
		public bool $hideInArray = FALSE,
	) {}

}
