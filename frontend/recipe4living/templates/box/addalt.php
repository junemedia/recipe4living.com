<script  type="text/javascript" >
jQuery(document).ready(function(){
var alttitle = "<?= (!empty($item['default_alt']))?$item['default_alt']:$item['title']; ?>" ;
jQuery('#contentbody img').each(function(){
jQuery(this).attr({alt:alttitle});
});
});
 
 
</script>