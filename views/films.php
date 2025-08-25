<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Películas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Películas</h1>
    <form class="row mb-4" method="get" action="/films">
        <div class="col-md-8">
            <input type="text" name="search" class="form-control" placeholder="Buscar por título..." value="<?= htmlspecialchars($search ?? '') ?>">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary w-100">Buscar</button>
        </div>
    </form>
    <?php if (!empty($films)): ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Título</th>
                <th>Año</th>
                <th>Duración</th>
                <th>Rating</th>
                <th>Actores</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($films as $film): ?>
            <tr>
                <td><?= htmlspecialchars($film['title']) ?></td>
                <td><?= htmlspecialchars($film['release_year']) ?></td>
                <td><?= htmlspecialchars($film['length']) ?> min</td>
                <td><?= htmlspecialchars($film['rating']) ?></td>
                <td>
                    <?php if (!empty($film['actors'])): ?>
                        <?= htmlspecialchars(implode(', ', $film['actors'])) ?>
                    <?php else: ?>
                        <em>No disponible</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="/films?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php else: ?>
        <div class="alert alert-info">No se encontraron películas.</div>
    <?php endif; ?>
</div>
</body>
</html>
