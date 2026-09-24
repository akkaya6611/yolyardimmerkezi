<?php
define('WP_USE_THEMES', false);
require('C:/Users/Serkan/Local Sites/yol-yardm-merkezi/app/public/wp-load.php');
 = get_terms(['taxonomy' => 'firma_kategori', 'hide_empty' => false]);
if (!is_wp_error()) {
    foreach ( as ) {
        echo ->term_id . ' | ' . ->name . ' | ' . ->slug . ' | ' . ->count . PHP_EOL;
    }
} else {
    echo 'Error: ' . ->get_error_message();
}
