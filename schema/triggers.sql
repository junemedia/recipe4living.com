DELIMITER $$

DROP TRIGGER IF EXISTS `newsletterItem_after_update`;

CREATE TRIGGER `newsletterItem_after_update` AFTER UPDATE
  ON `newsletterItem`
  FOR EACH ROW
  BEGIN
    -- only do the update if a change has been made
    IF NEW.targetUrl <> OLD.targetUrl THEN
      UPDATE  `newsletterCampaign`
      SET     `updated` = CURRENT_TIMESTAMP
      WHERE   `id` = NEW.newsletterCampaignId;
    END IF;
END $$
DELIMITER ;

