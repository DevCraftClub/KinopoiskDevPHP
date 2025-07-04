<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class Movie {

	public function __construct(
		public readonly int $id,
		public readonly ?ExternalId $externalId = NULL,
		public readonly ?string $name = NULL,
		public readonly ?string $alternativeName = NULL,
		public readonly ?string $enName = NULL,
		public readonly ?array $names = [],
		public readonly ?\KinopoiskDev\Enums\MovieType $type = NULL,
		public readonly ?int $typeNumber = NULL,
		public readonly ?int $year = NULL,
		public readonly ?string $description = NULL,
		public readonly ?string $shortDescription = NULL,
		public readonly ?string $slogan = NULL,
		public readonly ?\KinopoiskDev\Enums\MovieStatus $status = NULL,
		public readonly ?array $facts = [],
		public readonly ?int $movieLength = NULL,
		public readonly ?\KinopoiskDev\Enums\RatingMpaa $ratingMpaa = NULL,
		public readonly ?int $ageRating = NULL,
		public readonly ?Rating $rating = NULL,
		public readonly ?Votes $votes = NULL,
		public readonly ?array $logo = NULL,
		public readonly ?Image $poster = NULL,
		public readonly ?Image $backdrop = NULL,
		public readonly ?array $videos = NULL,
		public readonly array $genres = [],
		public readonly array $countries = [],
		public readonly array $persons = [],
		public readonly ?array $reviewInfo = NULL,
		public readonly ?array $seasonsInfo = [],
		public readonly array $budget = [],
		public readonly array $fees = [],
		public readonly array $premiere = [],
		public readonly ?array $similarMovies = [],
		public readonly ?array $sequelsAndPrequels = [],
		public readonly ?array $watchability = NULL,
		public readonly ?array $releaseYears = [],
		public readonly ?int $top10 = NULL,
		public readonly ?int $top250 = NULL,
		public readonly bool $isSeries = FALSE,
		public readonly ?bool $ticketsOnSale = NULL,
		public readonly ?int $totalSeriesLength = NULL,
		public readonly ?int $seriesLength = NULL,
		public readonly ?array $audience = [],
		public readonly array $lists = [],
		public readonly ?array $networks = NULL,
		public readonly ?string $createdAt = NULL,
		public readonly ?string $updatedAt = NULL,
	) {}

	/**
	 * Create Movie object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			id: $data['id'],
			externalId: isset($data['externalId'])
				? ExternalId::fromArray($data['externalId']) : NULL,
			name: $data['name'] ?? NULL,
			alternativeName: $data['alternativeName'] ?? NULL,
			enName: $data['enName'] ?? NULL,
			names: $data['names'] ?? [],
			type: isset($data['type']) ? \KinopoiskDev\Enums\MovieType::tryFrom($data['type']) : NULL,
			typeNumber: $data['typeNumber'] ?? NULL,
			year: $data['year'] ?? NULL,
			description: $data['description'] ?? NULL,
			shortDescription: $data['shortDescription'] ?? NULL,
			slogan: $data['slogan'] ?? NULL,
			status: isset($data['status']) ? \KinopoiskDev\Enums\MovieStatus::tryFrom($data['status']) : NULL,
			facts: $data['facts'] ?? [],
			movieLength: $data['movieLength'] ?? NULL,
			ratingMpaa: isset($data['ratingMpaa']) ? \KinopoiskDev\Enums\RatingMpaa::tryFrom($data['ratingMpaa']) : NULL,
			ageRating: $data['ageRating'] ?? NULL,
			rating: isset($data['rating']) ? Rating::fromArray($data['rating'])
				: NULL,
			votes: isset($data['votes']) ? Votes::fromArray($data['votes'])
				: NULL,
			logo: $data['logo'] ?? NULL,
			poster: isset($data['poster']) ? Image::fromArray($data['poster'])
				: NULL,
			backdrop: isset($data['backdrop'])
				? Image::fromArray($data['backdrop']) : NULL,
			videos: $data['videos'] ?? NULL,
			genres: $data['genres'] ?? [],
			countries: $data['countries'] ?? [],
			persons: isset($data['persons']) ? array_map(fn($p,
			) => Person::fromArray($p), $data['persons']) : [],
			reviewInfo: $data['reviewInfo'] ?? NULL,
			seasonsInfo: $data['seasonsInfo'] ?? [],
			budget: $data['budget'] ?? [],
			fees: $data['fees'] ?? [],
			premiere: $data['premiere'] ?? [],
			similarMovies: $data['similarMovies'] ?? [],
			sequelsAndPrequels: $data['sequelsAndPrequels'] ?? [],
			watchability: $data['watchability'] ?? NULL,
			releaseYears: $data['releaseYears'] ?? [],
			top10: $data['top10'] ?? NULL,
			top250: $data['top250'] ?? NULL,
			isSeries: $data['isSeries'] ?? FALSE,
			ticketsOnSale: $data['ticketsOnSale'] ?? NULL,
			totalSeriesLength: $data['totalSeriesLength'] ?? NULL,
			seriesLength: $data['seriesLength'] ?? NULL,
			audience: $data['audience'] ?? [],
			lists: $data['lists'] ?? [],
			networks: $data['networks'] ?? NULL,
			createdAt: $data['createdAt'] ?? NULL,
			updatedAt: $data['updatedAt'] ?? NULL,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'id'                  => $this->id,
			'externalId'          => $this->externalId?->toArray(),
			'name'                => $this->name,
			'alternativeName'     => $this->alternativeName,
			'enName'              => $this->enName,
			'names'               => $this->names,
			'type'                => $this->type?->value,
			'typeNumber'          => $this->typeNumber,
			'year'                => $this->year,
			'description'         => $this->description,
			'shortDescription'    => $this->shortDescription,
			'slogan'              => $this->slogan,
			'status'              => $this->status?->value,
			'facts'               => $this->facts,
			'movieLength'         => $this->movieLength,
			'ratingMpaa'          => $this->ratingMpaa?->value,
			'ageRating'           => $this->ageRating,
			'rating'              => $this->rating?->toArray(),
			'votes'               => $this->votes?->toArray(),
			'logo'                => $this->logo,
			'poster'              => $this->poster?->toArray(),
			'backdrop'            => $this->backdrop?->toArray(),
			'videos'              => $this->videos,
			'genres'              => $this->genres,
			'countries'           => $this->countries,
			'persons'             => array_map(fn($p) => $p->toArray(), $this->persons),
			'reviewInfo'          => $this->reviewInfo,
			'seasonsInfo'         => $this->seasonsInfo,
			'budget'              => $this->budget,
			'fees'                => $this->fees,
			'premiere'            => $this->premiere,
			'similarMovies'       => $this->similarMovies,
			'sequelsAndPrequels'  => $this->sequelsAndPrequels,
			'watchability'        => $this->watchability,
			'releaseYears'        => $this->releaseYears,
			'top10'               => $this->top10,
			'top250'              => $this->top250,
			'isSeries'            => $this->isSeries,
			'ticketsOnSale'       => $this->ticketsOnSale,
			'totalSeriesLength'   => $this->totalSeriesLength,
			'seriesLength'        => $this->seriesLength,
			'audience'            => $this->audience,
			'lists'               => $this->lists,
			'networks'            => $this->networks,
			'createdAt'           => $this->createdAt,
			'updatedAt'           => $this->updatedAt,
		];
	}

	/**
	 * Kinopoisk-Bewertung abrufen
	 */
	public function getKinopoiskRating(): ?float {
		return $this->rating['kp'] ?? NULL;
	}

	/**
	 * IMDB-Bewertung abrufen
	 */
	public function getImdbRating(): ?float {
		return $this->rating['imdb'] ?? NULL;
	}

	/**
	 * Poster-URL abrufen
	 */
	public function getPosterUrl(): ?string {
		return $this->poster['url'] ?? NULL;
	}

	/**
	 * Genres als String-Array abrufen
	 */
	public function getGenreNames(): array {
		return array_map(fn($genre) => $genre['name'] ?? '', $this->genres);
	}

	/**
	 * Länder als String-Array abrufen
	 */
	public function getCountryNames(): array {
		return array_map(fn($country) => $country['name'] ?? '',
			$this->countries);
	}

	/**
	 * External IDs abrufen
	 */
	public function getExternalId(): ?ExternalId {
		return $this->externalId;
	}

	/**
	 * IMDB-URL abrufen
	 */
	public function getImdbUrl(): ?string {
		return $this->externalId?->getImdbUrl();
	}

	/**
	 * TMDB-URL abrufen
	 */
	public function getTmdbUrl(): ?string {
		return $this->externalId?->getTmdbUrl();
	}

}
