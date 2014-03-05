<h1><?= $article->title; ?></h1>

<p style="float:left; width: 40%;">
	<?= anchor('news/' .date('Y/m', $article->created_on) .'/'. $article->slug, NULL, 'target="_blank"'); ?>
</p>

<p style="float:right; width: 40%; text-align: right;">
	<?= anchor('admin/news/edit/'. $article->id, lang('news_edit_label'), ' target="_parent"'); ?>
</p>

<iframe src="<?= site_url('news/' .date('Y/m', $article->created_on) .'/'. $article->slug); ?>" width="99%" height="480"></iframe>