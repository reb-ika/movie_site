<?php
header("Content-Type: application/json");

echo json_encode([
    "success" => true,
    "message" => "🎬 Welcome to CineVault API!",
    "version" => "1.0",
    "endpoints" => [
        "auth" => "/api/auth/",
        "users" => "/api/users/",
        "movies" => "/api/movies/",
        "reviews" => "/api/reviews/",
        "comments" => "/api/comments/",
        "watchlist" => "/api/watchlist/",
        "likes" => "/api/likes/",
        "ratings" => "/api/ratings/",
        "notifications" => "/api/notifications/",
        "favorites" => "/api/favorites/"
    ]
]);
