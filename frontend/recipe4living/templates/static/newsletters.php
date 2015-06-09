	<div id="main-content" class="static">
		<div id="column-container">
			<div id="panel-center" class="column" >
				<div id="contact" class="rounded" style="width:720px;" align="center">
					<iframe align="left" onload="NLArchiveOnloadCall();" id="frmid" src="/newsletters/index.php" frameborder="0" width="720px" style="overflow-y:hidden;overflow-x:hidden;"></iframe>
					<script type="text/javascript">
						function NLArchiveOnloadCall() {
							obj = document.getElementById('frmid');
							obj.style.height = 0;
						    obj.style.height = obj.contentWindow.document.body.scrollHeight + 50 + 'px';
						    self.scroll(0,0);
						}
					</script>
				</div>
			</div>
			<div id="panel-left" class="column">
				<?php $this->leftnav(); ?>
			</div>
			<div class="clear"></div>
		</div>
	</div>
