<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Image model for posters, backdrops, etc.
 */
class Image {

//Methods
	public function __construct(
		public readonly ?string $url = NULL,
		public readonly ?string $previewUrl = NULL,
		public readonly ?int    $height = NULL,
		public readonly ?int    $width = NULL,
	) {}

	/**
	 * String representation
	 */
	public function __toString(): string {
		if (!$this->exists()) {
			return 'No image';
		}

		$parts = [];
		if ($this->getFormattedDimensions()) {
			$parts[] = $this->getFormattedDimensions();
		}
		if ($this->getResolutionCategory()) {
			$parts[] = $this->getResolutionCategory();
		}

		return empty($parts) ? 'Image available' : implode(' - ', $parts);
	}

	/**
	 * Check if image exists
	 */
	public function exists(): bool {
		return $this->url !== NULL || $this->previewUrl !== NULL;
	}

	/**
	 * Get formatted dimensions string
	 */
	public function getFormattedDimensions(): ?string {
		if ($this->width === NULL || $this->height === NULL) {
			return NULL;
		}

		return "{$this->width}x{$this->height}";
	}

	/**
	 * Get image resolution category
	 */
	public function getResolutionCategory(): ?string {
		if ($this->width === NULL || $this->height === NULL) {
			return NULL;
		}

		$pixels = $this->width * $this->height;

		if ($pixels >= 8294400) { // 4K (3840x2160)
			return '4K';
		} elseif ($pixels >= 2073600) { // Full HD (1920x1080)
			return 'Full HD';
		} elseif ($pixels >= 921600) { // HD (1280x720)
			return 'HD';
		} elseif ($pixels >= 307200) { // SD (640x480)
			return 'SD';
		} else {
			return 'Low';
		}
	}

	/**
	 * Create Image object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			url       : $data['url'] ?? NULL,
			previewUrl: $data['previewUrl'] ?? NULL,
			height    : isset($data['height']) ? (int) $data['height'] : NULL,
			width     : isset($data['width']) ? (int) $data['width'] : NULL,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'url'        => $this->url,
			'previewUrl' => $this->previewUrl,
			'height'     => $this->height,
			'width'      => $this->width,
		];
	}

	/**
	 * Get image URL
	 */
	public function getUrl(): ?string {
		return $this->url;
	}

	/**
	 * Get preview URL (smaller version)
	 */
	public function getPreviewUrl(): ?string {
		return $this->previewUrl;
	}

	/**
	 * Get best available URL (prefers full size)
	 */
	public function getBestUrl(): ?string {
		return $this->url ?? $this->previewUrl;
	}

	/**
	 * Get image dimensions
	 */
	public function getDimensions(): ?array {
		if ($this->width === NULL || $this->height === NULL) {
			return NULL;
		}

		return [
			'width'  => $this->width,
			'height' => $this->height,
		];
	}

	/**
	 * Check if image is portrait
	 */
	public function isPortrait(): ?bool {
		$ratio = $this->getAspectRatio();

		return $ratio !== NULL ? $ratio < 1 : NULL;
	}

	/**
	 * Get aspect ratio
	 */
	public function getAspectRatio(): ?float {
		if ($this->width === NULL || $this->height === NULL || $this->height === 0) {
			return NULL;
		}

		return $this->width / $this->height;
	}

	/**
	 * Check if image is landscape
	 */
	public function isLandscape(): ?bool {
		$ratio = $this->getAspectRatio();

		return $ratio !== NULL ? $ratio > 1 : NULL;
	}

	/**
	 * Check if image is square
	 */
	public function isSquare(): ?bool {
		$ratio = $this->getAspectRatio();

		return $ratio !== NULL ? abs($ratio - 1) < 0.01 : NULL;
	}
//Methods

}