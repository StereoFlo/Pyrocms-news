<section class="title">
	<h4><?= lang('cat_list_title');?></h4>
</section>
<section class="item">
<?= form_open('admin/news/categories/delete'); ?>
	<table border="0" class="table-list">
		<thead>
		<tr>
			<th style="width: 20px;"><?= form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all'));?></th>
			<th><?= lang('cat_category_label');?></th>
			<th style="width:10em"><span><?= lang('cat_actions_label');?></span></th>
		</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3">
					<div class="inner"><? $this->load->view('admin/partials/pagination'); ?></div>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<? if ($categories) { ?>
			<? foreach ($categories as $category) { ?>
			<tr>
				<td><?= form_checkbox('action_to[]', $category->id); ?></td>
				<td><?= $category->title;?></td>
				<td>
					<?= anchor('admin/news/categories/edit/' . $category->id, lang('cat_edit_label')) . ' | '; ?>
					<?= anchor('admin/news/categories/delete/' . $category->id, lang('cat_delete_label'), array('class'=>'confirm'));?>
				</td>
			</tr>
			<? } ?>
		<? } else { ?>
			<tr>
				<td colspan="3"><?= lang('cat_no_categories');?></td>
			</tr>
		<? } ?>
		</tbody>
	</table>
	<? $this->load->view('admin/partials/buttons', array('buttons' => array('delete') )); ?>
<?= form_close(); ?>
</section>