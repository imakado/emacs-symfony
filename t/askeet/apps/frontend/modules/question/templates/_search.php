<?php echo form_tag('@search_question') ?>

<fieldset>

<?php echo input_tag('search', htmlspecialchars($sf_params->get('search')), array('style' => 'width: 150px')) ?>&nbsp;
<?php echo submit_tag(__('search it'), 'class=small') ?>
<?php echo checkbox_tag('search_all', 1, $sf_params->get('search_all')) ?>&nbsp;<label for="search_all" class="small"><?php echo __('search with all words') ?></label>

</fieldset>

</form>
