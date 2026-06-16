    <?php

    require_once(__DIR__ . "/../backend/config/conexion.php");
    session_start();

    if (!isset($_SESSION["usuario"])) {
        header("Location: login.php");
        exit();
    }

    $pageTitle  = "Detalle de Personal";
    $activePage = "detalle";

    define('BASE_CSS', 'css/');
    define('BASE_ASSETS', '../../assets/');

    $extraCSS = '<link rel="stylesheet" href="' . BASE_CSS . 'detalle.css">';

    $query = "

    SELECT
        p.id_personal,
        p.nombres,
        p.cip,
        p.dni,
        p.fecha_nacimiento,
        p.fecha_salida,
        p.nro_cuenta,
        p.departamento,
        p.provincia,
        p.distrito,
        p.direccion,
        p.obs,
        g.nombre  AS grado,
        a.nombre  AS arma,
        u.nombre  AS unidad,
        ep.nombre AS estado

    FROM personal_militar p

    LEFT JOIN grado g
    ON p.id_grado = g.id_grado

    LEFT JOIN arma a
    ON p.id_arma = a.id_arma

    LEFT JOIN unidad u
    ON p.id_unidad = u.id_unidad

    LEFT JOIN estado_personal ep
    ON p.id_estado = ep.id_estado

    ORDER BY p.id_personal DESC

    ";

    $result = pg_query($conn, $query);

    include 'includes/layout.php';

    ?>

    <div class="page-header">

        <div class="page-header-title">

            <h1>Detalle de Personal</h1>

            <p>
                Información completa del personal registrado
            </p>

        </div>

    </div>

    <div class="card detalle-card">

        <div class="detalle-header">

            <h3>

                <i class="fa fa-id-card"></i>

                Lista Detallada

            </h3>

            <span class="detalle-badge">

                <?= pg_num_rows($result) ?> registros

            </span>

        </div>

        <div class="table-wrap">

            <table class="detalle-table">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Personal</th>

                        <th>Grado</th>

                        <th>Unidad</th>

                        <th>DNI</th>

                        <th>CIP</th>

                        <th>Ingreso</th>

                        <th>Estado</th>


                    </tr>

                </thead>

                <tbody>

                    <?php $i = 1; ?>

                    <?php while ($row = pg_fetch_assoc($result)): ?>

                        <?php

                        $palabras = explode(' ', trim($row['nombres'] ?? ''));

                        $iniciales = strtoupper(

                            substr($palabras[0] ?? '', 0, 1) .

                                substr($palabras[1] ?? '', 0, 1)

                        );

                        ?>

                        <tr>

                            <td class="td-id">

                                <?= $i++ ?>

                            </td>

                            <td>

                                <div class="td-user">

                                    <div class="td-av">

                                        <?= $iniciales ?>

                                    </div>

                                    <div class="td-user-info">

                                        <span class="td-name">

                                            <?= htmlspecialchars($row['nombres'] ?? '') ?>

                                        </span>

                                        <span class="td-sub">

                                            ID: <?= $row['id_personal'] ?>

                                        </span>

                                    </div>

                                </div>

                            </td>

                            <td>

                                <span class="grado-tag">

                                    <?= htmlspecialchars($row['grado'] ?? '-') ?>

                                </span>

                            </td>


                            <td>

                                <?= htmlspecialchars($row['unidad'] ?? '-') ?>

                            </td>


                            <td>

                                <?= htmlspecialchars($row['dni'] ?? '-') ?>

                            </td>


                            <td class="td-cip">

                                <code>

                                    <?= htmlspecialchars($row['cip'] ?? '-') ?>

                                </code>

                            </td>

                            <td>

                                <?= htmlspecialchars($row['fecha_ingreso'] ?? '-') ?>

                            </td>

                            <td>

                                <span class="badge">

                                    <?= htmlspecialchars($row['estado'] ?? '-') ?>

                                </span>

                            </td>


                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

    <?php include 'includes/layout_end.php'; ?>