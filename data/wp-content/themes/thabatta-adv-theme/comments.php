<?php
if (post_password_required()) {
    return;
}
?>
<div id="comments" class="comments-area">
    <h3 class="comments-title">Comentários</h3>
    <?php if (have_comments()) : ?>
        <ol class="comment-list">
            <?php wp_list_comments(array('style' => 'ol', 'short_ping' => true)); ?>
        </ol>
        <?php the_comments_navigation(); ?>
    <?php endif; ?>
    <?php if (comments_open()) : ?>
        <div class="comment-respond">
            <?php comment_form([
                'title_reply' => '',
                'label_submit' => 'Publicar comentário'
            ]); ?>
        </div>
    <?php endif; ?>
</div> 