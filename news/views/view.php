<div class="news_article">
	<div class="article_heading">
		<h2><?= $article->title; ?></h2>
		<p class="article_date"><?= lang('news_posted_label');?>: <?= format_date($article->created_on); ?></p>
		<? if($article->category->slug) { ?>
		<p class="article_category">
			<?= lang('news_category_label');?>: <?= anchor('news/category/'.$article->category->slug, $article->category->title);?>
		</p>
		<? } ?>
	</div>
	<div class="article_body">
		<?= stripslashes($article->body); ?>
	</div>
</div>

<?/*= display_comments($article->id); */?>
