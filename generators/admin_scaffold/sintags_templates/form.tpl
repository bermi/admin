<?php  echo '<%'?>= error_messages_for '<?php  echo $model_name?>' %>

<?php  if(empty($content_columns)) : ?>
<?php  echo '<%'?>= all_input_tags <?php  echo $model_name?>, '<?php  echo $model_name?>', {} %>
<?php  else : 
        foreach ($content_columns as $column=>$details){
            if($column == 'id'){
                continue;
            }
            echo "
    <fieldset>
        <label for=\"{$singular_name}_{$column}\">_{".
            AkInflector::titleize($details['name']).
            "}</label> 
        <%= input '$model_name', '$column' %>
    </fieldset>

";
        }
endif;

?>
