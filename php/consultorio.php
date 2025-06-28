<?php
$conn = new mysqli("localhost", "root", "", "odontologia");
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Insertar nueva sede
if (isset($_POST['nombre-sede'])) {
    $nombre = $_POST["nombre-sede"];
    $ciudad = $_POST["ciudad"];
    $calle = $_POST["calle"];
    $numero = $_POST["numero"];

    // Validación solo letras
    if (!preg_match("/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/", $nombre) || !preg_match("/^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/", $ciudad)) {
        echo "<script>alert('Nombre de sede y ciudad solo pueden contener letras.');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO Sede (Nombre, Ciudad, Calle, Numero) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $nombre, $ciudad, $calle, $numero);
        $stmt->execute();
        $stmt->close();
    }
}

// Insertar nuevo consultorio
if (isset($_POST['numero-consultorio'])) {
    $numero = $_POST["numero-consultorio"];
    $id_sede = $_POST["id-sede-consultorio"];
    $stmt = $conn->prepare("INSERT INTO Consultorio (Numero, ID_Sede) VALUES (?, ?)");
    $stmt->bind_param("ii", $numero, $id_sede);
    $stmt->execute();
    $stmt->close();
}

// Obtener sedes
$sedes = $conn->query("SELECT ID_Sede, Nombre, Ciudad, Calle, Numero FROM Sede");
$sedesParaSelect = $conn->query("SELECT ID_Sede, Nombre FROM Sede");

// Obtener consultorios
$consultorios = $conn->query("
    SELECT c.ID_Consultorio, c.Numero, s.Nombre AS Sede 
    FROM Consultorio c 
    JOIN Sede s ON c.ID_Sede = s.ID_Sede
");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sedes y Consultorios - Clínica Odontológica</title>
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

    <section class="tratamientos">
        <div class="container">

            <h3>Listado de Sedes</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Sede</th>
                        <th>Nombre</th>
                        <th>Ciudad</th>
                        <th>Calle</th>
                        <th>Número</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($sede = $sedes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $sede["ID_Sede"] ?></td>
                            <td><?= $sede["Nombre"] ?></td>
                            <td><?= $sede["Ciudad"] ?></td>
                            <td><?= $sede["Calle"] ?></td>
                            <td><?= $sede["Numero"] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Sedes</h2>
            <form method="POST" action="" id="form-sede">
                <fieldset>
                    <legend>Registrar Sede</legend>
                    <label for="nombre-sede">Nombre:</label>
                    <input type="text" id="nombre-sede" name="nombre-sede" required maxlength="50" />

                    <label for="ciudad">Ciudad:</label>
                    <input type="text" id="ciudad" name="ciudad" required maxlength="50" />

                    <label for="calle">Calle:</label>
                    <input type="text" id="calle" name="calle" required maxlength="100" />

                    <label for="numero">Número:</label>
                    <input type="number" id="numero" name="numero" min="1" required />

                    <button type="submit">Crear Sede</button>
                </fieldset>
            </form>

            <h3>Listado de Consultorios</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID Consultorio</th>
                        <th>Número</th>
                        <th>Sede</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($fila = $consultorios->fetch_assoc()): ?>
                        <tr>
                            <td><?= $fila["ID_Consultorio"] ?></td>
                            <td><?= $fila["Numero"] ?></td>
                            <td><?= $fila["Sede"] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h2>Consultorios</h2>
            <form method="POST" action="">
                <fieldset>
                    <legend>Registrar Consultorio</legend>

                    <label for="numero-consultorio">Número:</label>
                    <input type="number" id="numero-consultorio" name="numero-consultorio" min="1" required />

                    <label for="id-sede-consultorio">ID Sede:</label>
                    <select id="id-sede-consultorio" name="id-sede-consultorio" required>
                        <option value="" disabled selected>Seleccione una sede</option>
                        <?php while ($sede = $sedesParaSelect->fetch_assoc()): ?>
                            <option value="<?= $sede['ID_Sede'] ?>"><?= $sede['Nombre'] ?></option>
                        <?php endwhile; ?>
                    </select>

                    <button type="submit">Crear Consultorio</button>
                </fieldset>
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

    <!-- Validación JS: solo letras en nombre y ciudad -->
    <script>
        document.getElementById('form-sede').addEventListener('submit', function(e) {
            const nombre = document.getElementById('nombre-sede').value.trim();
            const ciudad = document.getElementById('ciudad').value.trim();
            const soloLetras = /^[A-Za-zÁÉÍÓÚáéíóúñÑ\s]+$/;

            if (!soloLetras.test(nombre)) {
                alert('El nombre de la sede debe contener solo letras.');
                e.preventDefault();
                return;
            }

            if (!soloLetras.test(ciudad)) {
                alert('La ciudad debe contener solo letras.');
                e.preventDefault();
                return;
            }
        });
    </script>
</body>

</html>

<?php $conn->close(); ?>