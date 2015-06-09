
	<form action="" method="post">
		
		<?= Messages::getMessages(); ?>		
		<div class="div_input">
			<label for="title">
				Title:			
			</label>
			<input id="title" type="text" name="title" value="<?= isset($giveaway['title']) ? $giveaway['title'] : ''; ?>" />
		</div>
		<div class="div_input">
			<label for="review_id" title="The ID of the article that used for review.">
				Review ID:				
			</label>
			<input id="review_id" type="text" name="review_id" value="<?= isset($giveaway['articleid']) ? $giveaway['articleid'] : ''; ?>" /> 
		</div>
		<div class="div_input">
			<label for="start_date" title="Format:YYYY-MM-DD">
				Start Date:				
			</label>
			<input id="start_date" type="text" name="start_date" value="<?= isset($giveaway['publishDate']) ? $giveaway['publishDate'] : ''; ?>" /> 
		</div>
		<div class="div_input">
			<label for="end_date" title="Format:YYYY-MM-DD">
				End Date:				
			</label>	
			<input id="end_date" type="text" name="end_date" value="<?= isset($giveaway['endDate']) ? $giveaway['endDate'] : ''; ?>" /> 
		</div>
		<div class="div_input">		
			<label for="product_image">
				Product Image:				
			</label>
			<input id="product_image" type="text" name="product_image" value="<?= isset($giveaway['image']) ? $giveaway['image'] : ''; ?>" />
		</div>
		<div class="div_input">
			<label for="description">
				Description:				
			</label>
			<textarea id="description" name="description"><?= isset($giveaway['description']) ? $giveaway['description'] : ''; ?></textarea>
		</div>
		<button type="submit" name="submit" value="submit">
			<span>Save</span>
		</button>
		
		<input type="hidden" name="task" value="save" />
		
	</form>
	
	<style type="text/css">
		
		form {
			position: relative;
			width: 500px;
		}
		
		input, textarea {
			background-color: #F8F8F8;
			margin: 1px;
			border: 1px #BBBBBB solid;
		}
		
		input {			
			font-size: 14pt;
			width:365px;
		}
		
		textarea {
			width: 460px;
			font-size: 11pt;
			padding: 10px;
			line-height: 25px;
			
			height: 360px;
		}
		
		input:hover, textarea:hover {
			border: 2px #999999 solid;
			margin: 0px;
		}
		
		label {
			display: inline-block;
			margin: 10px;
			text-align: right;
			width: 85px;
		}
		
		.div_input {
			width: 580px;
		}
		
		button {
			display: block;
			margin: 10px auto;
		}
		
	</style>