<section class="title">
    <? if ($this->controller == 'admin_categories' && $this->method === 'edit') { ?>
        <h4><?= sprintf(lang('cat_edit_title'), $category->title); ?></h4>

    <? } else { ?>
        <h4><?= lang('cat_create_title'); ?></h4>

    <? } ?>
</section>

<section class="item">

    <?= form_open($this->uri->uri_string(), 'class="crud" id="categories"'); ?>

    <fieldset>
        <ol>
            <li class="even">
                <label for="title"><?= lang('cat_title_label'); ?></label>
                <?= form_input('title', $category->title); ?>
                <span class="required-icon tooltip"><?= lang('required_label'); ?></span>
            </li>
        </ol>
        <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))); ?>
    </fieldset>

    <?= form_close(); ?>
</section>