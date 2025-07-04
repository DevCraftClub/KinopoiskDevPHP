<?php

namespace KinopoiskDev\Enums;

/**
 * Enum для типов фильмов
 *
 * Этот enum содержит все возможные типы фильмов, которые могут быть
 * возвращены API Kinopoisk.dev
 */
enum MovieType: string {
    case MOVIE = 'movie';
    case TV_SERIES = 'tv-series';
    case CARTOON = 'cartoon';
    case ANIME = 'anime';
    case ANIMATED_SERIES = 'animated-series';
    case TV_SHOW = 'tv-show';

    /**
     * Возвращает человекочитаемое название типа фильма
     */
    public function getLabel(): string {
        return match($this) {
            self::MOVIE => 'Фильм',
            self::TV_SERIES => 'Сериал',
            self::CARTOON => 'Мультфильм',
            self::ANIME => 'Аниме',
            self::ANIMATED_SERIES => 'Анимационный сериал',
            self::TV_SHOW => 'ТВ-шоу',
        };
    }

}
