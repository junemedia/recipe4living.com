
<div style="width: 100%; height: 30px; background: #FFFFFF; position: fixed; top: 0px;">
	<form method="post" enctype="multipart/form-data">
		
		<div>
			<input class="file text-content" type="file" name="fileupload" value="Upload" size="30" />
			<input type="submit" value="Upload" />
		</div>
		
	</form>
</div>

<div style="margin-top: 40px; width: 450px; height: 120px; overflow-x: scroll; overflow-y: hidden; white-space: nowrap">
<?php if(!empty($uploadedImages)) { ?>
	<?php foreach($uploadedImages as $queueId=>$upload) { ?>
		<?php foreach($upload as $uploadId=>$file) { ?>
		<?php
			if(!empty($file['image_info']['width']) && !empty($file['image_info']['height'])) {
				if($file['image_info']['height']<100) {
					$imageHeight = $file['image_info']['height'];
				}
				else {
					$imageHeight = 100;
				}
				$imageWidth = round($file['image_info']['width']/$file['image_info']['height']*$imageHeight);
			}
			else {
				$imageHeight = 100;
				$imageWidth = 100;
			}
		?>
			<!--a id="<?= 'u-'.$uploadId ?>" href="#" title="Drag"-->
				<img id="" src="<?= 'http://'.$_SERVER['SERVER_NAME'] . ASSETURL . '/tempimages/'.$imageWidth.'/'.$imageHeight.'/0/' . $file['file_name'] ?>" style="border: 0px;">
			<!--/a-->
		<?php } ?>
	<?php } ?>
<?php } ?>
</div>
