<h2 id="page_title"><?= $category->title; ?></h2>
<?php if (!empty($news)): ?>
<?php foreach ($news as $article): ?>
	<div class="news_article">
		<!-- Article heading -->
		<div class="article_heading">
			<h2><?=  anchor('news/' .date('Y/m', $article->created_on) .'/'. $article->slug, $article->title); ?></h2>
			<p><?= lang('news_posted_label');?>: <b><?= format_date($article->created_on); ?></b>
			<?php if($article->category_slug) { ?>
				| <?= lang('news_category_label');?>: <?= anchor('news/category/'.$article->category_slug, $article->category_title);?>
			</p>
			<? } else { ?>
			</p>
			<? } ?>
		</div>
		<div class="article_body">
			<?= stripslashes($article->intro); ?>
		</div>
	</div>
<?php endforeach; ?>

<?= $pagination['links']; ?>

<?php else: ?>
	<p><?= lang('news_currently_no_articles');?></p>
<?php endif; ?>