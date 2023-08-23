<p>{$filterLinks}<br></p>

<h3>Andere onderwerpen</h3>
<table class="display">
  <thead>
  <tr>
    <th>Datum</th>
    <th>Evenement</th>
    <th>Onderwerp</th>
    <th>Antwoord</th>
  </tr>
  </thead>
    {foreach from=$sources item=row}
      <tr class="crm-entity">
        <td>{$row.date}</td>
        <td>{$row.event}</td>
        <td>{$row.topic}</td>
        <td>{$row.submission}</td>
      </tr>
    {/foreach}
</table>
