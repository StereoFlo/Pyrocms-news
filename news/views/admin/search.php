<?= form_open('admin/news/action'); ?>

    <h3><?= lang('news_list_title'); ?></h3>

<? if (!empty($news)) { ?>

    <table border="0" class="table-list">
        <thead>
        <tr>
            <th><?= form_checkbox(array('name' => 'action_to_all', 'class' => 'check-all')); ?></th>
            <th><?= lang('news_post_label'); ?></th>
            <th class="width-10"><?= lang('news_category_label'); ?></th>
            <th class="width-10"><?= lang('news_date_label'); ?></th>
            <th class="width-5"><?= lang('news_status_label'); ?></th>
            <th class="width-10"><span><?= lang('news_actions_label'); ?></span></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td colspan="6">
                <div class="inner filtered"><? $this->load->view('admin/partials/pagination'); ?></div>
            </td>
        </tr>
        </tfoot>
        <tbody>
        <? foreach ($news as $article) { ?>
            <tr>
                <td><?= form_checkbox('action_to[]', $article->id); ?></td>
                <td><?= $article->title; ?></td>
                <td><?= $article->category_title; ?></td>
                <td><?= format_date($article->created_on); ?></td>
                <td><?= lang('news_' . $article->status . '_label'); ?></td>
                <td>
                    <?= anchor('admin/news/preview/' . $article->id, lang($article->status == 'live' ? 'news_view_label' : 'news_preview_label'), 'rel="modal-large" class="iframe" target="_blank"') . ' | '; ?>
                    <?= anchor('admin/news/edit/' . $article->id, lang('news_edit_label')); ?> |
                    <?= anchor('admin/news/delete/' . $article->id, lang('news_delete_label'), array('class' => 'confirm')); ?>
                </td>
            </tr>
        <? } ?>
        </tbody>
    </table>

    <? $this->load->view('admin/partials/buttons', array('buttons' => array('delete', 'publish'))); ?>

<? } else { ?>
    <p><?= lang('news_no_articles'); ?></p>
<? } ?>

<?= form_close(); ?>