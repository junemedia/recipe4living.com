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

.STYLE1 {
	font-size: 10px;
	font-style: italic;
}
</style>

<script language="javascript">

var net = new Object();
net.READY_STATE_UNINTIALIZED = 0;
net.READY_STATE_LOADING = 1;
net.READY_STATE_LOADED = 2;
net.READY_STATE_INTERACTIVE = 3;
net.READY_STATE_COMPLETE = 4;

net.ContentLoader = function( url, onload, onerror, method, params, contentType ){
   this.url = url;
   this.req = null;
   this.onload = onload;
   this.onerror = ( onerror ) ? onerror : this.defaultError;
   this.loadXMLDoc( url, method, params, contentType );
}

net.ContentLoader.prototype = {
   loadXMLDoc : function( url, method, params, contentType ){
     if( !method ){
         method = "GET";
     }
     if ( !contentType && method == "POST" ){
         contentType = "application/x-www-form-urlencoded";
     }
   
      if ( window.XMLHttpRequest ){
          this.req = new XMLHttpRequest();
      } else if ( window.ActiveXObject ){
          this.req = new ActiveXObject( "Microsoft.XMLHTTP" );
      }
      if ( this.req ){
          try{
              var loader = this;
              this.req.onreadystatechange = function(){
                  loader.onReadyState.call( loader );
              }
              this.req.open( method, url, true );
              if ( contentType ){
                  this.req.setRequestHeader( "Content-Type", contentType );
              }
              this.req.send( params );          
          }catch( err ){
              this.onerror.call( this );
          }
      }
   },
   onReadyState : function(){
       var req = this.req;
       var ready = req.readyState;
       if ( ready == net.READY_STATE_COMPLETE ){
           var httpStatus = req.status;
           if ( httpStatus = 200 || httpStatus == 0 ){
               this.onload.call( this );
           }else{
               this.onerror.call( this );
           }
       }
   },
   defaultError : function(){
       alert( "error fetching data!"
           + "\n\nreadyState: " + this.req.readyState 
           + "\nstatus: " + this.req.status 
           + "\nheaders: " + this.req.getAllResponseHeaders() );
   }
   
}

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

function seach_go()
{
    q = document.getElementById('search').value;
    q = q.replace(' ', '%20')
    window.location = '<?php echo SITEURL . $this->_baseUrl . '/default_value_list'; ?>' + '?q=' + q;
}

function add_new()
{
	if(document.getElementById('new_ingredient').style.display == 'none'){
		document.ingredientsForm.show_add_new.value = 'Cancel';
		document.getElementById('new_ingredient').style.display = 'block';
	}else{
		document.ingredientsForm.show_add_new.value = 'Add New Ingredient';
		document.getElementById('new_ingredient').style.display = 'none';
	}
}

function getSearch()
{
    var value;
    value = document.getElementById('testSearch').value;
    var argvs = 'searchTerm=' + value;
    new net.ContentLoader( '/oversight/ingredients/getSearch?format=raw', getSearchResult, null, 'POST',  argvs); 
    
}

function getSearchResult()
{
     document.getElementById("testSearchDisplay").innerHTML = this.req.responseText;
     document.getElementById("testSearchDisplay").style.display = "block";
}

function save_notes()
{

    var argvs = 'notes=' + document.getElementById('notes_words').value;
    new net.ContentLoader( '/oversight/ingredients/updateNotes?format=raw', update_ok, null, 'POST',  argvs);
}

function update_ok()
{
	alert('Notes words updated successful!');
}

</script>
<form name="ingredientsForm" id="ingredientsForm" method="post" action="ingredients/default_value_save?page=<?php echo Request::getInt('page', 1);?>">
<table>
    <tr class="metadata">
        <td colspan="5" style="border: 0px;">
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
    <tr>
    <td colspan="5">Search: <input type='text' id='search' value='' />&nbsp;&nbsp;<input type='button' value='Go!' onclick="seach_go()" />&nbsp;&nbsp;<input type='reset' value='Reset'>&nbsp;&nbsp;<input type='Submit' value='Commit Change'>&nbsp;&nbsp;<input type='Button' id="show_add_new" value='Add New Ingredient' onclick="add_new();">
	<div id="new_ingredient" style="display:none; margin-top:5px;">
		<input type="text" name="new_default_value" style="margin-left:105px; width:200px;" />
		<input type="text" name="new_feed_back" style="width:200px; margin-left:15px;" />
		<input type="text" name="new_long_desc" style="width:500px; margin-left:15px;" />
		<input type="submit" name="add_new_ingredient" value="Save"  />
	</div>
	</td>
    </tr>

    <tr>
        <th>id</th>
        <th>NDB No</th>
      <th>Default Value<br>
        <span class="STYLE1">(what user will type in)</span></th>
        <th>Feed Back<br><span class="STYLE1">(how it will display to user after matching)</span></th>
        <th>Long Description<br><span class="STYLE1">(full ingredient entry that default value will search against)</span></th>
    </tr>
    <?php foreach($ingredients as $row){?>
    <tr>
        <td><input name="id[]" type='hidden' width="50" value="<?php echo $row["id"];?>" /><?php echo $row["id"];?></td>
        <td><input name="NDB_No[]" type='hidden' width="50" value="<?php echo $row["NDB_No"];?>" /><?php echo $row["NDB_No"];?></td>
        <td><input name="default_value[]" style='width:200px;' value="<?php echo $row["default_value"];?>" /></th>
        <td><input name="feed_back[]" style='width:200px;' value="<?php echo $row["feed_back"];?>" /></td>
        <td><input name="long_desc[]" style='width:500px;' value="<?php echo $row["long_desc"];?>" /></td> 
    </tr>
    <?php }?>
    <tr><td colspan="13"><input type='reset' value='Reset' >&nbsp;&nbsp;<input type='Submit' value='Commit Change'></td></tr>
    
    <tr>
        <td colspan="13">
      	<textarea name="notes_words" id="notes_words"><?php echo $noteswords;?></textarea><br />
		<input type="button" value="Update the notes words" name="up_notes" onclick="save_notes()" />
        </td>
    </tr>
    
    <tr><td colspan="13">
        <input type="text" id="testSearch" value="" style="width:800px;" />&nbsp;&nbsp;
        <input type="button" value="Try the new rule" onClick='getSearch()'><br />
        <pre> 
            <div id="testSearchDisplay">
            </div>
        </pre>
    </td></tr>
</table>
</form>