<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class TmdbService
{
    private string $baseUrl;

    private string $accessToken;

    private string $imageBaseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url');
        $this->accessToken = config('services.tmdb.access_token');
        $this->imageBaseUrl = config('services.tmdb.image_base_url');
    }

    private function get(string $endpoint, array $params = [])
    {
        $response = Http::withHeaders([
            'accept' => 'application/json',
            'Authorization' => 'Bearer '.$this->accessToken,
        ])->get("{$this->baseUrl}/{$endpoint}", $params);

        if ($response->failed()) {
            throw new \Exception('TMDB API request failed: '.$response->body());
        }

        return $response->json();
    }

    /**
     * Get cached data or fetch from API
     */
    private function getCached(string $key, string $endpoint, array $params = [], int $minutes = 60)
    {
        return Cache::remember($key, now()->addMinutes($minutes), function () use ($endpoint, $params) {
            return $this->get($endpoint, $params);
        });
    }

    // MOVIES

    public function getPopularMovies(int $page = 1)
    {
        return $this->getCached(
            "movies.popular.page.{$page}",
            'movie/popular',
            ['page' => $page],
            120
        );
    }

    public function getTopRatedMovies(int $page = 1)
    {
        return $this->getCached(
            "movies.top_rated.page.{$page}",
            'movie/top_rated',
            ['page' => $page],
            120
        );
    }

    public function getUpcomingMovies(int $page = 1)
    {
        return $this->getCached(
            "movies.upcoming.page.{$page}",
            'movie/upcoming',
            ['page' => $page],
            60
        );
    }

    public function getNowPlayingMovies(int $page = 1)
    {
        return $this->getCached(
            "movies.now_playing.page.{$page}",
            'movie/now_playing',
            ['page' => $page],
            30
        );
    }

    public function getMovieDetails(int $movieId)
    {
        return $this->getCached(
            "movie.{$movieId}",
            "movie/{$movieId}",
            ['append_to_response' => 'credits,videos,similar,recommendations'],
            1440 // 24 hours
        );
    }

    public function getSimilarMovies(int $movieId, int $page = 1)
    {
        return $this->getCached(
            "movie.{$movieId}.similar.page.{$page}",
            "movie/{$movieId}/similar",
            ['page' => $page],
            120
        );
    }

    public function getMovieRecommendations(int $movieId, int $page = 1)
    {
        return $this->getCached(
            "movie.{$movieId}.recommendations.page.{$page}",
            "movie/{$movieId}/recommendations",
            ['page' => $page],
            120
        );
    }

    public function searchMovies(string $query, int $page = 1)
    {
        return $this->get('search/movie', [
            'query' => $query,
            'page' => $page,
        ]);
    }

    public function getPopularTvShows(int $page = 1)
    {
        return $this->getCached(
            "tv.popular.page.{$page}",
            'tv/popular',
            ['page' => $page],
            120
        );
    }

    public function getTopRatedTvShows(int $page = 1)
    {
        return $this->getCached(
            "tv.top_rated.page.{$page}",
            'tv/top_rated',
            ['page' => $page],
            120
        );
    }

    public function getAiringTodayTvShows(int $page = 1)
    {
        return $this->getCached(
            "tv.airing_today.page.{$page}",
            'tv/airing_today',
            ['page' => $page],
            30
        );
    }

    public function getOnTheAirTvShows(int $page = 1)
    {
        return $this->getCached(
            "tv.on_the_air.page.{$page}",
            'tv/on_the_air',
            ['page' => $page],
            60
        );
    }

    public function getTvShowDetails(int $tvId)
    {
        return $this->getCached(
            "tv.{$tvId}",
            "tv/{$tvId}",
            ['append_to_response' => 'credits,videos,similar,recommendations'],
            1440 // 24 hours
        );
    }

    public function getTvSeasonDetails(int $tvId, int $seasonNumber)
    {
        return $this->getCached(
            "tv.{$tvId}.season.{$seasonNumber}",
            "tv/{$tvId}/season/{$seasonNumber}",
            [],
            1440
        );
    }

    public function getSimilarTvShows(int $tvId, int $page = 1)
    {
        return $this->getCached(
            "tv.{$tvId}.similar.page.{$page}",
            "tv/{$tvId}/similar",
            ['page' => $page],
            120
        );
    }

    public function searchTvShows(string $query, int $page = 1)
    {
        return $this->get('search/tv', [
            'query' => $query,
            'page' => $page,
        ]);
    }

    // PEOPLE

    public function getPersonDetails(int $personId)
    {
        return $this->getCached(
            "person.{$personId}",
            "person/{$personId}",
            ['append_to_response' => 'movie_credits,tv_credits'],
            1440
        );
    }

    public function searchPeople(string $query, int $page = 1)
    {
        return $this->get('search/person', [
            'query' => $query,
            'page' => $page,
        ]);
    }

    // GENRES

    public function getMovieGenres()
    {
        return $this->getCached(
            'genres.movies',
            'genre/movie/list',
            [],
            10080 // 7 days
        );
    }

    public function getTvGenres()
    {
        return $this->getCached(
            'genres.tv',
            'genre/tv/list',
            [],
            10080 // 7 days
        );
    }

    public function discoverMoviesByGenre(int $genreId, int $page = 1, array $additionalParams = [])
    {
        $params = array_merge([
            'with_genres' => $genreId,
            'page' => $page,
            'sort_by' => 'popularity.desc',
        ], $additionalParams);

        return $this->get('discover/movie', $params);
    }

    public function discoverTvShowsByGenre(int $genreId, int $page = 1, array $additionalParams = [])
    {
        $params = array_merge([
            'with_genres' => $genreId,
            'page' => $page,
            'sort_by' => 'popularity.desc',
        ], $additionalParams);

        return $this->get('discover/tv', $params);
    }

    /**
     * Get trending content
     *
     * @param  string  $mediaType  'all', 'movie', 'tv', 'person'
     * @param  string  $timeWindow  'day' or 'week'
     */
    public function getTrending(string $mediaType = 'all', string $timeWindow = 'week')
    {
        return $this->getCached(
            "trending.{$mediaType}.{$timeWindow}",
            "trending/{$mediaType}/{$timeWindow}",
            [],
            60
        );
    }

    public function multiSearch(string $query, int $page = 1)
    {
        return $this->get('search/multi', [
            'query' => $query,
            'page' => $page,
        ]);
    }

    public function getImageUrl(string $path, string $size = 'original'): ?string
    {
        if (! $path) {
            return null;
        }

        return "{$this->imageBaseUrl}/{$size}{$path}";
    }

    public function getConfiguration()
    {
        return $this->getCached(
            'tmdb.configuration',
            'configuration',
            [],
            10080 // 7 days
        );
    }
}
