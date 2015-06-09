
	<div class="standardform">
		<div class="formholder">
			<form action="<?= SITEURL ?>/maillist_unsubscribe_save" method="post"><div>
				<?= Messages::getMessages() ?>

				<label>Enter your e-mail address here: </label>
				<input type="text" name="email" class="textinput">
				<input type="submit" name="submit "value="Unsubscribe me" class="submit">
				<input type="hidden" name="task" value="maillist_unsubscribe_save" />
			</div></form>
		</div>
	</div>