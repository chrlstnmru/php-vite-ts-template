<?php
require_once 'config.php';

function vite(string $filepath = '', bool $include_globals = true): string
{
  $main_file = $include_globals ? [getJsTag('main.ts'), preloadImports('main.ts'), getCssTags('main.css')] : [];
  $file_tags = [getJsTag($filepath), preloadImports($filepath), getCssTags($filepath)];

  if (isDev()) {
    $main_file[] = '<script type="module" src="' . VITE_HOSTNAME . '/@vite/client"></script>';
  }

  return isset($filepath) || !empty($filepath) ?
    join("\n", array_unique(array_merge($main_file, $file_tags))) : // Merge the main file and the file tags
    join("\n", $main_file); // Return the main file only
}

function isDev(): bool
{
  static $state = null;
  if ($state !== null) {
    return $state;
  }

  $isDevEnv = isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'development';

  if (!$isDevEnv) {
    return false;
  }

  $viteHandle = curl_init(VITE_HOSTNAME . '/@vite/client');
  curl_setopt($viteHandle, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($viteHandle, CURLOPT_NOBODY, true);

  curl_exec($viteHandle);
  $error = curl_errno($viteHandle);
  curl_close($viteHandle);

  return $state = !$error;
}

function getViteManifest(): array
{
  $manifestPath = __DIR__ . '/../dist/.vite/manifest.json';
  if (!file_exists($manifestPath)) {
    throw new Exception('Vite manifest not found. Build your assets first');
  }

  $content = file_get_contents($manifestPath);
  $manifest = json_decode($content, true);
  if (!$manifest) {
    throw new Exception('Could not parse vite manifest');
  }

  return $manifest;
}

function getAssetUrl(string $name): string
{
  if (empty($name)) {
    return '';
  }
  if (isDev()) {
    return join('/', [VITE_HOSTNAME, $name]);
  }

  $manifest = getViteManifest();
  return isset($manifest[$name]) ? join('/', [BUILD_DIR, $manifest[$name]['file']]) : '';
}

function getCssUrls(string $entry): array
{
  $urls = [];
  $manifest = getViteManifest();

  if (str_ends_with($entry, '.css') && !empty($manifest[$entry])) {
    $urls[] = join('/', [BUILD_DIR, $manifest[$entry]['file']]);
  }

  if (!empty($manifest[$entry]['css'])) {
    foreach ($manifest[$entry]['css'] as $css) {
      $urls[] = join('/', [BUILD_DIR, $css]);
    }
  }

  if (!empty($manifest[$entry]['imports'])) {
    foreach ($manifest[$entry]['imports'] as $import) {
      if (!empty($manifest[$import]['css'])) {
        foreach ($manifest[$import]['css'] as $css) {
          $urls[] = join('/', [BUILD_DIR, $css]);
        }
      }
    }
  }

  return $urls;
}

function getImportUrls(string $entry): array
{
  $urls = [];
  $manifest = getViteManifest();

  if (!empty($manifest[$entry]['imports'])) {
    foreach ($manifest[$entry]['imports'] as $import) {
      $urls[] = join('/', [BUILD_DIR, $import]);
    }
  }

  return $urls;
}

function getCssTags(string $entry): string
{

  if (isDev()) {
    return '';
  }

  $tags = '';
  foreach (getCssUrls($entry) as $cssUrl) {
    $tags .= '<link rel="stylesheet" href="' . $cssUrl . '">';
  }
  return $tags;
}

function preloadImports(string $entry): string
{
  if (isDev()) {
    return '';
  }

  $tags = '';
  foreach (getImportUrls($entry) as $importUrl) {
    $tags .= '<link rel="modulepreload" href="' . $importUrl . '">';
  }

  return $tags;
}

function getJsTag(string $entry): string
{
  $url = getAssetUrl($entry);
  if (!$url) {
    return '';
  }
  return '<script type="module" src="' . $url . '"></script>';
}
