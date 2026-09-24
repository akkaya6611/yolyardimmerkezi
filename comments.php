<?php
/**
 * Yol Yardım Merkezi - Modern Yorumlar ve Değerlendirmeler Şablonu (comments.php)
 *
 * @package Yol_Yardim_Merkezi
 */

if (!defined('ABSPATH')) {
    exit;
}

if (post_password_required()) {
    return;
}
?>

<div id="comments" class="yym-comments-area">
    <?php if (have_comments()) : ?>
        <h4 class="yym-comments-subheading">
            <?php
            $comment_count = get_comments_number();
            printf(
                esc_html(_n('%1$s Müşteri Yorumu', '%1$s Müşteri Yorumu', $comment_count, 'yol-yardim-merkezi')),
                number_format_i18n($comment_count)
            );
            ?>
        </h4>

        <ul class="yym-comment-list">
            <?php
            wp_list_comments(array(
                'style'       => 'ul',
                'short_ping'  => true,
                'avatar_size' => 48,
                'callback'    => function($comment, $args, $depth) {
                    $GLOBALS['comment'] = $comment;
                    ?>
                    <li <?php comment_class('yym-comment-item'); ?> id="comment-<?php comment_ID(); ?>">
                        <article class="yym-comment-body">
                            <div class="yym-comment-header">
                                <div class="yym-comment-author-wrap">
                                    <div class="yym-comment-avatar-fallback">
                                        <?php echo esc_html(mb_strtoupper(mb_substr(get_comment_author(), 0, 1, 'UTF-8'), 'UTF-8')); ?>
                                    </div>
                                    <div class="yym-comment-meta-col">
                                        <div class="yym-comment-name-row">
                                            <strong class="yym-comment-author-name"><?php comment_author(); ?></strong>
                                        </div>
                                        <div class="yym-comment-sub-row">
                                            <time class="yym-comment-date" datetime="<?php comment_time('c'); ?>">
                                                <?php printf(esc_html__('%1$s önce', 'yol-yardim-merkezi'), human_time_diff(get_comment_time('U'), current_time('timestamp'))); ?>
                                            </time>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="yym-comment-content">
                                <?php comment_text(); ?>
                            </div>
                        </article>
                    </li>
                    <?php
                }
            ));
            ?>
        </ul>

        <?php the_comments_navigation(); ?>

    <?php else : ?>
        <p class="yym-comments-empty">Henüz yorum yapılmamış.</p>
    <?php endif; ?>

    <!-- YORUM VE DEĞERLENDİRME FORMU -->
    <div class="yym-comment-form-wrap">
        <?php
        $commenter = wp_get_current_commenter();
        $req       = get_option('require_name_email');
        $aria_req  = ($req ? " aria-required='true' required" : '');

        $fields = array(
            'author' => '<div class="yym-cform-row"><div class="yym-cform-col">' .
                        '<label for="author" class="yym-cform-label">Adınız Soyadınız <span class="required">*</span></label>' .
                        '<input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" placeholder="Örn: Ahmet Yılmaz" class="yym-cform-input"' . $aria_req . ' />' .
                        '</div>',
            'email'  => '<div class="yym-cform-col">' .
                        '<label for="email" class="yym-cform-label">E-Posta Adresiniz <span class="required">*</span> <small>(Yayınlanmaz)</small></label>' .
                        '<input id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" placeholder="ahmet@example.com" class="yym-cform-input"' . $aria_req . ' />' .
                        '</div></div>',
            'url'    => '', // Çekici firma yorumunda web sitesi inputu gereksizdir, kaldırıldı
        );

        comment_form(array(
            'fields'               => $fields,
            'comment_field'        => '<div class="yym-cform-group">' .
                                      '<label for="comment" class="yym-cform-label">Hizmet Deneyiminiz ve Yorumunuz <span class="required">*</span></label>' .
                                      '<textarea id="comment" name="comment" cols="45" rows="4" placeholder="Bu firmadan aldığınız yol yardım ve çekici hizmetiyle ilgili deneyiminizi yazın (varış süresi, iletişim, fiyat vb.)..." class="yym-cform-textarea" required></textarea>' .
                                      '</div>',
            'title_reply'          => '✍️ Bu Firmayı Değerlendirin & Yorum Yapın',
            'title_reply_to'       => '✍️ %s İsimli Müşteriye Yanıt Ver',
            'title_reply_before'   => '<h4 id="reply-title" class="yym-cform-title">',
            'title_reply_after'    => '</h4>',
            'comment_notes_before' => '<p class="yym-cform-notes">Yorumunuz onaylandıktan sonra firma profilinde görüntülenecektir. E-posta adresiniz gizli tutulur.</p>',
            'comment_notes_after'  => '',
            'class_form'           => 'yym-modern-comment-form',
            'class_submit'         => 'yym-btn-submit-review',
            'label_submit'         => '⭐ Değerlendirmeyi Gönder',
            'submit_button'        => '<button type="submit" name="%1$s" id="%2$s" class="%3$s"><span>%4$s</span></button>',
        ));
        ?>
    </div>
</div>
