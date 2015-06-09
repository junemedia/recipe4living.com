
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		
		<?php if($pagination->get('total') > 0) { ?>
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
				<div style="height: 10px; margin: 14px 0px;">
					Listing <?= $pagination->get('start'); ?> - <?= $pagination->get('end'); ?> of <?= $pagination->get('total'); ?>
				</div>
			</td>
		</tr>
		<?php } ?>
		
		<?php if(!empty($filterArray)) { ?>
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				<strong>Search results for:</strong>
				<?php
					if(isset($filterArray['username'])) {
						echo ' <strong style="color: blue;">Username:</strong> '.$filterArray['username'];
					}
					if(isset($filterArray['email'])) {
						echo ' <strong style="color: blue;">Email:</strong> '.$filterArray['email'];
					}
					if(isset($filterArray['fullName'])) {
						echo ' <strong style="color: blue;">Name:</strong> '.$filterArray['fullName'];
					}
					if(isset($filterArray['live'])) {
						echo ' <strong style="color: blue;">Live:</strong> '.($live == 1 ? 'No' : 'Yes' );
					}
				?>
			</td>
		</tr>
		<?php } ?>

		<tr class="metadata">
			<th class="textfield" style="padding: 5px; width: 5%; text-align: center;">ID</th>
			<th class="textfield" style="padding: 5px; width: 20%;">Username</th>
			<th class="textfield" style="padding: 5px; width: 20%;">Last Logged In</th>
			<th class="textfield" style="padding: 5px; width: 20%;">Email</th>
			<th class="textfield" style="padding: 5px; width: 20%;">First Name</th>
			<th class="textfield" style="padding: 5px; width: 20%;">Last Name</th>
			<th class="textfield" style="padding: 5px; width: 10%;">IP</th>
			<th class="textfield" style="text-align: center; padding: 5px; width: 5%;">User Type</th>
			<th class="textfield" style="text-align: center; padding: 5px; width: 5%;">Live</th>
			<th class="textfield" style="text-align: center; padding: 5px; width: 5%;">&nbsp;</th>
			<th class="textfield" style="text-align: center; padding: 5px; width: 5%;">&nbsp;</th>
		</tr>
		
		<?php 
			if (!empty($users)) {
				$alt = false; foreach ($users as $user) { $alt = !$alt;
		?>		
		<tr class="<?= $alt ? 'odd' : ''; ?>"<?= ($user['type'] == 'admin' ? ' style="background: #EEEEEE;"' : '') ?>>
		
			<td style="text-align: center;"><?= $user['id'] ?></td>
			<td><?= $user['username']; ?></td>
			<td><?= $user['lastLoggedin']; ?></td>
			<td><?= $user['email'] ?></td>
			<td class="textfield" style="padding: 5px;"><?= $user['firstname'] ?></td>
			<td class="textfield" style="padding: 5px;"><?= $user['lastname'] ?></td>
			<td class="textfield" style="padding: 5px;"><?= $user['ipaddr'] ?></td>
			<td class="textfield" style="padding: 5px; text-align: center;"><?= $user['type'] ?></td>
			<td style="text-align: center;"><!--<a href="<?= SITEURL.$baseUrl.'/setstatus?userId='.$user['id'].'&amp;action='.($user['deleted'] == 0 ? 'disable' : 'enable') ?>" title="Click to <?= $user['deleted'] == 0 ? 'disable' : 'enable'; ?> this user">--><img src="<?= COREASSETURL; ?>/images/famfamfam/<?= $user['deleted'] == 0 ? 'tick.png' : 'cross.png'; ?>" /><!--</a>--></td>
			<td style="text-align: center;"><a href="<?= SITEURL.'/users/userDetails/'.$user['id'] ?>">Edit</a></td>
			<td style="text-align: center;"><a href="<?= SITEURL.$detailsPageBaseUrl.'/'.$user['id'] ?>">View</a></td>
		</tr>
		<?php
				}
		?>
		
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				<div class="fr"><?= $pagination->get('buttons'); ?></div>
			</td>
		</tr>
		
		<?php
			}
			else {
		?>
		
		<tr class="metadata">
			<td colspan="8" style="border: 0px;">
				No matches found.
			</td>
		</tr>
		
		<?php
			}
		?>
		
	</table>

	<form name="search_form" method="get">
	<table id="items_data" class="centered horizontal" style="width: 1200px">
		<tr>
			<td colspan="2">Search</td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">Username:</td>
			<td><input type="text" name="username" value="<?= $username ?>" style="width: 300px;" /></td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">Email:</td>
			<td><input type="text" name="email" value="<?= $email ?>" style="width: 300px;" /></td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right; width: 30%;">Name:</td>
			<td style="width: 70%;"><input type="text" name="full_name" value="<?= $fullName ?>" style="width: 300px;" /></td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">Live:</td>
			<td>
				<label><input type="radio" name="live" value="1"<?= $live == 1 ? 'checked="checked"' : '' ?> /> No</label>
				<label><input type="radio" name="live" value="2"<?= $live == 2 ? 'checked="checked"' : '' ?> /> Yes</label>
				<label><input type="radio" name="live" value="3"<?= ($live == 3|| empty($live)) ? 'checked="checked"' : '' ?> /> No Preference</label>
			</td>
		</tr>
		<tr>
			<td class="textfield" style="padding: 5px; text-align: right;">&nbsp;</td>
			<td>
				<input type="submit" value="Search" />
				<input type="submit" name="clear" value="Clear Search Criteria" />
			</td>
		</tr>
	</table>
	</form>
	
	
	
