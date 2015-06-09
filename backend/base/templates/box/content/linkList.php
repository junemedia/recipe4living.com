	<form action="/oversight/box/content/" method="post" enctype="multipart/form-data"><div>
	
		<? // Language code dropdown ?>
		<input type="hidden" name="langCode" value="<?= $langCode; ?>" />
		
		<p>
			<label for="title">
				Title
				<br />
				<input type="text" name="title" value="<?= isset($title) ? $title : '' ?>" class="flat" style="width: 200px;" />
			</label>
		</p>
		<p>
			<label for="link">
				Title Link
				<br />
				<input type="text" name="link" value="<?= isset($link) ? $link : '' ?>" class="flat" style="width: 200px;">
			</label>
		</p>
		<p>
			<label for="title">
				Subtitle
				<br />
				<input type="text" name="subtitle" value="<?= isset($subtitle) ? $subtitle : '' ?>" class="flat" style="width: 200px;" />
			</label>
		</p>
		<p>
			<label for="link">
				Subtitle Link
				<br />
				<input type="text" name="subtitleLink" value="<?= isset($subtitleLink) ? $subtitleLink : '' ?>" class="flat" style="width: 200px;">
			</label>
		</p>
		<p>
			<label for="link">
				Date
				<br />
				<input type="text" name="date" value="<?= isset($date) ? $date : '' ?>" class="flat" style="width: 200px;">
			</label>
		</p>
		<p>
			<label for="text">
				Link Category<br />
				<select name="linkCategory">
					<option value=""> - select - </option>
				<?php
					if($linkCategories = Template::get('linkCategories')) {
						foreach($linkCategories as $id=>$category) {
							$selected = '';
							if(isset($linkCategory) && $linkCategory==$category) {
								$selected = ' selected="selected"';
							}
							echo '<option value="'.$id.'"'.$selected.'>'.$category.'</option>';
						}
					}
				?>
				</select>
			</label>
		</p>
		<p>
			<label for="link">
				Sequence
				<br />
				<input type="text" name="order" value="<?= isset($sequence) ? $sequence : '' ?>" class="flat" style="width: 30px;">
			</label>
		</p>
		
		<div style="clear:both;"></div>

		<? // Finish off ?>
		<span>
			<input type="hidden" name="canAdd" value="<?= (int) $canAdd; ?>" />
			<input type="hidden" name="canDelete" value="<?= (int) $canDelete; ?>" />
			<?php 
				// Edit existing
				if (!empty($contentId)) {
			?>
			<input type="hidden" name="contentId" value="<?= $contentId; ?>" />
			<input type="submit" name="save" value="Save" class="button" />
			<?php if ($canDelete) { ?>
			<input type="submit" name="delete" value="Delete" class="button" />
			<?php } ?>
			<?php 
				// Add new
				} else {
			?>
			<input type="hidden" name="boxId" value="<?= $boxId; ?>" />
			<input type="submit" name="save" value="Add new" class="button" />
			<?php
				}
			?>
		</span>
	</div></form>