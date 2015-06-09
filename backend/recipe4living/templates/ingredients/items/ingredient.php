<style type="text/css"> 

.plain {
    height:12px;
    padding: 5px;
}

.focus{
border-width: 0px;
width:auto;
height:auto;
background-color: white;
clear:both;
}

.opt0{
background-color:red; color:white;    
}

.opt1{
background-color:green; color:white;    
}

</style>

<script language="javascript">
function go_page()
{
    page = document.getElementById('page_num').value;
    window.location = '<?php echo $paginationBaseUrl;?>' + page;
}

function set_opt_color(obj)
{
    color = obj.value;
    if(color == 0) obj.style.backgroundColor = 'red';
    if(color == 1) obj.style.backgroundColor = 'green';
}

</script>
<form name="ingredientsForm" id="ingredientsForm" method="post" action="ingredients/save?page=<?php echo Request::getInt('page', 1);?>">
<table>
<?php if(isset($pagination)){?>
    <tr class="metadata">
        <td colspan="16" style="border: 0px;">
            <div class="fr">
                Page:<input type="input" id='page_num'>
                <input type="button" value="G0!" onclick="go_page();">
                &nbsp;<?php echo $pagination->get('buttons'); ?>
            </div>
            <div style="height: 10px; margin: 14px 0px;">
                Listing <?= $pagination->get('start'); ?> - <?= $pagination->get('end'); ?> of <?= $pagination->get('total'); ?>
            </div>
        </td>
    </tr>
<?php }?>
    <tr class="metadata">
        <td colspan="16">
            <?php
                //<a href='ingredients/filter_status?status=1'>Checked</a>&nbsp;
                //<a href='ingredients/filter_status?status=0'>Unchecked</a>&nbsp;
                //<a href='ingredients/filter_status?status=all'>All</a>&nbsp;
             ?>
			<input type='reset' value='Reset'>&nbsp;&nbsp;
			<input type='Submit' value='Commit Change'>
            Search: <input type='text' name="searchTerm" value=''>
            in  <select name="field">
                    <option value="recipeId">recipeId</option>
                    <option value="ingredientRawRow">What user typed in</option>
                    <option value="measurement">Measurement</option>
                    <option value="measurementDescription">Measurement Size</option>
                    <option value="IngredientRaw">Ingredient Search Term</option>
                    <option value="notes">Preparation Notes</option>
                    <option value="IngredientFeedBack">Ingredient Display</option>
                    <option value="IngredientLongDesc">Ingredient Match in Database</option>
                </select>
            <input type='submit' name='search' value='Go!'>
        </td>
    </tr>
    <tr>
        <th>id</th>
        <th>recipeId</th>
        <th style="width:500px;">What user typed in</th>
        <th>Quantity<br />Display</th>
        <th>Quantity<br />Decimal</th>
        <th>Measurement</th>
        <th>Measurement<br />Size</th>
        <th>Ingredient Search Term</th>
        <th>Preparation Notes</th>
        <th>Ingredient Display</th>
        <th>Ingredient Match in Database</th>
        <th>status</th>
        <th>NDB_No</th>
		<th>views</th>
		<th>User</th>
		<th>Time</th>
        
    </tr>
    <?php foreach($ingredients as $row){?>
    <tr>
        <td><input name="id[]" type='hidden' value="<?php echo $row["irid"];?>" /><?php echo $row["irid"];?></td>
        <td>
			<input name="recipeId[]" type='hidden' value="<?php echo $row["recipeId"];?>"/>
			<?php if (isset($mappings[$row["recipeId"]])){ ?>
				<a href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/recipes/edit/' . $mappings[$row["recipeId"]] . '.htm';?>" target="_blank">
					<?php echo $row["recipeId"];?>
				</a>
			<?php }else{
				echo $row["recipeId"];
			}?>
		</td>
        <td><input name="ingredientRawRow[]" type='hidden' value="<?php echo $row["ingredientRawRow"];?>" /><?php echo $row["ingredientRawRow"];?></th>
        <td><input name="quantity[]" style='width:40px;' value="<?php echo $row["quantity"];?>" /></td>
        <td><input name="quantity_true[]" style='width:40px;' value="<?php echo $row["quantity_true"];?>" /></td>
        <td><input name="measurement[]" style='width:40px;' value="<?php echo $row["measurement"];?>" /></td>
        <td><input name="measurementDescription[]" style='width:60px;' value="<?php echo $row["measurementDescription"];?>" /></td>
        <td><input name="IngredientRaw[]" value="<?php echo $row["IngredientRaw"];?>" onfocus='focus_class(this);' onblur='blur_class(this);' /></td>
        <td><input name="notes[]" value="<?php echo $row["notes"];?>" /></td>
        <td><input name="IngredientFeedBack[]" value="<?php echo $row["IngredientFeedBack"];?>" /></td>
        <td><input name="IngredientLongDesc[]" value="<?php echo $row["IngredientLongDesc"];?>" /></td>
        <td>
            <select name="status[]" id="status" class="opt<?php echo $row["status"];?>" onchange="set_opt_color(this)">
            <option value='0' class="opt0" <?php if($row["status"] == 0) echo 'selected';?> >Unchecked</option>
            <option value='1' class="opt1" <?php if($row["status"] == 1) echo 'selected';?> >Checked</option>
            </select>
        </td>
        <td><input name="NDB_No[]" type='hidden' value="<?php echo $row["NDB_No"];?>" /><?php echo $row["NDB_No"];?></td>
		<td><?php echo $row["views"];?></td>
		<td><?php echo $row["username"];?></td>
		<td><?php echo $row["time"];?></td>
    </tr>
    <?php }?>
    <tr><td colspan="16"><input type='reset' value='Reset'>&nbsp;&nbsp;<input type='Submit' value='Commit Change'></td></tr>
</table>
</form>