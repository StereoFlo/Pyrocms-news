<div class="news_article">
    <div class="article_heading">
        <h2><?= $article->title; ?></h2>
        <p><?= lang('news_posted_label'); ?>:
            <b><?= format_date($article->created_on); ?></b> <?= lang('news_views'); ?>: <b><?= $article->views; ?></b>
            <? if ($article->category->slug) { ?>
            | <?= lang('news_category_label'); ?>
            : <?= anchor('news/category/' . $article->category->slug, $article->category->title); ?>
        </p>
        <? } else { ?>
            </p>
        <? } ?>
    </div>
    <div class="article_body">
        <?= stripslashes($article->body); ?>
    </div>
</div>

<?= display_comments($article->id, 'news'); ?>
<p>&nbsp;</p>

