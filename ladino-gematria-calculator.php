<?php
/**
 * Plugin Name: Ladino Gematria Calculator
 * Description: מחשבון גימטריה שמתרגם שנה עברית לשנה לועזית
 * Version: 1.0
 * Author: ladino.org
 * 
 * תוסף זה מאפשר המרה של טקסט עברי לשנה לועזית באמצעות חישוב גימטרי
 * התוסף מוסיף shortcode [gematria_calculator] שניתן להוסיף לכל עמוד באתר
 * ניתן להתאים את המראה באמצעות CSS או מאפיינים נוספים ב-shortcode
 */

// מניעת גישה ישירה לקובץ
if (!defined('ABSPATH')) {
    exit;
}

class Ladino_Gematria_Calculator {

    public function __construct() {
        // רישום קובצי CSS ו-JavaScript
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // רישום shortcode
        add_shortcode('gematria_calculator', array($this, 'gematria_calculator_shortcode'));
        
        // רישום AJAX endpoint
        add_action('wp_ajax_calculate_gematria', array($this, 'calculate_gematria'));
        add_action('wp_ajax_nopriv_calculate_gematria', array($this, 'calculate_gematria'));
    }

    /**
     * רישום קבצי JavaScript ו-CSS
     */
    public function enqueue_scripts() {
        wp_enqueue_style('gematria-calculator-style', plugin_dir_url(__FILE__) . 'assets/css/gematria-calculator.css', array(), '1.0.0');
        wp_enqueue_script('gematria-calculator-script', plugin_dir_url(__FILE__) . 'assets/js/gematria-calculator.js', array('jquery'), '1.0.0', true);
        
        // העברת נתונים ל-JavaScript
        wp_localize_script('gematria-calculator-script', 'gematria_calculator_vars', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gematria_calculator_nonce')
        ));
    }

    /**
     * פונקציה ליצירת shortcode
     */
    public function gematria_calculator_shortcode($atts) {
        // מאפייני ברירת מחדל
        $atts = shortcode_atts(array(
            'placeholder' => 'הזן טקסט בעברית',
            'button_text' => 'חשב',
            'result_text' => 'השנה הלועזית היא:',
            'direction' => 'rtl',
            'class' => '',
        ), $atts);
        
        // התחלת הפלט
        ob_start();
        ?>
        <div class="gematria-calculator-container <?php echo esc_attr($atts['class']); ?>" dir="<?php echo esc_attr($atts['direction']); ?>">
            <div class="gematria-calculator-form">
                <input type="text" id="gematria-calculator-text" placeholder="<?php echo esc_attr($atts['placeholder']); ?>" />
                <button id="gematria-calculator-button"><?php echo esc_html($atts['button_text']); ?></button>
                <button id="gematria-calculator-clear" type="button"><?php echo esc_html($atts['clear_text'] ?? 'נקה'); ?></button>
            </div>
            <div id="gematria-calculator-result" class="gematria-calculator-result">
                <!-- התוצאה תוצג כאן -->
            </div>
            <div id="gematria-calculator-error" class="gematria-calculator-error"></div>
        </div>
        <?php
        
        // החזרת הפלט
        return ob_get_clean();
    }

    /**
     * פונקציה לחישוב גימטריה (AJAX endpoint)
     */
    public function calculate_gematria() {
        // בדיקת אבטחה
        if (!check_ajax_referer('gematria_calculator_nonce', 'nonce', false)) {
            wp_send_json_error('Security check failed');
            wp_die();
        }
        
        // קבלת טקסט מהבקשה
        $text = isset($_POST['text']) ? sanitize_text_field($_POST['text']) : '';
        
        if (empty($text)) {
            wp_send_json_error('Text is required');
            wp_die();
        }
        
        // ערכי גימטריה
        $gematria_values = array(
            'א' => 1,
            'ב' => 2,
            'ג' => 3,
            'ד' => 4,
            'ה' => 5,
            'ו' => 6,
            'ז' => 7,
            'ח' => 8,
            'ט' => 9,
            'י' => 10,
            'כ' => 20,
            'ל' => 30,
            'מ' => 40,
            'נ' => 50,
            'ס' => 60,
            'ע' => 70,
            'פ' => 80,
            'צ' => 90,
            'ק' => 100,
            'ר' => 200,
            'ש' => 300,
            'ת' => 400,
        );
        
        // חישוב הגימטריה
        $gematria = 0;
        $text_array = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($text_array as $letter) {
            if (isset($gematria_values[$letter])) {
                $gematria += $gematria_values[$letter];
            }
        }
        
        // הוספת 1240 לתוצאה (כפי שמופיע בקוד המקורי)
        $gematria += 1240;
        
        // החזרת התוצאה
        wp_send_json_success(array(
            'gematria' => $gematria,
            'text' => $text
        ));
        
        wp_die();
    }
}

// יצירת מופע של המחלקה
$ladino_gematria_calculator = new Ladino_Gematria_Calculator();