<?php
/**
 * Plugin Name: NewsAPI WP Plugin
 * Description: Fetches data from NewsAPI.org API and displays top news from Mexico.
 * Version: 1.0
 * Author: Angel Vera
 */

// Enqueue necessary scripts/styles
function newsapi_enqueue_scripts() {
  wp_enqueue_style('newsapi-style', plugins_url('style.css', __FILE__ ), false, '1.0', 'all');
}
add_action('wp_enqueue_scripts', 'newsapi_enqueue_scripts');

// Function to fetch news data from NewsAPI.org
function newsapi_fetch_news() {
  $userAgent=$_SERVER['HTTP_USER_AGENT'];
  $endpoint = 'https://newsapi.org/v2/top-headlines?country=mx&apiKey=YOUR_API_KEY';

  // Fetch data from the API
  $curl = curl_init($endpoint);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
  $response = curl_exec($curl);

  if (curl_error($curl)) {
    return false; // If there's an error, return false
  }

  curl_close($curl);
  $data = json_decode($response);
  return $data->articles;
}

// Function to display news
function newsapi_display_news() {
  $articles = newsapi_fetch_news();

  if (!$articles) {
    echo 'Failed to fetch news.';
    return;
  }

  echo '<div class="newsapi-news-container">';
  foreach ($articles as $article) {
    echo '<div class="newsapi-news-item">';
    echo '<h4>' . esc_html($article->title) . '</h4>';
    echo '<a href="' . esc_url($article->url) . '" target="_blank">Read more</a>';
    echo '</div>';
  }
  echo '</div>';
}

// Shortcode to display news on a WordPress page or post
function newsapi_news_shortcode() {
  ob_start();
  newsapi_display_news();
  return ob_get_clean();
}
add_shortcode('newsapi_news', 'newsapi_news_shortcode');