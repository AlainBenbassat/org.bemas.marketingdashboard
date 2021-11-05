<p>{$filterLinks}<br></p>

<h3>Samenvatting</h3>
<table class="display">
  <thead>
  <tr>
    <th>Bron</th>
    <th>Aantal</th>
  </tr>
  </thead>
    {foreach from=$sourceSummary item=row}
      <tr class="crm-entity">
        <td>{$row.source}</td>
        <td>{$row.num_sources}</td>
      </tr>
    {/foreach}
</table>

<h3>Andere bronnen</h3>
<table class="display">
  <thead>
  <tr>
    <th>Datum</th>
    <th>Evenement</th>
    <th>Antwoord</th>
  </tr>
  </thead>
    {foreach from=$sources item=row}
      <tr class="crm-entity">
        <td>{$row.date}</td>
        <td>{$row.event}</td>
        <td>{$row.submission}</td>
      </tr>
    {/foreach}
</table>
