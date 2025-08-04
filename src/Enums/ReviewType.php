<?php

namespace KinopoiskDev\Enums;

enum ReviewType: string {
	case POSITIVE = 'Позитивный';
	case NEGATIVE = 'Негативный';
	case NEUTRAL = 'Нейтральный';
}