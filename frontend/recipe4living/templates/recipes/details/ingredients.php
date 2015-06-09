	
<div id="recipe-ingredients" class="block">
	<h2>Your Ingredients</h2>
	<h3>We've tried to figure out what ingredients you meant - if what you see here is slightly different to what you entered, don't worry! If it's totally different, please try amending your ingredients below, until the list closely resembles the intended ingredients</h3>

	<div class="content" style="padding-top:10px;">
		<ul>
			<? foreach ($item['tidyIngredients'] as $ingredient) { if (!is_array($ingredient['details'])) { /* malformed */ continue; }  ?>
				<li><?=$ingredient['amount'];?> <?=$ingredient['details']['weights'][$ingredient['weightId']]['Msre_Desc'];?> <?=$ingredient['details']['Long_Desc'];?></li>
			<? } ?>
		</ul>
	</div>
</div>

<?php if (!empty($item['ingredients'])) { ?>
<div id="form-share-recipe" style="padding-bottom:50px;font-size:16px">
	<form id="form_ingredients_submit" name="form_ingredients_submit" action="#" method="post" enctype="multipart/form-data">
		<div class="fieldwrap ingredients">
			<label for="ingredients">Ingredients <span class="red-ast">*</span></label>
			<p class="text-content">Place each ingredient on a new line.</p>
			<textarea id="form_ingredients" class="textinput required" name="change_ingredients" cols="10" rows="10"><?= implode('',$item['ingredients']); ?></textarea>
			<small>Please use the following abbreviations: tsp., Tbs., lb., oz., pkg., C., qt.</small>
		</div>
		<div class="fieldwrap">
			<button name="update" class="button-md fr" type="submit" value="Update"><span>Update Ingredients</span></button>
		</div>
	</form>
</div>
<?php } ?>
