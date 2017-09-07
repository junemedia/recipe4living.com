<?php
  /*
   * if the campaign is in the past, and it's not a new campaign,
   * make it non-editable
   */
  $viewOnly = $this->_campaign['campaign'] < date('Y-m-d') &&
              $this->_campaign['id'] !== 0;
  $formClass = $viewOnly ? 'view_only'
                         : '';
  $items = $this->_campaign['items'];
?>

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

  #newsletterEditForm.view_only input[type=text] {
    border: none;
    background-color: white;
  }
</style>

<h2><?php echo $this->_newsletter['label']; ?></h2>
<div class="centered horizontal">&nbsp;</div>

<div>
  <form id="newsletterEditForm" class="<?php echo $formClass; ?>" action="<?php echo SITEURL."/newsletters/{$this->_newsletter['id']}/{$this->_campaign['id']}" ?>" method="POST" onsubmit="return newsletterValidateForm(this)">
    <input type="hidden" name="newsletterCampaignId" value="<?php echo $this->_campaign['id']; ?>"/>
    <input type="hidden" name="newsletter" value="<?php echo $this->_newsletter['id']; ?>"/>

    <table id="items_data" class="centered horizontal" style="width: 1200px;">
      <tr>
        <td class="label">Date:</td>
        <td><input type="text" name="date" id="datepicker" value="<?php echo $this->_campaign['campaign']; ?>" placeholder="yyyy-mm-dd" style="width: 8em;" /></td>
      </tr>

      <tr>
        <td class="label">Subject:</td>
        <td><input type="text" name="subject" value="<?php echo htmlspecialchars($this->_campaign['subject']); ?>" /></td>
      </tr>

      <tr>
        <td class="label">Feature:</td>
        <td><input type="text" name="feature" value="<?php echo htmlspecialchars($items[0]['targetUrl']); ?>" /></td>
      </tr>

      <?php for ($i = 1; $i < count($items); $i++) { ?>
      <tr>
        <td class="label">More <?php echo $i; ?>:</td>
        <td><input type="text" name="mwl<?php echo $i; ?>" value="<?php echo htmlspecialchars($items[$i]['targetUrl']); ?>" /></td>
      </tr>
      <?php } ?>


      <?php if (!$viewOnly) { ?>
      <tr style="border:none;">
        <td>&nbsp;</td>
        <td>
          <button type="submit" name="process" value="update">Save &amp; refresh</button>
          <button type="submit" name="process" value="save">Save &amp; quit</button>
        </td>
      </tr>
      <?php } ?>

      <tr style="border:none;">
        <td colspan="2" style="height:25px;">&nbsp;</td>
      </tr>

      <?php if ($this->_campaign['id'] !== 0) { ?>
      <tr style="background-color: white !important">
        <td class="label" style="vertical-align:top"> Preview: </td>
        <td>
          <iframe src="<?php echo "{$this->_apiUrl}/newsletter/{$this->_newsletter['id']}.html/{$this->_campaign['campaign']}"; ?>" id="iframe1" frameborder="0" scrolling="auto" width="800" height="1600"></iframe>
        </td>
      </tr>
      <?php } ?>

    </table>
  </form>
</div>

<?php include(BLUPATH_TEMPLATES.'/newsletters/foot.html'); ?>
