<?php
$CamelCaseSingular = AkInflector::camelize($singular_name);
$CamelCasePlural = AkInflector::camelize($plural_name);
?>
<div id="content_menu">
    <ul class="menu">
        <li class="primary"><?php  echo '<%='?> link_to _('Create new <?php  echo AkInflector::titleize($singular_name)?>'), :action => 'add' %></li>
        <li class="active"><?php  echo '<%='?> link_to _('Listing <?php  echo AkInflector::titleize($plural_name)?>'), :action => 'listing' %></li>
    </ul>
    <p class="information">{_controller_information}</p>
</div>

<div class="content">
{?<?php  echo $CamelCasePlural?>}
<h1>_{Listing available <?php  echo AkInflector::titleize($plural_name)?>}</h1>
  <div class="listing">
  <table cellspacing="0" summary="_{Listing available <?php  echo AkInflector::titleize($plural_name)?>}">
  
  <tr>
    <?php  echo '<?php  '?>$content_columns = array_keys($<?php  echo $model_name?>->getContentColumns()); ?>
    {loop content_columns}
        <th scope="col"><?php  echo '<%='?> sortable_link content_column %></th>
    {end}
    <th colspan="3" scope="col"><span class="auraltext">_{Item actions}</span></th>
  </tr>

  {loop <?php  echo $CamelCasePlural?>}
    <tr {?<?php  echo $CamelCaseSingular?>_odd_position}class="odd"{end}>
    {loop content_columns}
      <td class="field"><?php  echo '<?php '?>echo $<?php  echo $CamelCaseSingular?>->get($content_column) ?></td>
    {end}
      <td class="operation"><?php  echo '<%='?> link_to_show <?php  echo $CamelCaseSingular?> %></td>
      <td class="operation"><?php  echo '<%='?> link_to_edit <?php  echo $CamelCaseSingular?> %></td>
      <td class="operation"><?php  echo '<%='?> link_to_destroy <?php  echo $CamelCaseSingular?> %></td>    
    </tr>
  {end}
   </table>
  </div>
  
  {?<?php  echo $singular_name?>_pages.links}
      <div class="paginator">
      <div id="header"><?php  echo '<?php  echo '?>translate('Showing page %page of %number_of_pages',array('%page'=>$<?php  echo $singular_name?>_pages->getCurrentPage(),'%number_of_pages'=>$<?php  echo $singular_name?>_pages->pages))?></div>
      {<?php  echo $singular_name?>_pages.links?}
      </div>
  {end}
  
  {else}
  
  <h1>_{No <?php  echo AkInflector::titleize($plural_name)?> available yet.}</h1>
  
  <p><?php  echo '<%='?> link_to _('Click here to create the first <?php  echo AkInflector::titleize($singular_name)?>'), :action => 'add' %></p>
  
  {end} 
  
</div>












