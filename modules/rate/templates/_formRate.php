<div class="form-rate">
  <form
    action="<?php echo url_for('add-rate', array('model' => $object->getModel(), 'id' => $object->getItemId())) ?>"
    method="post"
    name="<?php echo $form->getName() ?>"
    class="rating<?php if (!$sf_data->getRaw('sf_user')->canVote()): ?> disabled<?php endif; ?>"
    title="Note moyenne : <?php echo number_format($object->getAvgRating(), 1, '.', ''    ); ?>"
  >
  <fieldset>
    <legend>Rate it !</legend>
    <?php include_partial('rate/form', array('form' => $form)) ?>
  </fieldset>
  </form>
</div>
