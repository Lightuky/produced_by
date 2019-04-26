<div class = "titlePage">
  <h2>{'Produit Par'|@translate}</h2>
</div>

<!-- Create the form for creating and editing copyrights -->
<form action='{$PRODUCEDBY_PATH}-{if $edit == 0}create{else}update{/if}'
      method='POST'>
  <fieldset>
    <legend>{if $edit == 0}{'Ajouter un auteur'|@translate}
            {else}{'Modifier un auteur'|@translate}{/if}</legend>
      <!-- If $edit != 0 we should remember the id of the copyright that is
           edited -->
      {if $edit != 0}<input type='hidden' name='id' id='id' value='{$PBid}' />{/if}
      <!-- Put all the attributes of the copyright in a nice table -->
      <table>
        <tr>
          <td>{'Name'|@translate}</td>
          <td><input type='text' name='name' id='name' value='{$PBname|escape}' /></td>
        </tr>
        <tr>
          <td></td>
          <td><input type='submit' value="{if $edit == 0}{'Create'|@translate}{else}{'Update'|@translate}{/if}" /></td>
        </tr>
      </table>
  </fieldset>
</form>

<!-- If we are on the 'homepage', show a table of all copyrights -->
{if $edit == 0}
<form>
  <fieldset>
    <legend>{'Modifier un auteur'|@translate}</legend>
    <table>
      <tr>
        <!-- Create a nice header row -->
        <th>{'Name'|@translate}</th>
        <th>{'Actions'|@translate}</th>
      </tr>
    <!-- Loop over all copyrights -->
    {if not empty($PBs)}
    {foreach from=$PBs item=PB}
    {strip}
      <tr class="{cycle values="row1,row2"}"> <!-- This gives nicely colored
                                                   table rows -->
        <td>{$PB.name}</td>
        <!-- Show nice Edit and Delete icons -->
        <td>
          <a href="{$PRODUCEDBY_PATH}-edit&id={$PB.pb_id}">
            <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/edit_s.png"
            alt="{'Edit'|@translate}" title="{'Edit'|@translate}" />
          </a>
          <a href="{$PRODUCEDBY_PATH}-delete&id={$PB.pb_id}" onclick="return confirm(document.getElementById('btn_delete').title + '\n\n' + '{'Etes vous sur?'|@translate|@escape:'javascript'}');">
            <img src="{$ROOT_URL}{$themeconf.admin_icon_dir}/delete.png"
            id="btn_delete" alt="{'Supprimer'|@translate}" title="{'Supprimer un auteur'|@translate}" />
          </a>
        </td>
      </tr>
    {/strip}
    {/foreach}
    {/if}
    </table>
  </fieldset>
</form>
{/if}
