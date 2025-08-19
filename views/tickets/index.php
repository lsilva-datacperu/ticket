<h1>Listado de Tickets</h1>
<a href="/tickets/create">Nuevo Ticket</a>
<table border="1">
    <tr>
        <th>Código</th>
        <th>Título</th>
        <th>Tipo</th>
        <th>Prioridad</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>
    <?php foreach ($tickets as $ticket): ?>
        <tr>
            <td><?= htmlspecialchars($ticket['code']) ?></td>
            <td><?= htmlspecialchars($ticket['title']) ?></td>
            <td><?= htmlspecialchars($ticket['request_type']) ?></td>
            <td><?= htmlspecialchars($ticket['priority']) ?></td>
            <td><?= htmlspecialchars($ticket['status']) ?></td>
            <td>
                <a href="/tickets/view/<?= $ticket['id'] ?>">Ver</a>
                <?php if ($ticket['status'] !== 'cerrado'): ?>
                    <form method="POST" action="/tickets/close/<?= $ticket['id'] ?>" style="display:inline;">
                        <button type="submit">Cerrar</button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
