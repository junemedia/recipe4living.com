<?php if(!empty($campaigns)) { ?>

  <table id="items_data" class="centered horizontal" style="width: 1200px">

    <tr class="metadata">
      <td colspan="3" style="border: none;">
        <div style="height: 10px; margin: 23px 0px 5px;">
          <a href="<?php echo SITEURL . "/newsletters/{$this->_newsletter}/new" ?>" style="font-weight:bold;text-decoration:none;">+ New campaign</a></div>
      </td>
    </tr>

    <tr class="metadata">
      <!--th class="textfield" style="padding: 5px;">&nbsp;</th-->
      <th class="textfield" style="padding: 5px;">Date</th>
      <th class="textfield" style="padding: 5px;">Subject</th>
      <th class="textfield" style="padding: 5px;">&nbsp;</th>
    </tr>

    <?php foreach ($campaigns as $campaign) { ?>
    <tr>

      <!--td style="text-align: center;"><img src="<?= ASSETURL.'/campaignimages/50/50/1/'.$campaign['filename'] ?>" alt="" /></td-->
      <td style="text-align: center;"><?php echo $campaign['campaign']; ?></td>
      <td class="textfield" style="padding: 5px;"><?= Text::trim($campaign['subject'], 100); ?></td>
      <td style="padding: 5px; text-align: center;"><a href="<?php echo SITEURL . "/newsletters/{$this->_newsletter}/{$campaign['id']}"; ?>">Edit</a></td>
    </tr>
    <?php } ?>

  </table>

<?php } else { ?>

  No campaigns

<?php } ?>