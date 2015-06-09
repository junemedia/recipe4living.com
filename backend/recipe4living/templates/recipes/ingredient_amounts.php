
	<h1>Ingredient amounts: <?= $item['title']; ?></h1>
	<?php if (empty($ingredients)) { ?>
	
	<p>There are no ingredients assigned to this recipe, please <a href="<?= SITEURL.$addIngredientsUrl; ?>">add some ingredients</a> first.</p>
	
	<?php } else { ?>
	
	<form action="<?= SITEURL.$taskUrl; ?>" method="post">
		
		<?php foreach ($ingredients as $ingredient) { extract($ingredient); ?>
		<div class="ingredient">
			<span class="name"><?= $Long_Desc; ?></span>
			<input class="amount" type="text" name="amounts[<?= $NDB_No; ?>]" value="<?= $amount; ?>" />
			<select class="measure" name="weightIds[<?= $NDB_No; ?>]"<?= empty($weights) ? ' style="display: none;"' : ''; ?>>
				<?php foreach ($weights as $availableWeight) { ?>
				<option value="<?= $availableWeight['id']; ?>"<?= $weightId == $availableWeight['id'] ? ' selected="selected"' : ''; ?>><?= $availableWeight['Msre_Desc']; ?> [<?= $availableWeight['Gm_Wgt']; ?>g]</option>
				<?php } ?>
			</select>
		</div>
		<?php } ?>
		
		<button type="submit" name="submit" value="submit"><span>Save</span></button>
		
	</form>
	
	<div style="margin-top: 20px;">
		<a href="<?= SITEURL.$addIngredientsUrl; ?>">Back to ingredients list</a>
	</div>
	
	<div class="recipe-ingredients-panel">
		<div class="panel proposed">
			<h2>Displayed ingredients</h2>
			<?php if ($proposedIngredients) { ?>
			<ul>
				<?php foreach ($proposedIngredients as $ingredient) { ?>
				<li title="<?= $ingredient; ?>">
					<?= Text::trim($ingredient, 30); ?>
				</li>
				<?php } ?>
			</ul>
			<?php } else { ?>
			None
			<?php } ?>
		</div>
	</div>
	
	<style type="text/css">
		
		.ingredient {
			display: block;
		}
		
		.ingredient .name {
			display: inline-block;
			width: 400px;
		}
		
		.ingredient .amount {
			width: 50px;
		}
		
	</style>
	
	<?php } ?>
