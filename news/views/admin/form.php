<section class="title">
	<? if ($this->method == 'create') { ?>
		<h4><?= lang('news_create_title'); ?></h4>
	<? } else { ?>
			<h4><?= sprintf(lang('news_edit_title'), $article->title); ?></h4>
	<? } ?>
</section>

<section class="item">

<?= form_open(uri_string(), 'class="crud"'); ?>

		<div class="tabs">
			<ul class="tab-menu">
				<li><a href="#news-content-tab"><span><?=lang('news_content_label'); ?></span></a></li>
				<li><a href="#news-options-tab"><span><?=lang('news_options_label'); ?></span></a></li>
			</ul>

			<div id="news-content-tab">
			<table style="border: 1px solid #eee;">
				<tr>
					<td><label for="category_id"><?=lang('news_category_label'); ?></label></td>
					<td><?=form_dropdown('category_id', array(lang('news_no_category_select_label')) + $categories, @$article->category_id) ?>
					[ <?=anchor('admin/news/categories/create', lang('news_new_category_label'), 'target="_blank"'); ?> ]</td>
				</tr>
				<tr>
					<td><label for="title"><?=lang('news_title_label'); ?></label></td>
					<td><?=form_input('title', htmlspecialchars_decode($article->title), 'maxlength="100"'); ?><span class="required-icon tooltip"><?=lang('required_label'); ?></span></td>
				</tr>
				<tr>
					<td><label for="slug"><?=lang('news_slug_label'); ?></label></td>
					<td>
					<?=form_input('slug', $article->slug, 'maxlength="100" class="width-20"'); ?>
					<span class="required-icon tooltip"><?=lang('required_label'); ?></span>
					</td>
				</tr>
				<tr>
					<td><label for="status"><?=lang('news_status_label'); ?></label></td>
					<td><?=form_dropdown('status', array('draft' => lang('news_draft_label'), 'live' => lang('news_live_label')), $article->status) ?></td>
				</tr>
				<tr>
					<td><label class="intro" for="intro"><?=lang('news_intro_label'); ?></label></td>
					<td><?=form_textarea(array('id' => 'intro', 'name' => 'intro', 'value' => $article->intro, 'rows' => 5, 'class' => 'wysiwyg-simple')); ?></td>
				</tr>
				<tr>
					<td>dewdwe</td>
					<td><?=form_textarea(array('id' => 'body', 'name' => 'body', 'value' => stripslashes($article->body), 'rows' => 50, 'class' => 'wysiwyg-advanced')); ?></td>
				</tr>
			</table>
			
			</div>
			<div id="news-options-tab">
			<table style="border: 1px solid #eee;">
				<tr>
					<td>
						<label><?=lang('news_date_label'); ?></label>
					</td>
					<td>
						<?=form_input('created_on', date('Y-m-d', $article->created_on), 'maxlength="10" id="datepicker" class="text width-20"'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<label class="time-meta"><?=lang('news_time_label'); ?></label>
					</td>
					<td>
						<?= form_dropdown('created_on_hour', $hours, date('H', $article->created_on)) ?>
						<?= form_dropdown('created_on_minute', $minutes, date('i', ltrim($article->created_on, '0'))) ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
<?= $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'save_exit', 'cancel'))); ?>

<?= form_close(); ?>
</section>