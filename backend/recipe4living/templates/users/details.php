	
	<div class="centered horizontal">&nbsp;</div>
	
	<table id="items_data" class="centered horizontal" style="width: 1200px;">
		
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%; vertical-align: top;">Image:</td>
			<td class="textfield" style="padding: 5px; width: 50%;"><img src="<?= ASSETURL . '/userimages/150/150/1/' . $user['image'] ?>" alt="avatar" /></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%;">Username:</td>
			<td class="textfield" style="padding: 5px; width: 50%;"><?= $user['username'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Email:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['email'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">First Name:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['firstname'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Last Name:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['lastname'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Location:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['location'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Display Name:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['displayname'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Rating:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['rating'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Ranking:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['ranking'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">User Type:</td>
			<td class="textfield" style="padding: 5px;">
				<form method="POST" name="user_type_form" action="<?= SITEURL.$baseUrl.'/updateUserType/'.$user['id'] ?>">
					<select name="type">
						<option value="member" <?=($user['type']=='member')?'selected="selected"':''?>>member</option>
						<option value="admin" <?=($user['type']=='admin')?'selected="selected"':''?>>admin</option>
					</select>
					<input type="submit" value="Update" />
				</form>
			</td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Registration Date:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['joined'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">About:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['about'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Favourite Foods:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['favouriteFoods'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Date of Birth:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['dob'] ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold;">Profile is Private:</td>
			<td class="textfield" style="padding: 5px;"><?= $user['private'] == 1 ? 'yes' : 'no' ?></td>
		</tr>
		
	</table>

	<div class="centered horizontal">&nbsp;</div>

	<form name="delete_form" action="<?= SITEURL.$baseUrl.'/setpassword/'.$user['id'] ?>" method="post">
	<table id="items_data" class="centered horizontal" style="width: 1200px;">
		
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%;">New Password:</td>
			<td class="textfield" style="padding: 5px; width: 50%;"><input type="text" name="newPassword" value="<?= Template::get('newPassword') ?>" /></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%;">&nbsp;</td>
			<td class="textfield" style="padding: 5px; width: 50%;">
				<input type="submit" name="update" value="Update Password" />
				<label><input type="checkbox" name="updateSendEmail" value="on"<?= (is_null(Template::get('updateSendEmail')) || Template::get('updateSendEmail') === true) ? ' checked="checked"' : '' ?> /> Send new password to the user</label>
			</td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%;">&nbsp;</td>
			<td class="textfield" style="padding: 5px; width: 50%;">
				<input type="submit" name="reset" value="Generate New Password" />
				<label><input type="checkbox" name="resetSendEmail" value="on"<?= (is_null(Template::get('resetSendEmail')) || Template::get('resetSendEmail') === true) ? ' checked="checked"' : '' ?> /> Send new password to the user</label>
			</td>
		</tr>
		
	</table>
	</form>

	<div class="centered horizontal">&nbsp;</div>
	
	<form name="delete_form" action="<?= SITEURL.$baseUrl.'/'.($user['deleted'] == 1 ? 'enableuser' : 'disableuser').'/'.$user['id'] ?>" method="post">
	<table id="items_data" class="centered horizontal" style="width: 1200px;">
		
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%;">Live:</td>
			<td class="textfield" style="padding: 5px; width: 50%;"><?= $user['deleted'] == 0 ? 'yes' : 'no' ?></td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; vertical-align: top;">Deletion Reason:</td>
			<td class="textfield" style="padding: 5px;">
			<?php if($user['deleted'] == 1) { ?>
				<?= $user['deleteReason'] ?>
			<?php } else { ?>
				<textarea name="deleteReason" style="height:70px;"></textarea>
			<?php } ?>
			</td>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 5px; text-align: right; font-weight: bold; width: 50%;">&nbsp;</td>
			<td class="textfield" style="padding: 5px; width: 50%;">
			<?php if($user['deleted'] == 1) { ?>
				<input type="submit" name="delete" value="Enable This User" />
			<?php } else { ?>
				<input type="submit" name="undelete" value="Disable This User" />
			<?php } ?>
			</td>
		</tr>
		
	</table>
	</form>

	<?php if (!empty($recipeboxItems)) { ?>
	
	<div class="centered horizontal">&nbsp;</div>
	
	<table id="items_data" class="centered horizontal" style="width: 1200px;">
		
		<tr class="metadata">
			<th class="textfield" style="padding: 5px;">Recipe Box</th>
		</tr>
		
		<?php 
			$alt = false; foreach ($recipeboxItems as $item) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
			
			<td class="textfield"><a href="<?= FRONTENDSITEURL.$item['link']; ?>" target="_blank" style="width: 1180px;"><?= Text::trim($item['title'], 100); ?></a></td>
		</tr>
		<?php
			}
		?>
		
		<tr class="metadata">
			<td colspan="7" style="border: 0px;">
				<div class="fr"><?= $recipeboxPagination->get('buttons'); ?></div>
			</td>
		</tr>
		
	</table>

	<?php } ?>

	<?php if (!empty($recipeNoteItems)) { ?>

	<div class="centered horizontal">&nbsp;</div>
	
	<table id="items_data" class="centered horizontal" style="width: 1200px;">
		
		<tr class="metadata">
			<th colspan="2" class="textfield" style="padding: 5px;">Recipe Notes</th>
		</tr>
		<tr class="metadata">
			<th class="textfield" style="padding: 5px; width: 50%;">Recipe</th>
			<th class="textfield" style="padding: 5px; width: 50%;">Note</th>
		</tr>
		
		<?php 
			$alt = false; foreach ($recipeNoteItems as $item) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
			<td class="textfield"><a href="<?= FRONTENDSITEURL.$item['link']; ?>" target="_blank" style="width: 585px;"><?= Text::trim($item['title'], 100); ?></a></td>
			<td class="textfield" style="padding: 5px;"><?= $item['comment'] ?></td>
		</tr>
		<?php
			}
		?>
		
		<tr class="metadata">
			<td colspan="7" style="border: 0px;">
				<div class="fr"><?= $recipeNotePagination->get('buttons'); ?></div>
			</td>
		</tr>
		
	</table>

	<?php } ?>

	<div class="centered horizontal">&nbsp;</div>

	<table id="items_data" class="centered horizontal" style="width: 1200px;">
		
		<tr class="metadata">
			<th colspan="4" class="textfield" style="padding: 5px;">Saved Cookbooks</th>
		</tr>
		<tr class="metadata">
			<th class="textfield" style="padding: 5px; width: 40%;">Title</th>
			<th class="textfield" style="padding: 5px; width: 40%;">Description</th>
			<th class="textfield" style="padding: 5px; width: 15%;">Date</th>
			<th class="textfield" style="padding: 5px; width: 5%;">Live</th>
		</tr>
		
		<?php if (empty($cookbookItems)) { ?>
		<tr>
			<td colspan="7" style="text-align: center;">N / A</td>
		</tr>
		<?php } else {
				$alt = false; foreach ($cookbookItems as $item) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>">
			
			<td class="textfield" style="padding: 5px;"><a href="<?= FRONTENDSITEURL.$item['link']; ?>" target="_blank" style="width: 180px;"><?= $item['title'] ?></a></td>
			<td class="textfield" style="padding: 5px;"><?= $item['description'] ?></td>
			<td class="textfield" style="padding: 5px;"><?= $item['date'] ?></td>
			<td class="textfield" style="padding: 5px;"><?= $item['live'] ? 'yes' : 'no' ?></td>
			
		</tr>
		<?php
				}
		?>
		
		<tr class="metadata">
			<td colspan="7" style="border: 0px;">
				<div class="fr"><?= $cookbookPagination->get('buttons'); ?></div>
			</td>
		</tr>
		<?php
			}
		?>
		
	</table>

	<div class="centered horizontal">&nbsp;</div>
	
	<table id="items_data" class="centered horizontal" style="width: 1200px;">
		
		<tr class="metadata">
			<th class="textfield" style="padding: 5px;">Reviews</th>
		</tr>
		<tr class="metadata">
			<td class="textfield" style="padding: 10px 0px;"><a href="<?= SITEURL. '/comments?user='.urlencode($user['username']) ?>" target="_blank" style="display: inline;">View Reviews by "<?= $user['username'] ?>"</a></td>
		</tr>
		
	</table>

	<div class="centered horizontal">&nbsp;</div>

	<div class="centered horizontal" style="text-align: center;">
		<a href="<?= $backButtonUrl ?>">Go Back</a>
	</div>

	<div class="centered horizontal">&nbsp;</div>
