<%= error_messages_for 'role' %>

    <fieldset>
        <label for="role_name">_{Name}</label> 
        <%= input 'role', 'name' %>
    </fieldset>

    <fieldset>
        <label for="role_description">_{Description}</label> 
        <%= input 'role', 'description' %>
    </fieldset>
    <? if(!$role->nested_set->isRoot()) : ?>
    <fieldset>
        <label for="role_is_enabled">_{Is enabled}</label> 
        <%= input 'role', 'is_enabled' %>
    </fieldset>
    <? endif; ?>
