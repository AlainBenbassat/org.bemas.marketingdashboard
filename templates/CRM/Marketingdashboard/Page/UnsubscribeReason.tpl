<h3>Samenvatting</h3>
<table class="display">
  <thead>
  <tr>
    <th>Reden</th>
    <th>Aantal</th>
  </tr>
  </thead>
  {foreach from=$reasonSummary item=row}
    <tr class="crm-entity">
      <td>{$row.reason}</td>
      <td>{$row.num_reasons}</td>
    </tr>
  {/foreach}
</table>

<h3>Andere redenen</h3>
<p><br>{$filterLinks}</p>
<table class="display">
  <thead>
  <tr>
    <th>Naam</th>
    <th>Reden</th>
  </tr>
  </thead>
    {foreach from=$reasons item=row}
      <tr class="crm-entity">
        <td>{$row.display_name}</td>
        <td>{$row.reason}</td>
      </tr>
    {/foreach}
</table>
