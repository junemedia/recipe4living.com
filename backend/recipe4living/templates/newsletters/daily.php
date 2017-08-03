<style>
  #items_data td {
    padding: 5px 5px 10px;
    border: none;
  }
  #items_data td.label {
    width: 10%;
    text-align: right;
    font-weight: bold;
  }
  #items_data input[type=text] {
    width: 80%;
  }
</style>

<div class="centered horizontal" style="clear:both">&nbsp;</div>
<h3>Daily Recipe: <?php echo $this->_campaign['campaign']; ?><h3>
<div class="centered horizontal">&nbsp;</div>

<div>
  <form action="<?= SITEURL.$baseUrl.'/newsletters/campaign/'.$this->_campaign['id'] ?>" method="POST">
    
    <table id="items_data" class="centered horizontal" style="width: 1200px;">

      <tr>
        <td class="label">Featured item:</td>
        <td><input type="text" name="feature" value="<?php echo $items[0]['targetUrl']; ?>" /></td>
      </tr>

      <tr>
        <td class="label">MWL 1:</td>
        <td><input type="text" name="mwl1" value="<?php echo $items[1]['targetUrl']; ?>" /></td>
      </tr>

      <tr>
        <td class="label">MWL 2:</td>
        <td><input type="text" name="mwl2" value="<?php echo $items[2]['targetUrl']; ?>" /></td>
      </tr>

      <tr>
        <td class="label">MWL 3:</td>
        <td><input type="text" name="mwl3" value="<?php echo $items[3]['targetUrl']; ?>" /></td>
      </tr>

      <tr>
        <td class="label">MWL 4:</td>
        <td><input type="text" name="mwl4" value="<?php echo $items[4]['targetUrl']; ?>" /></td>
      </tr>

      <tr style="border:none;">
        <td>&nbsp;</td>
        <td>
          <input type="submit" name="update" value="Save" />
        </td>
      </tr>
    </table>
  </form>
</div>
