
		<form id="listing-recipe-search" action="/recipes/shopping_list" method="get" class="reloads"><div> 
			<input type="hidden" name="reloadtask" value="shopping_list_items" id="reloadtask" />
			<div id="list-sort" class="fl"> 
				<label for="group" class="fl">Group by:</label> 
				<select id="sort-by" name="group" class="reloads"> 
					<option <?=($groupBy=='foodGroup'?'selected="selected"':'')?> value="foodGroup">Food Type</option> 
					<option <?=($groupBy=='recipe'?'selected="selected"':'')?> value="recipe">Recipe</option> 
				</select> 
				<noscript> 
					<input type="submit" value="Sort" /> 
				</noscript> 
			</div> 
			<div class="fr shopping-list-print">
				<a href="?format=print" class="print-popup">Print</a>
			</div>
		</div></form>
		
		<div class="clear"></div> 
		
		<div class="shopping-list rounded"> 

<div class="included-container">
<h2>Recipes included:</h2>
<?php foreach ($recipes as $recipe) { ?>
	<div class="included">
		<form method="get" action="<?=SITEURL?>/recipes/shopping_list_edit/<?=$recipe['slug']?>.htm">
			<a href="<?=SITEURL?>/recipes/<?=$recipe['slug']?>.htm"><?=$recipe['title']?></a> 
			<a class ="removerecipe" href="<?=SITEURL?>/recipes/shopping_list_remove/<?=$recipe['slug']?>.htm">(Remove)</a>
			<div class="portions">
				<div class="portion"><input type="text" name="portions" value="<?=$recipe['portions']?>" /> portion<?=($recipe['portions']==1?'':'s')?></div>
				<input type="submit" value="Update" class="update" />
			</div>
			<div class="clear"></div>
		</form>
	</div>
<?php } ?>
</div>
<?php
foreach ($ingredients as &$groupItems) {
	$groupValue = reset($groupItems);
	$groupValue = $groupValue[$groupBy];
	if ($groupBy == 'foodGroup') $groupValue = $foodGroups[$groupValue];
	else $groupValue = $recipes[$groupValue]['title'];
	?>
	<h3><?=$groupValue?></h3>
	<table style="width:100%" >
	<?php
	
	foreach ($groupItems as $ingId => &$ing) {
		// Fix units
		$units = Array();
		foreach ($ing['units'] as $unit) {
			$units[] = Array(
					'gm' => $unit['Gm_Wgt']/$unit['Amount'],
					'text' => $unit['Msre_Desc'],
					'min' => $unit['Amount'],
				);
		}
		
		$unit = $this->_calculateUnit($ing['amount'],$units);
		/*$gm = $this->_calculateUnit($ing['amount'],Array(
			Array('gm' => 1,'text' => 'g'),
			Array('gm' => 1000,'text' => 'kg'),
			)); */
		$oz = $this->_calculateUnit($ing['amount'],Array(
			Array('gm' => 1/0.0352739619, 'text' => 'oz')
			));
		?>
		<tr>
<?php /*			<td style="width: 50%"><?=$ing['text']?></td>
			<td style="width: 30%"><?=ceil($unit['quantity']-0.4).'x '.$unit['text']?></td> */ ?>
			<td style="width: 80%"><?=ceil($unit['quantity']-0.4).' '.$unit['text'].' '.$ing['text']?></td>
			<td style="width: 10%"><?=(ceil($oz['quantity']-0.4)+0).' '.$oz['text']?></td>
			<td style="width: 10%"><a class ="remove" href="<?=SITEURL?>/recipes/shopping_list_hideingredient/<?=$ingId?>">Delete</a></td>
		</tr>
		<?php
	}
	?>
	</table>
	<?php
}
?>
<?php Template::startScript(); ?>
	$$('.remove').each(function(i){
		i.addEvent('click', function(el){
           return confirm('Are you sure you want to remove this ingredient from your shopping list?');
        });
	});
	$$('.removerecipe').each(function(i){
		i.addEvent('click', function(el){
           return confirm('Are you sure you want to remove this recipe from your shopping list?');
        });
	});
<?php Template::endScript(); ?>
</div>
