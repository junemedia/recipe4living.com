<!--
	<div id="comment" class="screenonly">
		<h4>Comment on this article:</h4>
		<form id="form-review-recipe" method="post" action=""><div>
			<textarea name="comments" id="q" cols="10" rows="4" class="simpletext" title="Write your comment here"></textarea>

			<button type="submit" class="button-md fl"><span>Submit your comment</span></button>
			<input type="hidden" name="task" value="save_review" />
			<div class="clear"></div>
		</div></form>
	</div>
-->
<?php if ($this->_doc->getFormat() != 'print') { ?>
    <!--<div id="disqus_thread"></div>
    <script type="text/javascript">
        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        var disqus_shortname = 'recipe4living'; // required: replace example with your forum shortname
        var disqus_url = 'http://www.recipe4living.com<?php echo substr($_SERVER["REQUEST_URI"],0,strlen($_SERVER["REQUEST_URI"])-1); ?>';

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function() {
            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
        })();
    </script>-->
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

  <div class="fb-comments" style="margin-top:20px;" data-href="http://www.recipe4living.com<?php echo substr($_SERVER["REQUEST_URI"],0,strlen($_SERVER["REQUEST_URI"])-1); ?>" data-numposts="5" data-colorscheme="light"></div>	
<?php } ?>
