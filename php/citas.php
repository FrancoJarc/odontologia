<?php
$conn = new mysqli("localhost", "root", "", "odontologia");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Insertar cita si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_afiliado = $_POST["id-afiliado"];
    $id_odontologo = $_POST["id-odontologo"];
    $id_sede = $_POST["id-sede"];
    $id_tratamiento = $_POST["id-tratamiento"];
    $fecha_completa = $_POST["fecha-hora"];

    // Convertir a formato compatible con MySQL DATETIME
    $fecha = date("Y-m-d H:i:s", strtotime($fecha_completa));

    $stmt = $conn->prepare("INSERT INTO Cita (ID_Afiliado, ID_Odontologo, ID_Sede, ID_Tratamiento, Fecha) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $id_afiliado, $id_odontologo, $id_sede, $id_tratamiento, $fecha);
    $stmt->execute();
    $stmt->close();
}

// Obtener citas con nombres y apellidos relacionados
$citas = $conn->query("
    SELECT c.ID_Cita, 
           CONCAT(a.Nombre, ' ', a.Apellido) AS Afiliado, 
           CONCAT(o.Nombre, ' ', o.Apellido) AS Odontologo, 
           s.Nombre AS Sede, 
           t.Nombre AS Tratamiento, 
           c.Fecha
    FROM Cita c
    JOIN Afiliado a ON c.ID_Afiliado = a.ID_Afiliado
    JOIN Odontologo o ON c.ID_Odontologo = o.ID_Odontologo
    JOIN Sede s ON c.ID_Sede = s.ID_Sede
    JOIN Tratamiento t ON c.ID_Tratamiento = t.ID_Tratamiento
    ORDER BY c.ID_Cita ASC
");

// Cargar opciones para los <select>
$afiliados = $conn->query("SELECT ID_Afiliado, Nombre, Apellido FROM Afiliado");
$odontologos = $conn->query("SELECT ID_Odontologo, Nombre, Apellido FROM Odontologo");
$sedes = $conn->query("SELECT ID_Sede, Nombre FROM Sede");
$tratamientos = $conn->query("SELECT ID_Tratamiento, Código, Nombre FROM Tratamiento");
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
                    <li><a href="../html/index.html">Inicio</a></li>
                    <li><a href="../php/consultorio.php">Sedes</a></li>
                    <li><a href="../html/equipamiento.html">Equipamiento/Insumos</a></li>
                    <li><a href="../php/afiliados.php">Afiliados</a></li>
                    <li><a href="../php/odontologos.php">Odontólogos</a></li>
                    <li><a href="../php/citas.php">Citas</a></li>
                    <li><a href="../php/tratamiento.php">Tratamientos</a></li>
                    <li><a href="../html/historia-clinica.html">Historia Clínica</a></li>
                    <li><a href="../html/portal-cliente.html">Portal Paciente</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="citas">
        <div class="container">
            <h3>Lista de Citas</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Cita</th>
                        <th>Afiliado</th>
                        <th>Odontólogo</th>
                        <th>Sede</th>
                        <th>Tratamiento</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($cita = $citas->fetch_assoc()): ?>
                        <tr>
                            <td><?= $cita["ID_Cita"] ?></td>
                            <td><?= $cita["Afiliado"] ?></td>
                            <td><?= $cita["Odontologo"] ?></td>
                            <td><?= $cita["Sede"] ?></td>
                            <td><?= $cita["Tratamiento"] ?></td>
                            <td><?= date("d/m/Y H:i", strtotime($cita["Fecha"])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Registrar Cita</h2>
            <form method="POST" action="">
                <label for="id-afiliado">Afiliado:</label>
                <select id="id-afiliado" name="id-afiliado" required>
                    <option value="">Seleccionar afiliado</option>
                    <?php while ($a = $afiliados->fetch_assoc()): ?>
                        <option value="<?= $a['ID_Afiliado'] ?>">
                            <?= $a['Nombre'] . " " . $a['Apellido'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="id-odontologo">Odontólogo:</label>
                <select id="id-odontologo" name="id-odontologo" required>
                    <option value="">Seleccionar odontólogo</option>
                    <?php while ($o = $odontologos->fetch_assoc()): ?>
                        <option value="<?= $o['ID_Odontologo'] ?>">
                            <?= $o['Nombre'] . " " . $o['Apellido'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="id-sede">Sede:</label>
                <select id="id-sede" name="id-sede" required>
                    <option value="">Seleccionar sede</option>
                    <?php while ($s = $sedes->fetch_assoc()): ?>
                        <option value="<?= $s['ID_Sede'] ?>"><?= $s['Nombre'] ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="id-tratamiento">Tratamiento:</label>
                <select id="id-tratamiento" name="id-tratamiento" required>
                    <option value="">Seleccionar tratamiento</option>
                    <?php while ($t = $tratamientos->fetch_assoc()): ?>
                        <option value="<?= $t['ID_Tratamiento'] ?>">
                            <?= $t['Código'] . " - " . $t['Nombre'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="fecha-hora">Fecha y Hora:</label>
                <input type="datetime-local" id="fecha-hora" name="fecha-hora" required />

                <button type="submit">Crear Cita</button>
            </form>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-container">
            <p>&copy; 2025 Clínica Odontológica. Todos los derechos reservados.</p>
            <ul class="footer-links">
                <li><a href="../html/contacto.html">Contacto</a></li>
                <li><a href="#">Política de Privacidad</a></li>
            </ul>
        </div>
    </footer>
</body>

</html>

<?php $conn->close(); ?>