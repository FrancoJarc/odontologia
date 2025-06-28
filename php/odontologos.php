<?php
$conn = new mysqli("localhost", "root", "", "odontologia");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Insertar odontólogo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["dni"])) {
    $dni = $_POST["dni"];
    $nombre = $_POST["nombre"];
    $apellido = $_POST["apellido"];
    $id_especialidad = $_POST["id-especialidad"];

    $stmt = $conn->prepare("INSERT INTO Odontologo (DNI, Nombre, Apellido, ID_Especialidad) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $dni, $nombre, $apellido, $id_especialidad);
    $stmt->execute();
    $stmt->close();
}

// Obtener odontólogos con su especialidad
$odontologos = $conn->query("
    SELECT o.ID_Odontologo, o.DNI, o.Nombre, o.Apellido, e.Nombre AS Especialidad
    FROM Odontologo o
    JOIN Especialidad e ON o.ID_Especialidad = e.ID_Especialidad
");

// Obtener especialidades para el formulario
$especialidades = $conn->query("SELECT ID_Especialidad, Nombre FROM Especialidad");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Odontólogos - Clínica Odontológica</title>
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

    <section class="odontologos">
        <div class="container">
            <h3>Lista de Odontólogos</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DNI</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Especialidad</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($o = $odontologos->fetch_assoc()): ?>
                        <tr>
                            <td><?= $o["ID_Odontologo"] ?></td>
                            <td><?= $o["DNI"] ?></td>
                            <td><?= $o["Nombre"] ?></td>
                            <td><?= $o["Apellido"] ?></td>
                            <td><?= $o["Especialidad"] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Registrar Odontólogo</h2>
            <form method="POST" action="">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" required />

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required />

                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" required />

                <label for="id-especialidad">Especialidad:</label>
                <select id="id-especialidad" name="id-especialidad" required>
                    <option value="">Seleccionar Especialidad</option>
                    <?php while ($e = $especialidades->fetch_assoc()): ?>
                        <option value="<?= $e["ID_Especialidad"] ?>"><?= $e["Nombre"] ?></option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">Crear Odontólogo</button>
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