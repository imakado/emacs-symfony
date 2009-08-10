<h1><?php echo __('Confirmation - login information sent') ?></h1>

<p><?php echo __('Your login information was sent to') ?></p>
<p><?php echo $sf_request->getParameter('email') ?></p>
<p><?php echo __('You should receive it shortly, so you can proceed to the %1%.', array('%1%' => link_to(__('login page'),'@login'))) ?></p>
