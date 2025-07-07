<?php

namespace KinopoiskDev\Responses\Api;

use KinopoiskDev\Responses\BaseResponseDto;

/**
 * Класс для представления возможного значения поля
 *
 * Представляет информацию о возможном значении для определенного поля API,
 * включая само значение и вспомогательный slug. Используется для получения
 * списка доступных значений для фильтрации по конкретным полям.
 *
 * @package KinopoiskDev\Responses\Api
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\ItemName Для простых названий элементов
 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_getpossiblevaluesbyfieldname
 */
class PossibleValueDto extends BaseResponseDto {

	/**
	 * Конструктор для создания объекта возможного значения
	 *
	 * Создает новый экземпляр класса PossibleValueDto с указанными параметрами.
	 * Оба параметра являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @param   string|null  $name  Значение по которому нужно делать запрос в базу данных
	 * @param   string|null  $slug  Вспомогательное значение для идентификации
	 */
	public function __construct(
		public readonly ?string $name = NULL,
		public readonly ?string $slug = NULL,
	) {}

	/**
	 * Возвращает строковое представление возможного значения
	 *
	 * Формирует читаемое представление возможного значения, предпочитая
	 * название перед slug-ом. Если оба значения отсутствуют, возвращает
	 * сообщение о пустом значении.
	 *
	 * @return string Строковое представление возможного значения
	 */
	public function __toString(): string {
		if ($this->name !== NULL) {
			return $this->name;
		}

		if ($this->slug !== NULL) {
			return $this->slug;
		}

		return 'Пустое значение';
	}

	/**
	 * Создает объект PossibleValueDto из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса PossibleValueDto из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null.
	 *
	 * @param   array  $data  Массив данных о возможном значении от API, содержащий ключи:
	 *                        - name: string|null - значение для запроса в базу данных
	 *                        - slug: string|null - вспомогательное значение
	 *
	 * @return static Новый экземпляр класса PossibleValueDto с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new static(
			name: $data['name'] ?? NULL,
			slug: $data['slug'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса PossibleValueDto в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @return array Массив с данными о возможном значении, содержащий ключи:
	 *               - name: string|null - значение для запроса
	 *               - slug: string|null - вспомогательное значение
	 */
	public function toArray(): array {
		return [
			'name' => $this->name,
			'slug' => $this->slug,
		];
	}

}
