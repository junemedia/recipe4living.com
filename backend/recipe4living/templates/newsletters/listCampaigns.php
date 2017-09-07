<h2><?php echo $this->_newsletter['label']; ?></h2>

<table id="items_data" class="centered horizontal" style="width: 1200px">

  <tr class="metadata">
    <td colspan="5" style="border: none;">
      <div style="height: 10px; margin: 23px 0px 5px;">
        <a href="<?php echo SITEURL . "/newsletters/{$this->_newsletter['id']}/new" ?>" style="font-weight:bold;text-decoration:none;">+ New campaign</a>
        <?php if ($this->_view === 'upcoming') { ?>
        <a href="<?php echo SITEURL . "/newsletters/{$this->_newsletter['id']}/archive" ?>" style="font-weight:bold;text-decoration:none;margin-left:3em;">View archive</a>
        <?php } else { ?>
        <a href="<?php echo SITEURL . "/newsletters/{$this->_newsletter['id']}" ?>" style="font-weight:bold;text-decoration:none;margin-left:3em;">View upcoming</a>
        <?php } ?>
      </div>
    </td>
  </tr>

  <tr class="metadata">
    <th class="textfield" style="padding: 5px;width:10em;">Date</th>
    <th class="textfield" style="padding: 5px;">Subject</th>
    <th class="textfield" style="padding: 5px;">Last Updated</th>
    <th class="textfield" style="padding: 5px;width:6em;">&nbsp;</th>
    <th class="textfield" style="padding: 5px;width:6em;">&nbsp;</th>
  </tr>

<?php foreach ($campaigns as $campaign) { ?>
  <tr>
    <td style="text-align: center;">
      <?php echo $campaign['campaign']; ?> </td>
    <td class="textfield" style="padding: 5px;">
      <?php echo Text::trim($campaign['subject'], 100); ?> </td>
    <td style="padding: 5px; text-align: center;">
      <?php echo Text::trim($campaign['updated']); ?> </td>

    <td style="padding: 5px; text-align: center;">
    <?php if ($this->_view === 'upcoming') { ?>
      <a href="<?php echo SITEURL . "/newsletters/{$this->_newsletter['id']}/{$campaign['id']}"; ?>">Edit</a>
    <?php } else { ?>
      <a href="<?php echo SITEURL . "/newsletters/{$this->_newsletter['id']}/{$campaign['id']}"; ?>">View</a>
    <?php } ?>
    </td>

    <td style="padding: 5px; text-align: center;">
    <?php if ($this->_view === 'upcoming') { ?>
      <a href="<?php echo SITEURL . "/newsletters/{$this->_newsletter['id']}/delete/{$campaign['id']}"; ?>" class="delete">Delete</a>
    <?php } ?>
    </td>
  </tr>
<?php } ?>

<?php if (empty($campaigns)) { ?>
  <tr>
    <td colspan="4" style="text-align:center">No campaigns</td>
  </tr>
<?php } ?>

</table>


<script>
  // Confirm before deletion
  (function () {
    var deleteLinks = document.querySelectorAll('a.delete');
    for (var i = 0; i < deleteLinks.length; i++) {
      deleteLinks[i].addEventListener('click', confirmDelete, false);
    }
  })();

  function confirmDelete(e) {
    if (!confirm('Are you sure you want to delete this campaign?')) {
      e.preventDefault();
    }
  }
</script>
