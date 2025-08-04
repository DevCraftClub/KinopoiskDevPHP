# FilterField

**Описание:** Enum для полей фильтрации
Этот enum содержит все возможные поля, которые можно использовать
при фильтрации данных через API Kinopoisk.dev

## `getFieldType()`

**Описание:** Возвращает тип поля

## `supportsIncludeExclude()`

**Описание:** Проверяет, поддерживает ли поле операторы включения/исключения

## `supportsRange()`

**Описание:** Проверяет, поддерживает ли поле диапазоны

## `getDefaultOperator()`

**Описание:** Возвращает оператор по умолчанию для поля

## `getBaseField()`

**Описание:** Возвращает базовое поле для составных полей (например, rating.kp -> rating)

## `getSubField()`

**Описание:** Возвращает подполе для составных полей (например, rating.kp -> kp)

## Cases

### `ID`

**Значение:** `'id'`

### `EXTERNAL_ID`

**Значение:** `'externalId'`

### `NAME`

**Значение:** `'name'`

### `EN_NAME`

**Значение:** `'enName'`

### `ALTERNATIVE_NAME`

**Значение:** `'alternativeName'`

### `NAMES`

**Значение:** `'names.name'`

### `DESCRIPTION`

**Значение:** `'description'`

### `SHORT_DESCRIPTION`

**Значение:** `'shortDescription'`

### `SLOGAN`

**Значение:** `'slogan'`

### `TYPE`

**Значение:** `'type'`

### `TYPE_NUMBER`

**Значение:** `'typeNumber'`

### `IS_SERIES`

**Значение:** `'isSeries'`

### `STATUS`

**Значение:** `'status'`

### `YEAR`

**Значение:** `'year'`

### `RELEASE_YEARS`

**Значение:** `'releaseYears'`

### `UPDATED_AT`

**Значение:** `'updatedAt'`

### `CREATED_AT`

**Значение:** `'createdAt'`

### `RATING_KP`

**Значение:** `'rating.kp'`

### `RATING_IMDB`

**Значение:** `'rating.imdb'`

### `RATING_TMDB`

**Значение:** `'rating.tmdb'`

### `RATING_FILM_CRITICS`

**Значение:** `'rating.filmCritics'`

### `RATING_RUSSIAN_FILM_CRITICS`

**Значение:** `'rating.russianFilmCritics'`

### `RATING_AWAIT`

**Значение:** `'rating.await'`

### `RATING_MPAA`

**Значение:** `'ratingMpaa'`

### `AGE_RATING`

**Значение:** `'ageRating'`

### `VOTES_KP`

**Значение:** `'votes.kp'`

### `VOTES_IMDB`

**Значение:** `'votes.imdb'`

### `VOTES_TMDB`

**Значение:** `'votes.tmdb'`

### `VOTES_FILM_CRITICS`

**Значение:** `'votes.filmCritics'`

### `VOTES_RUSSIAN_FILM_CRITICS`

**Значение:** `'votes.russianFilmCritics'`

### `VOTES_AWAIT`

**Значение:** `'votes.await'`

### `MOVIE_LENGTH`

**Значение:** `'movieLength'`

### `SERIES_LENGTH`

**Значение:** `'seriesLength'`

### `TOTAL_SERIES_LENGTH`

**Значение:** `'totalSeriesLength'`

### `GENRES`

**Значение:** `'genres.name'`

### `COUNTRIES`

**Значение:** `'countries.name'`

### `POSTER`

**Значение:** `'poster'`

### `BACKDROP`

**Значение:** `'backdrop'`

### `LOGO`

**Значение:** `'logo'`

### `TICKETS_ON_SALE`

**Значение:** `'ticketsOnSale'`

### `VIDEOS`

**Значение:** `'videos'`

### `NETWORKS`

**Значение:** `'networks'`

### `PERSONS`

**Значение:** `'persons'`

### `PERSONS_NAME`

**Значение:** `'persons.name'`

### `PERSONS_ID`

**Значение:** `'persons.id'`

### `PERSONS_PROFESSION`

**Значение:** `'persons.profession'`

### `FACTS`

**Значение:** `'facts'`

### `FEES`

**Значение:** `'fees'`

### `PREMIERE`

**Значение:** `'premiere'`

### `PREMIERE_WORLD`

**Значение:** `'premiere.world'`

### `PREMIERE_RUSSIA`

**Значение:** `'premiere.russia'`

### `PREMIERE_USA`

**Значение:** `'premiere.usa'`

### `SIMILAR_MOVIES`

**Значение:** `'similarMovies'`

### `SEQUELS_AND_PREQUELS`

**Значение:** `'sequelsAndPrequels'`

### `WATCHABILITY`

**Значение:** `'watchability'`

### `LISTS`

**Значение:** `'lists'`

### `TOP_10`

**Значение:** `'top10'`

### `TOP_250`

**Значение:** `'top250'`

### `SEASONS_INFO`

**Значение:** `'seasonsInfo'`

### `BUDGET`

**Значение:** `'budget'`

### `AUDIENCE`

**Значение:** `'audience'`

