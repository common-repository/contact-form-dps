<?php

  require_once("../../../wp-config.php");
  require_once("../../../wp-content/plugins/contact-form-dps/contact_settings.php");


  global $wpdb;
$querydata = $wpdb->get_results(
  "
   SELECT * FROM wpcontract WHERE id = '$_POST[ids]'
  "
  );

 global $wpdb;
$querydata = $wpdb->get_results(
  "
   DELETE FROM wpcontract WHERE id = '$_POST[ids]'
  "
  );
 
  header("Location: ../../../wp-admin/admin.php?page=contact-form-dps/contact_settings.php");
