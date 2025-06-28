<?php
$conn = new mysqli("localhost", "root", "", "odontologia");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dni = $_POST["dni"];
    $idOdontologo = $_POST["id-odontologo"];
    $idSede = $_POST["id-sede"];
    $idTratamiento = $_POST["id-tratamiento"];
    $fechaHora = $_POST["fecha"]; // datetime-local

    // Buscar ID_Afiliado por DNI
    $stmt = $conn->prepare("SELECT ID_Afiliado FROM afiliado WHERE DNI = ?");
    $stmt->bind_param("s", $dni);
    $stmt->execute();
    $stmt->bind_result($idAfiliado);
    if ($stmt->fetch()) {
        $stmt->close();

        // Insertar cita
        $insert = $conn->prepare("INSERT INTO cita (ID_Afiliado, ID_Odontologo, ID_Sede, ID_Tratamiento, Fecha) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("sssss", $idAfiliado, $idOdontologo, $idSede, $idTratamiento, $fechaHora);
        $insert->execute();
        $insert->close();
    } else {
        $stmt->close();
        // DNI no encontrado, podrías agregar algo aquí si querés
    }
}

// Traer datos para selects
$odontologos = $conn->query("SELECT ID_Odontologo, Nombre, Apellido FROM odontologo");
$sedes = $conn->query("SELECT ID_Sede, Nombre FROM sede");
$tratamientos = $conn->query("SELECT ID_Tratamiento, Nombre FROM tratamiento");

// Traer todas las citas para mostrar
$citas = $conn->query("
    SELECT c.ID_Cita, a.DNI, a.Nombre AS AfiliadoNombre, a.Apellido AS AfiliadoApellido,
           o.Nombre AS OdontologoNombre, o.Apellido AS OdontologoApellido,
           s.Nombre AS SedeNombre,
           t.Nombre AS TratamientoNombre,
           c.Fecha
    FROM cita c
    JOIN afiliado a ON c.ID_Afiliado = a.ID_Afiliado
    JOIN odontologo o ON c.ID_Odontologo = o.ID_Odontologo
    JOIN sede s ON c.ID_Sede = s.ID_Sede
    JOIN tratamiento t ON c.ID_Tratamiento = t.ID_Tratamiento
    ORDER BY c.Fecha DESC
");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Citas - Clínica Odontológica</title>
    <link rel="stylesheet" href="../css/estilos.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
</head>

<body>
    <header>
        <div class="container-nav">
            <img src="../imagenes/logo.png" alt="Logo Clínica Odontológica" class="logo" />
            <nav>
                <ul>
                    <li><a href="../html/portal-cliente.html">Inicio</a></li>
                    <li><a href="../html/sedes-consultorio-cliente.html">Sedes</a></li>
                    <li><a href="../html/odontologos-cliente.html">Odontólogos</a></li>
                    <li><a href="../php/citas-cliente.php">Citas</a></li>
                    <li><a href="../html/tratamiento-cliente.html">Tratamientos</a></li>
                    <li><a href="../html/historia-clinica-cliente.html">Historia Clínica</a></li>
                    <li><a href="../html/index.html">Portal Odontólogo</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="citas">
        <div class="container">
            <h3>Tus citas anteriores</h3>
            <table>
                <thead>
                    <tr>
                        <th>Código de Cita</th>
                        <th>DNI</th>
                        <th>Afiliado</th>
                        <th>Odontólogo</th>
                        <th>Sede</th>
                        <th>Tratamiento</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = $citas->fetch_assoc()):
                        $fechaHora = new DateTime($c['Fecha']);
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($c['ID_Cita']) ?></td>
                            <td><?= htmlspecialchars($c['DNI']) ?></td>
                            <td><?= htmlspecialchars($c['AfiliadoNombre'] . ' ' . $c['AfiliadoApellido']) ?></td>
                            <td><?= htmlspecialchars($c['OdontologoNombre'] . ' ' . $c['OdontologoApellido']) ?></td>
                            <td><?= htmlspecialchars($c['SedeNombre']) ?></td>
                            <td><?= htmlspecialchars($c['TratamientoNombre']) ?></td>
                            <td><?= $fechaHora->format('Y-m-d') ?></td>
                            <td><?= $fechaHora->format('H:i') ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Registrar Cita</h2>

            <form method="POST" action="">
                <label for="dni">DNI del afiliado:</label>
                <input type="text" name="dni" id="dni" required />

                <label for="id-odontologo">Odontólogo:</label>
                <select name="id-odontologo" id="id-odontologo" required>
                    <option value="">Seleccionar odontólogo</option>
                    <?php
                    $odontologos->data_seek(0);
                    while ($o = $odontologos->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($o['ID_Odontologo']) ?>">
                            <?= htmlspecialchars($o['Nombre'] . ' ' . $o['Apellido']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="id-sede">Sede:</label>
                <select name="id-sede" id="id-sede" required>
                    <option value="">Seleccionar sede</option>
                    <?php
                    $sedes->data_seek(0);
                    while ($s = $sedes->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($s['ID_Sede']) ?>">
                            <?= htmlspecialchars($s['Nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="id-tratamiento">Tratamiento:</label>
                <select name="id-tratamiento" id="id-tratamiento" required>
                    <option value="">Seleccionar tratamiento</option>
                    <?php
                    $tratamientos->data_seek(0);
                    while ($t = $tratamientos->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($t['ID_Tratamiento']) ?>">
                            <?= htmlspecialchars($t['Nombre']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="fecha">Fecha y hora:</label>
                <input type="datetime-local" id="fecha" name="fecha" required />

                <button type="submit">Registrar Cita</button>
            </form>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2025 Clínica Odontológica. Todos los derechos reservados.</p>
            <ul class="footer-links">
                <li><a href="contacto.html">Contacto</a></li>
                <li><a href="#">Política de Privacidad</a></li>
            </ul>
        </div>
    </footer>

</body>

</html>

<?php
$conn->close();
?>