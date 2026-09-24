<?php
/** Firma yönetim araçları — MIS 360 Yol Yardım, güncelleme 3.0.0. */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('MIS360_Firma_Tools_V3', false)) {
final class MIS360_Firma_Tools_V3 {
    const VERSION = '3.0.0';
    const OPTION = 'mis360_firma_tools_job_v3';
    const NONCE = 'mis360_firma_tools_v3';
    private static $page_hook = '';

    public static function boot() {
        add_action('admin_menu', array(__CLASS__, 'menu'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'assets'));
        foreach (array('status', 'start', 'batch', 'cancel') as $operation) {
            add_action('wp_ajax_mis360_firma_v3_' . $operation, array(__CLASS__, 'ajax'));
        }
    }

    public static function menu() {
        self::$page_hook = add_management_page(
            'Firma Yönetim Araçları', 'Firma Yönetim Araçları', 'manage_options',
            'mis360-firma-tools', array(__CLASS__, 'page')
        );
    }

    public static function assets($hook) {
        if ($hook !== self::$page_hook || !current_user_can('manage_options')) {
            return;
        }
        wp_enqueue_style('mis360-firma-tools', get_template_directory_uri() . '/assets/css/firma-tools.css', array(), self::VERSION);
        wp_enqueue_script('mis360-firma-tools', get_template_directory_uri() . '/assets/js/firma-tools.js', array(), self::VERSION, true);
        wp_localize_script('mis360-firma-tools', 'mis360Firma', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(self::NONCE),
        ));
    }

    public static function page() {
        if (!current_user_can('manage_options')) {
            wp_die('Bu aracı kullanma yetkiniz yok.');
        }
        ?>
        <div class="wrap mis360-tools" id="mis360-firma-tools">
            <h1>Firma Yönetim Araçları</h1>
            <p>Firmaları tek seferde başlatın; silme işlemi küçük gruplar halinde otomatik devam etsin.</p>
            <div class="mis360-card">
                <h2>Firma temizleme</h2>
                <p class="mis360-warning"><strong>Kalıcı silme:</strong> Seçilen firma kayıtları geri alınamaz şekilde silinir. Başlatmadan önce veritabanı yedeğinizi alın.</p>
                <p>Firma kayıtlarına bağlı alanlar, yorumlar ve kategori bağlantıları temizlenir. Medya dosyaları, kategori/şehir tanımları, sayfalar, Hizmet Rehberi ve Bölgeler korunur.</p>
                <label class="mis360-choice"><input type="checkbox" id="mis-include-trash" checked> Çöp kutusundaki firmaları da sil</label>
                <p id="mis-scope">Firma sayısı kontrol ediliyor…</p>
                <dl class="mis360-counters">
                    <div><dt>Hedef firma</dt><dd id="mis-total">—</dd></div>
                    <div><dt>Silinen</dt><dd id="mis-deleted">0</dd></div>
                    <div><dt>Kalan</dt><dd id="mis-remaining">—</dd></div>
                </dl>
                <div class="mis360-progress-row">
                    <progress id="mis-progress" max="100" value="0" aria-label="Firma silme ilerlemesi"></progress>
                    <strong id="mis-percent">%0</strong>
                </div>
                <p id="mis-status" role="status" aria-live="polite">Bağlantı kuruluyor…</p>
                <p id="mis-changed" hidden></p>
                <div class="mis360-actions">
                    <button type="button" class="button button-primary" id="mis-delete-start" disabled>Tüm Firmaları Sil</button>
                    <button type="button" class="button" id="mis-delete-stop" disabled>Duraklat</button>
                    <button type="button" class="button" id="mis-delete-cancel" hidden>İşlemi sonlandır</button>
                    <button type="button" class="button" id="mis-refresh">Durumu yenile</button>
                </div>
                <p class="description">Her istek en fazla 100 firma işler. Duraklatma, devam eden küçük grup tamamlanınca uygulanır. Sayfa kapanırsa yeniden açıp “Devam et” ile sürdürebilirsiniz.</p>
                <p class="description">İşlem başladıktan sonra eklenen yeni firmalar bu silme işlemine dahil edilmez.</p>
                <noscript><p class="mis360-warning">Bu araç için tarayıcınızda JavaScript etkin olmalıdır.</p></noscript>
            </div>
        </div>
        <?php
    }

    private static function save($job) {
        if (!update_option(self::OPTION, $job, false) && get_option(self::OPTION, null) !== $job) {
            throw new RuntimeException('İşlem durumu kaydedilemedi. Veritabanını kontrol edip durumu yenileyin.');
        }
    }

    private static function where($job) {
        global $wpdb;
        return $wpdb->prepare("post_type = %s AND ID <= %d", 'firma', $job['max_id'])
            . ($job['include_trash'] ? '' : " AND post_status <> 'trash'");
    }

    private static function count($job) {
        global $wpdb;
        $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE " . self::where($job));
        if ($count === null || $wpdb->last_error) {
            throw new RuntimeException('Firma sayısı okunamadı. Veritabanı bağlantısını kontrol edin.');
        }
        return (int) $count;
    }

    private static function recover($job) {
        // A response can be lost after WordPress deletes a record. Recover the saved pending ID.
        if (!empty($job['pending'])) {
            global $wpdb;
            $exists = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE ID = %d", $job['pending']));
            if ($wpdb->last_error) {
                throw new RuntimeException('Önceki silme işlemi doğrulanamadı. Durumu yeniden kontrol edin.');
            }
            if (!$exists) {
                $job['deleted']++;
            }
            $job['pending'] = 0;
            self::save($job);
        }
        return $job;
    }

    private static function snapshot($job) {
        if (!$job) {
            return null;
        }
        $remaining = self::count($job);
        return array(
            'id' => $job['user_id'] === get_current_user_id() ? $job['id'] : '',
            'owned' => $job['user_id'] === get_current_user_id(),
            'phase' => $job['phase'], 'sequence' => $job['sequence'],
            'total' => $job['total'], 'deleted' => $job['deleted'], 'remaining' => $remaining,
            'includeTrash' => $job['include_trash'],
            'changed' => max(0, $job['total'] - $job['deleted'] - $remaining),
            'percent' => $remaining === 0 ? 100 : min(99, max(0, (int) floor(100 * ($job['total'] - $remaining) / max(1, $job['total'])))),
            'message' => $job['message'],
        );
    }

    private static function process($operation) {
        global $wpdb;
        $job = get_option(self::OPTION, null);
        if ($job) {
            $job = self::recover($job);
        }
        if ($operation === 'status') {
            $all = array('max_id' => PHP_INT_MAX, 'include_trash' => true);
            $active = array('max_id' => PHP_INT_MAX, 'include_trash' => false);
            return array('all' => self::count($all), 'active' => self::count($active), 'job' => self::snapshot($job));
        }
        if ($operation === 'start') {
            if (isset($_POST['confirm']) && $_POST['confirm'] === 'SIL') {
                if ($job && in_array($job['phase'], array('ready', 'error'), true)) {
                    throw new RuntimeException('Devam eden bir işlem var. Durumu yenileyip devam edin veya işlemi sonlandırın.');
                }
                $max = $wpdb->get_var($wpdb->prepare("SELECT COALESCE(MAX(ID), 0) FROM {$wpdb->posts} WHERE post_type = %s", 'firma'));
                if ($max === null || $wpdb->last_error) {
                    throw new RuntimeException('Firma kayıtları okunamadı.');
                }
                $job = array(
                    'id' => wp_generate_uuid4(), 'user_id' => get_current_user_id(),
                    'include_trash' => isset($_POST['include_trash']) && $_POST['include_trash'] === '1',
                    'max_id' => (int) $max, 'total' => 0, 'deleted' => 0,
                    'sequence' => 0, 'pending' => 0, 'phase' => 'ready', 'message' => '',
                );
                $job['total'] = self::count($job);
                if ($job['total'] === 0) {
                    $job['phase'] = 'completed';
                }
                self::save($job);
                return array('job' => self::snapshot($job));
            }
            throw new RuntimeException('Silme onayı alınamadı.');
        }

        $id = isset($_POST['job_id']) && is_string($_POST['job_id']) ? sanitize_text_field(wp_unslash($_POST['job_id'])) : '';
        if (!$job || !$id || !hash_equals($job['id'], $id) || $job['user_id'] !== get_current_user_id()) {
            throw new RuntimeException('Bu silme işlemi size ait değil veya süresi değişmiş. Durumu yenileyin.');
        }
        if ($operation === 'cancel') {
            $job['phase'] = 'cancelled';
            $job['message'] = 'İşlem sonlandırıldı. Daha önce silinen firmalar geri alınmaz.';
            self::save($job);
            return array('job' => self::snapshot($job));
        }
        if (!in_array($job['phase'], array('ready', 'error'), true)) {
            return array('job' => self::snapshot($job));
        }
        $sequence = isset($_POST['sequence']) ? absint($_POST['sequence']) : 0;
        if ($sequence <= $job['sequence']) {
            return array('job' => self::snapshot($job)); // Repeated request: never delete an extra batch.
        }
        if ($sequence !== $job['sequence'] + 1) {
            throw new RuntimeException('İstek sırası değişti. Durumu yenileyip devam edin.');
        }
        $ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE " . self::where($job) . ' ORDER BY ID ASC LIMIT 100');
        if ($wpdb->last_error) {
            throw new RuntimeException('Silinecek firmalar okunamadı.');
        }
        $started = microtime(true);
        $limit = (int) ini_get('max_execution_time');
        $budget = $limit > 0 ? max(1, min(8, $limit / 3)) : 8;
        $job['phase'] = 'ready';
        $job['message'] = '';
        foreach ($ids as $id) {
            $id = (int) $id;
            $post = get_post($id);
            if (!$post || $post->post_type !== 'firma' || (!$job['include_trash'] && $post->post_status === 'trash')) {
                continue;
            }
            if (!current_user_can('delete_post', $id)) {
                $job['phase'] = 'error';
                $job['message'] = sprintf('Firma #%d için silme yetkisi yok. İşlem durduruldu.', $id);
                break;
            }
            $job['pending'] = $id;
            self::save($job);
            $result = wp_delete_post($id, true);
            if (!$result || is_wp_error($result) || get_post($id)) {
                $job['pending'] = 0;
                $job['phase'] = 'error';
                $job['message'] = sprintf('Firma #%d silinemedi. Engelleyen eklentiyi veya veritabanı hatasını kontrol edip yeniden deneyin.', $id);
                break;
            }
            $job['deleted']++;
            $job['pending'] = 0;
            self::save($job);
            if (microtime(true) - $started >= $budget) {
                break;
            }
        }
        $job['sequence'] = $sequence;
        if (self::count($job) === 0) {
            $job['phase'] = 'completed';
            $job['message'] = 'Tamamlandı. İşlem kapsamındaki tüm firmalar silindi.';
        }
        self::save($job);
        return array('job' => self::snapshot($job));
    }

    public static function ajax() {
        if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            wp_send_json_error(array('message' => 'Bu işlem POST isteği gerektirir.'), 405);
        }
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Bu aracı yalnızca yetkili yöneticiler kullanabilir.'), 403);
        }
        if (!check_ajax_referer(self::NONCE, 'nonce', false)) {
            wp_send_json_error(array('message' => 'Oturum doğrulaması yenilenmeli. Sayfayı yenileyin.'), 403);
        }
        $type = get_post_type_object('firma');
        if (!$type || !current_user_can($type->cap->delete_posts)) {
            wp_send_json_error(array('message' => 'Firma kayıt türü veya firma silme yetkisi bulunamadı.'), 403);
        }
        $action = isset($_POST['action']) && is_string($_POST['action']) ? sanitize_key($_POST['action']) : '';
        $operation = str_replace('mis360_firma_v3_', '', $action);
        if (!in_array($operation, array('status', 'start', 'batch', 'cancel'), true)) {
            wp_send_json_error(array('message' => 'Geçersiz işlem.'), 400);
        }
        global $wpdb;
        // A connection-scoped lock serializes tabs/admins and is released even if PHP terminates.
        $lock = 'mis360_firma_' . md5(DB_NAME . $wpdb->prefix);
        $locked = $wpdb->get_var($wpdb->prepare('SELECT GET_LOCK(%s, 0)', $lock));
        if ((string) $locked !== '1') {
            wp_send_json_error(array('message' => 'Başka bir firma isteği halen çalışıyor. Biraz bekleyip Durumu yenile düğmesine basın.'), 409);
        }
        $error = '';
        try {
            $data = self::process($operation);
        } catch (Throwable $exception) {
            $error = $exception instanceof RuntimeException ? $exception->getMessage() : 'İşlem kesildi. Durumu yenileyip tekrar deneyin; sunucu hata günlüğünü kontrol edin.';
        } finally {
            $wpdb->get_var($wpdb->prepare('SELECT RELEASE_LOCK(%s)', $lock));
        }
        if ($error) {
            wp_send_json_error(array('message' => $error), 400);
        }
        wp_send_json_success($data);
    }
}

MIS360_Firma_Tools_V3::boot();
}
