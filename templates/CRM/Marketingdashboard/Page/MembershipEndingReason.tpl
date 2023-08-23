<p>{$filterLinks}<br></p>

<h3>Samenvatting</h3>
<table class="display">
  <thead>
  <tr>
    <th>Reden</th>
    <th>Aantal</th>
  </tr>
  </thead>
    {foreach from=$reasonsSummary item=row}
      <tr class="crm-entity">
        <td>{$row.reason}</td>
        <td>{$row.num_reasons}</td>
      </tr>
    {/foreach}
</table>

<h3>Opzegingen</h3>
<table class="display">
  <thead>
  <tr>
    <th>Organisatie</th>
    <th>Reden</th>
  </tr>
  </thead>
    {foreach from=$reasons item=row}
      <tr class="crm-entity">
        <td>{$row.contact}</td>
        <td>{$row.reason}</td>
      </tr>
    {/foreach}
</table>
