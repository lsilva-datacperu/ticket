<h1>Crear Ticket</h1>
<form method="POST" action="/tickets/store">
    <label>Título:</label>
    <input type="text" name="title" required><br>

    <label>Descripción:</label>
    <textarea name="description" required></textarea><br>

    <label>Tipo:</label>
    <select name="request_type">
        <option value="mejora">Mejora</option>
        <option value="bug">Bug</option>
        <option value="nueva_funcionalidad">Nueva Funcionalidad</option>
    </select><br>

    <label>Prioridad:</label>
    <select name="priority">
        <option value="baja">Baja</option>
        <option value="media">Media</option>
        <option value="alta">Alta</option>
    </select><br>

    <label>Módulo:</label>
    <input type="number" name="module_id"><br>

    <label>ID Solicitante:</label>
    <input type="number" name="requested_by"><br>

    <button type="submit">Guardar</button>
</form>
