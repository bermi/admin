<?php
$CamelCaseSingular = AkInflector::camelize($singular_name);
?><div id="content_menu">
    <ul class="menu">
        <li><?php  echo '<%='?> link_to _('Create new <?php  echo AkInflector::titleize($singular_name)?>'), :action => 'add' %></li>
        <li class="primary"><?php  echo '<%='?> link_to _('Edit <?php  echo AkInflector::titleize($singular_name)?>'), :action => 'edit', :id => <?php  echo $model_name?>.id %></li>
        <li class="active"><?php  echo '<%='?> link_to _('Showing <?php  echo AkInflector::titleize($singular_name)?>'), :action => 'show', :id => <?php  echo $model_name?>.id %></li>
        <li><?php  echo '<%='?> link_to _('Delete <?php  echo AkInflector::titleize($singular_name)?>'), :action => 'destroy', :id => <?php  echo $model_name?>.id %></li>
        <li><?php  echo '<%='?> link_to _('Show available <?php  echo AkInflector::titleize($plural_name)?>'), :action => 'listing' %></li>
    </ul>
    <p class="information">{_controller_information}</p>
</div>


<div id="content">
  <h1>_{<?php  echo AkInflector::titleize($singular_name)?> details}</h1>

    <?php  echo '<?php  '?>$content_columns = array_keys($<?php  echo $model_name?>->getContentColumns()); ?>
    
    <dl>
    {loop content_columns}
      <dt><?php  echo '<%='?> translate( titleize( content_column ) ) %>:</dt>
      <dd><?php  echo '<?php  echo '?> $<?php  echo $CamelCaseSingular?>->get($content_column) ?>&nbsp;</dd>
    {end}
    </dl>

    <p class="operations"><?php  echo '<%='?> link_to_edit <?php  echo $CamelCaseSingular?> %> _{or} <?php  echo '<%='?> link_to_destroy <?php  echo AkInflector::camelize($singular_name)?> %> _{<?php  echo $singular_name?>} </p>
</div>
