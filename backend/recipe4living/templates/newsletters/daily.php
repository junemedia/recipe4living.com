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
  button {
    font-size: 10px;
    padding: 4px 10px;
    border: solid 1px #ccc;
    background-color: #e7eaef;
    border-radius: 6px;
    margin-right: .5em;
    color: #555;
    width: 10em;
  }
</style>

<div class="centered horizontal" style="clear:both">&nbsp;</div>
<h2>Daily Recipes</h2>
<div class="centered horizontal">&nbsp;</div>

<div>
  <form action="<?php echo SITEURL.$baseUrl.'/newsletters/daily/'.$this->_campaign['id'] ?>" method="POST" onsubmit="return newsletterValidateForm(this)">
    <input type="hidden" name="newsletterCampaignId" value="<?php echo $this->_campaign['id']; ?>"/>
    <input type="hidden" name="newsletter" value="daily"/>

    <table id="items_data" class="centered horizontal" style="width: 1200px;">
      <tr>
        <td class="label">Date:</td>
        <td><input type="text" name="date" value="<?php echo $this->_campaign['campaign']; ?>" placeholder="yyyy-mm-dd" style="width: 8em;" /></td>
      </tr>

      <tr>
        <td class="label">Subject:</td>
        <td><input type="text" name="subject" value="<?php echo htmlspecialchars($this->_campaign['subject']); ?>" /></td>
      </tr>

      <tr>
        <td class="label">Feature:</td>
        <td><input type="text" name="feature" value="<?php echo htmlspecialchars($items[0]['targetUrl']); ?>" /></td>
      </tr>

      <tr>
        <td class="label">MWL 1:</td>
        <td><input type="text" name="mwl1" value="<?php echo htmlspecialchars($items[1]['targetUrl']); ?>" /></td>
      </tr>

      <tr>
        <td class="label">MWL 2:</td>
        <td><input type="text" name="mwl2" value="<?php echo htmlspecialchars($items[2]['targetUrl']); ?>" /></td>
      </tr>

      <tr>
        <td class="label">MWL 3:</td>
        <td><input type="text" name="mwl3" value="<?php echo htmlspecialchars($items[3]['targetUrl']); ?>" /></td>
      </tr>

      <tr>
        <td class="label">MWL 4:</td>
        <td><input type="text" name="mwl4" value="<?php echo htmlspecialchars($items[4]['targetUrl']); ?>" /></td>
      </tr>

      <tr style="border:none;">
        <td>&nbsp;</td>
        <td>
          <button type="submit" name="process" value="update">Save &amp; refresh</button>
          <button type="submit" name="process" value="save">Save &amp; quit</button>
        </td>
      </tr>

      <tr style="border:none;">
        <td colspan="2" style="height:25px;">&nbsp;</td>
      </tr>

      <tr style="background-color: white !important">
        <td class="label" style="vertical-align:top"> Preview: </td>
        <td>
          <iframe src="<?php echo "{$this->_apiUrl}/newsletter/{$this->_newsletter}.html/{$this->_campaign['campaign']}"; ?>" id="iframe1" frameborder="0" scrolling="auto" width="800" height="1600"></iframe>
        </td>
      </tr>
    </table>
  </form>
</div>

<script type="text/javascript">
  function newsletterValidateForm(form) {
    var d = form.date.value;
    if (!isValidDate(d)) {
      alert("A valid date of the form YYYY-MM-DD is required");
      return false;
    }
    return true;
  }

  function isValidDate(dateString) {
    var d;
    var regEx = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateString.match(regEx)) {
      return false;  // Invalid format
    }

    if(!((d = new Date(dateString))|0)) {
      return false; // Invalid date (or this could be epoch)
    }
    return d.toISOString().slice(0,10) == dateString;
  }
</script>
