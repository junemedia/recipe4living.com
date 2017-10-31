	<?php Template::startScript(); ?>
		$('servingsize').addEvent('change', function(event) {
			event.stop();
			$('servingform').submit();	
		});
	<?php Template::endScript(); ?>
	<?php if ($inline) { ?>

	<div class="rounded nutrition">
		<div class="content">
			<h2>Nutrition Facts</h2>
			<?php if($recipeNutrition['servingMeasure'] == 'people'){?>
				<div>
					<div class="fl" style="padding-top:2px;"> <p class="text-content">Serving size for </p> </div>
					 <div class="fl" style=" padding: 0px 5px 0px 5px;">
						<form action="" method="get" id="servingform">
							<select id="servingsize" name="servingsize" style="width: 40px;">
								<?php for($i=1;$i<6;$i++){?>
									<option value="<?= $i;?>" <?= ($servingSize == $i)? 'selected = "selected"':''; ?>><?= $i;?></option>
								<?php }?>
							</select> 
						</form> 
					</div>
					<div class="fl" style="padding-top:2px;">
					<p class="text-content"> <?= ($servingSize == 1)?'person':'people';?> <?= round($recipeNutrition['weight'],2); ?>g</p>
					</div>
				</div>
				<div style="clear:both;"></div>
				<p class="text-content"> This recipe is made for <?= $recipeNutrition['servingQuantity']; ?> <?= ($recipeNutrition['servingQuantity'] == 1)?'person':'people' ?></p>			
			<?php }else{?>
				<div>
					<div class="fl" style="padding-top:2px;"> <p class="text-content">Serving size </p> </div>
					<div class="fl" style=" padding: 0px 5px 0px 5px;">
						<form action="" method="get" id="servingform">
							<select id="servingsize" name="servingsize" style="width: 40px">
								<?php for($i=1;$i<6;$i++){?>
									<option value="<?= $i;?>" <?= ($servingSize == $i)? 'selected = "selected"':''; ?>><?= $i;?></option>
								<?php }?>
							</select>
						</form>
					</div>  
					<div class="fl" style="padding-top:2px;"> 
						<p class="text-content"> <?= ($servingSize == 1)?$recipeNutrition['servingMeasure']:$recipeNutrition['servingMeasure'].'s';?> <?= round($recipeNutrition['weight'],2); ?>g</p>
					</div>
				</div>
				<div style="clear:both;"></div>				
				<p class="text-content">This recipe makes <?= $recipeNutrition['servingQuantity']; ?> <?= ($recipeNutrition['servingQuantity'] == 1)?$recipeNutrition['servingMeasure']:$recipeNutrition['servingMeasure'].'s';?></p>
			<?php }?>
			<table cellspacing="0" cellpadding="0" class="text-content">
				<tr class="major">
					<td>Calories <?= $calories; ?></td>
					<td></td>
				</tr>
				<tr class="major">
					<td>Amount Per Serving</td>
					<td class="value">%DV</td>					 
				</tr>				
				<tr class="minor">
					<td>Total Fat <?= $totalFat;?>g</td>
					<td class="value"><?= round($totalFat/65*100,1); //65grams DV value?>%</td>					 
				</tr>
				<tr class="minor">
					<td>Saturated Fat <?= round($saturatedFat, 1); ?><?= $allNutrients[606]['Units']; ?></td>
					<td class="value"><?= round($saturatedFat/$allNutrients[606]['DV']*100, 1); ?>%</td>					
				</tr>
				<tr class="minor">
					<td> Monounsaturated Fat <?= round($monounsaturatedFat, 1); ?><?= $allNutrients[645]['Units']; ?></td> 
					<td class="value"><?= '-'; ?></td>					
				</tr>
				<tr class="minor">
					<td>Polyunsaturated Fat <?= round($polyunsaturatedFat, 1); ?><?= $allNutrients[646]['Units']; ?></td>
					<td class="value"><?= '-'; ?></td>					
				</tr>
				<tr class="minor">
					<td>Trans Fat <?= round($transFat, 1); ?><?= $allNutrients[605]['Units']; ?></td>
					<td class="value"><?= '-'; ?></td>					
				</tr>
				<tr class="major">
					<td>Cholesterol <?= isset($recipeNutrition['nutrition'][601]) ? round($recipeNutrition['nutrition'][601], $allNutrients[601]['Decimal']).$allNutrients[601]['Units'] : '-'; ?></td>
					<td class="value"><?= isset($allNutrients[601]['DV']) ? (round($recipeNutrition['nutrition'][601]/$allNutrients[601]['DV']*100, $allNutrients[601]['Decimal'])).'%' : '-'; ?></td>
				</tr>
				<tr class="major">
					<td>Sodium <?= isset($recipeNutrition['nutrition'][307]) ? round($recipeNutrition['nutrition'][307], $allNutrients[307]['Decimal']).$allNutrients[307]['Units'] : '-'; ?></td>
					<td class="value"><?= isset($allNutrients[307]['DV']) ? (round($recipeNutrition['nutrition'][307]/$allNutrients[307]['DV']*100, $allNutrients[307]['Decimal'])).'%' : '-'; ?></td>

				</tr>
				<tr class="major">
					<td>Potassium <?= isset($recipeNutrition['nutrition'][306]) ? round($recipeNutrition['nutrition'][306], $allNutrients[306]['Decimal']).$allNutrients[306]['Units'] : '-'; ?></td>
					<td class="value"><?= isset($allNutrients[306]['DV']) ? (round($recipeNutrition['nutrition'][306]/$allNutrients[306]['DV']*100, $allNutrients[306]['Decimal'])).'%' : '-'; ?></td>
				</tr>
				<tr class="major">
					<td>Carbohydrate <?= isset($recipeNutrition['nutrition'][205]) ? round($recipeNutrition['nutrition'][205], 1).$allNutrients[205]['Units'] : '-'; ?></td>	
					<td class="value"><?= isset($allNutrients[205]['DV']) ? (round($recipeNutrition['nutrition'][205]/$allNutrients[205]['DV']*100, $allNutrients[205]['Decimal'])).'%' : '-'; ?></td>
				</tr>
				<tr class="minor">
					<td>Dietary Fiber <?= isset($recipeNutrition['nutrition'][291]) ? round($recipeNutrition['nutrition'][291], $allNutrients[291]['Decimal']).$allNutrients[291]['Units'] : '-'; ?></td>
					<td class="value"><?= isset($allNutrients[291]['DV']) ? (round($recipeNutrition['nutrition'][291]/$allNutrients[291]['DV']*100, $allNutrients[291]['Decimal'])).'%' : '-'; ?></td>
				</tr>
				<tr class=" major">
					<td>Protein <?= isset($recipeNutrition['nutrition'][203]) ? round($recipeNutrition['nutrition'][203], 1).$allNutrients[203]['Units'] : '-'; ?></td>
					<td class="value"><?= isset($allNutrients[203]['DV']) ? (round($recipeNutrition['nutrition'][203]/$allNutrients[203]['DV']*100, $allNutrients[203]['Decimal'])).'%' : '-'; ?></td>
				</tr>
			</table>

			<a class="info-popup fr text-content" href="<?= SITEURL.$detailedLink; ?>?servingsize=<?= $servingSize;?>">See extended details</a>
			<div class="clear"></div>
		</div>
	</div>

	<?php } else { ?>

	<?php if (!Template::get('isPopup')) { ?>
	<div id="main-content" class="recipe">
		<div id="column-container">
			<div id="panel-center" class="column">

				<div id="recipe-nutrition" class="rounded"><div class="content">

					<h1>Nutrition Information for <a href="<?= SITEURL.$item['link']; ?>"><?= $item['title']; ?></a></h1>
	<?php } ?>

	<p class="text-content">
		<?= Template::text('nutrition_disclaimer'); ?>
	</p>
	<div class="full nutrition">
		<table class="text-content">
			<?php foreach ($allNutrients as $nutrientId => $nutrient) { ?>
			<tr>
				<td><?= $nutrient['NutrDesc']; ?></td>
				<td><?= isset($recipeNutrition['nutrition'][$nutrientId]) ? round($recipeNutrition['nutrition'][$nutrientId], $nutrient['Decimal']).$nutrient['Units'] : '-'; ?></td>
				<td><?= (isset($recipeNutrition['nutrition'][$nutrientId])  && isset($nutrient['DV'])) ? round($recipeNutrition['nutrition'][$nutrientId]/$nutrient['DV']*100, $nutrient['Decimal']).'%': '-'; ?></td>				
			</tr>
			<?php } ?>
		</table>
	</div>

	<?php if (!Template::get('isPopup')) { ?>
						<a class="fr text-content" href="<?= SITEURL.$item['link']; ?>">Back to recipe</a>
						<div class="clear"></div>
					</div>
				</div>
			</div>

			<div id="panel-left" class="column screenonly">
				<?php $this->leftnav(); ?>
			</div>

			<div id="panel-right" class="column screenonly">
				<div class="ad"><?php $this->_advert('openx_300x250atf'); ?></div>

				<?php $this->_box('reference_guides', array('limit' => 10)); ?>
			</div>

			<div class="clear"></div>
		</div>
	</div>
	<?php } ?>

	<?php } ?>

