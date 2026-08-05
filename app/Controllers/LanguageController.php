<?php
class LanguageController extends Controller {
    public function switchLang($lang) {
        $allowedLangs = ['en', 'ar'];
        if (in_array($lang, $allowedLangs)) {
            $_SESSION['lang'] = $lang;
            // Also set a cookie for 30 days
            setcookie('lang', $lang, time() + (86400 * 30), "/");
        }
        
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL . '/';
        $this->redirect($referer);
    }
}
