<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Image model for posters, backdrops, etc.
 */
class Image
{
	public function __construct(
		public readonly ?string $url = null,
		public readonly ?string $previewUrl = null,
		public readonly ?int $height = null,
		public readonly ?int $width = null,
	) {}

	/**
	 * Create Image object from array (API response)
	 */
	public static function fromArray(array $data): self
	{
		return new self(
			url: $data['url'] ?? null,
			previewUrl: $data['previewUrl'] ?? null,
			height: isset($data['height']) ? (int) $data['height'] : null,
			width: isset($data['width']) ? (int) $data['width'] : null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array
	{
		return [
			'url' => $this->url,
			'previewUrl' => $this->previewUrl,
			'height' => $this->height,
			'width' => $this->width,
		];
	}

	/**
	 * Get image URL
	 */
	public function getUrl(): ?string
	{
		return $this->url;
	}

	/**
	 * Get preview URL (smaller version)
	 */
	public function getPreviewUrl(): ?string
	{
		return $this->previewUrl;
	}

	/**
	 * Get best available URL (prefers full size)
	 */
	public function getBestUrl(): ?string
	{
		return $this->url ?? $this->previewUrl;
	}

	/**
	 * Get image dimensions
	 */
	public function getDimensions(): ?array
	{
		if ($this->width === null || $this->height === null) {
			return null;
		}

		return [
			'width' => $this->width,
			'height' => $this->height,
		];
	}

	/**
	 * Get aspect ratio
	 */
	public function getAspectRatio(): ?float
	{
		if ($this->width === null || $this->height === null || $this->height === 0) {
			return null;
		}

		return $this->width / $this->height;
	}

	/**
	 * Check if image is portrait
	 */
	public function isPortrait(): ?bool
	{
		$ratio = $this->getAspectRatio();
		return $ratio !== null ? $ratio < 1 : null;
	}

	/**
	 * Check if image is landscape
	 */
	public function isLandscape(): ?bool
	{
		$ratio = $this->getAspectRatio();
		return $ratio !== null ? $ratio > 1 : null;
	}

	/**
	 * Check if image is square
	 */
	public function isSquare(): ?bool
	{
		$ratio = $this->getAspectRatio();
		return $ratio !== null ? abs($ratio - 1) < 0.01 : null;
	}

	/**
	 * Check if image exists
	 */
	public function exists(): bool
	{
		return $this->url !== null || $this->previewUrl !== null;
	}

	/**
	 * Get image resolution category
	 */
	public function getResolutionCategory(): ?string
	{
		if ($this->width === null || $this->height === null) {
			return null;
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
	 * Get formatted dimensions string
	 */
	public function getFormattedDimensions(): ?string
	{
		if ($this->width === null || $this->height === null) {
			return null;
		}

		return "{$this->width}x{$this->height}";
	}

	/**
	 * String representation
	 */
	public function __toString(): string
	{
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
}