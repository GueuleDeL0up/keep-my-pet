<?php
// Language management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current language from session, DB (if logged in), or default
function getCurrentLanguage()
{
    global $_SESSION, $db;
    
    // Priority: session > DB (if logged in) > browser > default
    if (!empty($_SESSION['language'])) {
        return $_SESSION['language'];
    }
    
    if (!empty($_SESSION['user_id']) && isset($db)) {
        try {
            $stmt = $db->prepare("SELECT language FROM users WHERE id = ? LIMIT 1");
            $stmt->execute([$_SESSION['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && !empty($result['language'])) {
                $_SESSION['language'] = $result['language'];
                return $result['language'];
            }
        } catch (Exception $e) {
            // Si erreur BD, continuer sans BD
        }
    }
    
    // Browser language detection
    if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        if (in_array($lang, ['en', 'es', 'fr'])) {
            return $lang;
        }
    }
    
    return 'fr'; // Default
}

// Set language
function setLanguage($lang)
{
    global $db;
    
    $valid_langs = ['fr', 'en', 'es'];
    if (!in_array($lang, $valid_langs)) {
        $lang = 'fr';
    }
    
    $_SESSION['language'] = $lang;
    
    // Save to DB if logged in and DB is available
    if (!empty($_SESSION['user_id']) && isset($db)) {
        try {
            $stmt = $db->prepare("UPDATE users SET language = ? WHERE id = ?");
            $stmt->execute([$lang, $_SESSION['user_id']]);
        } catch (Exception $e) {
            // Si erreur BD, continuer sans sauvegarder
        }
    }
    
    return $lang;
}

// Load language file
function loadLanguage($lang = null)
{
    if (!$lang) {
        $lang = getCurrentLanguage();
    }
    
    $file = __DIR__ . "/../Languages/{$lang}.php";
    if (file_exists($file)) {
        return require $file;
    }
    
    // Fallback to French
    return require __DIR__ . "/../Languages/fr.php";
}

// Translation function
function t($key, $lang = null)
{
    if (!$lang) {
        $lang = getCurrentLanguage();
    }
    
    $translations = loadLanguage($lang);
    
    return $translations[$key] ?? $key;
}

// Format date according to language
function formatDate($date, $format = 'long')
{
    $lang = getCurrentLanguage();
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    
    // Locale mapping
    $locales = [
        'fr' => 'fr_FR',
        'en' => 'en_US',
        'es' => 'es_ES'
    ];
    
    $locale = $locales[$lang] ?? 'fr_FR';
    
    // Use IntlDateFormatter instead of deprecated strftime()
    if ($format === 'long') {
        // e.g., "mercredi 21 janvier 2026"
        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        return $formatter->format($timestamp);
    } elseif ($format === 'medium') {
        // e.g., "21 janvier 2026"
        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
        return $formatter->format($timestamp);
    } elseif ($format === 'short') {
        // e.g., "21/01/2026"
        return date('d/m/Y', $timestamp);
    } elseif ($format === 'day') {
        // e.g., "mercredi"
        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $formatter->setPattern('EEEE');
        return $formatter->format($timestamp);
    } elseif ($format === 'month') {
        // e.g., "janvier"
        $formatter = new IntlDateFormatter($locale, IntlDateFormatter::FULL, IntlDateFormatter::NONE);
        $formatter->setPattern('MMMM');
        return $formatter->format($timestamp);
    } else {
        return date($format, $timestamp);
    }
}

// Initialize
$current_language = getCurrentLanguage();
$_SESSION['language'] = $current_language;
