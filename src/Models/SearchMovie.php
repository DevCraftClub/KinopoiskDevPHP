<?php

namespace KinopoiskDev\Models;

class SearchMovie {

	public function __construct(
		public readonly int $id,
		public readonly ?string $name = NULL,
		public readonly ?string $alternativeName = NULL,
		public readonly ?string $enName = NULL,
		public readonly ?string $type = NULL,
		public readonly ?int $year = NULL,
		public readonly ?string $description = NULL,
		public readonly ?string $shortDescription = NULL,
		public readonly ?int $movieLength = NULL,
		public readonly ?array $names = NULL,
		public readonly ?array $externalId = NULL,
		public readonly ?array $logo = NULL,
		public readonly ?array $poster = NULL,
		public readonly ?array $backdrop = NULL,
		public readonly ?array $rating = NULL,
		public readonly ?array $votes = NULL,
		public readonly ?array $genres = NULL,
		public readonly ?array $countries = NULL,
		public readonly ?array $releaseYears = NULL,
		public readonly ?bool $isSeries = NULL,
		public readonly ?bool $ticketsOnSale = NULL,
		public readonly ?int $totalSeriesLength = NULL,
		public readonly ?int $seriesLength = NULL,
		public readonly ?string $ratingMpaa = NULL,
		public readonly ?int $ageRating = NULL,
		public readonly ?int $top10 = NULL,
		public readonly ?int $top250 = NULL,
		public readonly ?int $typeNumber = NULL,
		public readonly ?string $status = NULL,

	) {
	}

	public static function fromArray(array $data): self {
		return new self(
			id: $data['id'],
			name: $data['name'] ?? NULL,
			alternativeName: $data['alternativeName'] ?? NULL,
			enName: $data['enName'] ?? NULL,
			type: $data['type'] ?? NULL,
			year: $data['year'] ?? NULL,
			description: $data['description'] ?? NULL,
			shortDescription: $data['shortDescription'] ?? NULL,
			movieLength: $data['movieLength'] ?? NULL,
			names: is_array($data['names']) ? Name::fromArray($data['names']): NULL,
			externalId: is_array($data['externalId']) ? ExternalId::fromArray($data['externalId']): NULL,
			logo: is_array($data['logo']) ? Logo::fromArray($data['logo']): NULL,
			poster: is_array($data['poster']) ? ShortImage::fromArray($data['poster']): NULL,
			backdrop: is_array($data['backdrop']) ? ShortImage::fromArray($data['backdrop']): NULL,
			rating: is_array($data['rating']) ? Rating::fromArray($data['rating']): NULL,
			votes: is_array($data['votes']) ? Votes::fromArray($data['votes']): NULL,
			genres: is_array($data['genres']) ? ItemName::fromArray($data['genres']): NULL,
			countries: is_array($data['countries']) ? ItemName::fromArray($data['countries']): NULL,
			releaseYears: is_array($data['releaseYears']) ? YearRange::fromArray($data['releaseYears']): NULL,
			isSeries: $data['isSeries'] ?? NULL,
			ticketsOnSale: $data['ticketsOnSale'] ?? NULL,
			totalSeriesLength: $data['totalSeriesLength'] ?? NULL,
			seriesLength: $data['seriesLength'] ?? NULL,
			ratingMpaa: $data['ratingMpaa'] ?? NULL,
			ageRating: $data['ageRating'] ?? NULL,
			top10: $data['top10'] ?? NULL,
			top250: $data['top250'] ?? NULL,
			typeNumber: $data['typeNumber'] ?? NULL,
			status: $data['status'] ?? NULL,
		);
	}

	public function toArray(): array {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'alternativeName' => $this->alternativeName,
			'enName' => $this->enName,
			'type' => $this->type,
			'year' => $this->year,
			'description' => $this->description,
			'shortDescription' => $this->shortDescription,
			'movieLength' => $this->movieLength,
			'names' => $this->names,
			'externalId' => $this->externalId,
			'logo' => $this->logo,
			'poster' => $this->poster,
			'backdrop' => $this->backdrop,
			'rating' => $this->rating,
			'votes' => $this->votes,
			'genres' => $this->genres,
			'countries' => $this->countries,
			'releaseYears' => $this->releaseYears,
			'isSeries' => $this->isSeries,
			'ticketsOnSale' => $this->ticketsOnSale,
			'totalSeriesLength' => $this->totalSeriesLength,
			'seriesLength' => $this->seriesLength,
			'ratingMpaa' => $this->ratingMpaa,
			'ageRating' => $this->ageRating,
			'top10' => $this->top10,
			'top250' => $this->top250,
			'typeNumber' => $this->typeNumber,
			'status' => $this->status,
		];
	}
}
