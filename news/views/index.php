<?php if (!empty($news)): ?>
    <?php foreach ($news as $article): ?>
        <div class="news_article">
            <!-- Article heading -->
            <div class="article_heading">
                <h2><?= anchor('news/' . date('Y/m', $article->created_on) . '/' . $article->slug, $article->title); ?></h2>
                <p><?= lang('news_posted_label'); ?>: <b><?= format_date($article->created_on); ?></b>
                    | <?= lang('news_views'); ?>: <b><?= $article->views; ?></b>
                    <?php if ($article->category_slug) { ?>
                    | <?= lang('news_category_label'); ?>
                    : <?= anchor('news/category/' . $article->category_slug, $article->category_title); ?>
                </p>
                <? } else { ?>
                    </p>
                <? } ?>
            </div>
            <div class="article_body">
                <?php echo stripslashes($article->intro); ?>
            </div>
        </div>
    <?php endforeach; ?>

    <?php echo $pagination['links']; ?>

<?php else: ?>
    <p><?php echo lang('news_currently_no_articles'); ?></p>
<?php endif; ?>