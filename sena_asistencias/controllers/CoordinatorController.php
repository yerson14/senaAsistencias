<?php
session_start(); // Iniciar la sesión
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/AmbienteModel.php'; 
require_once __DIR__ . '/../models/CentroModel.php';
require_once __DIR__ . '/../models/InstructorModel.php';
require_once __DIR__ . '/../models/ProgramaFormacionModel.php';
require_once __DIR__ . '/../models/FichaModel.php';
require_once __DIR__ . '/../models/AprendizModel.php';

class CoordinadorController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Crear un ambiente
    // Método para crear un ambiente
public function crearAmbiente($nombre, $centro_id)
{
    try {
        // Validar que los campos no estén vacíos
        if (empty($nombre) || empty($centro_id)) {
            throw new Exception("Todos los campos son requeridos.");
        }

        // Validar que el centro_id sea un número
        if (!is_numeric($centro_id)) {
            throw new Exception("El ID del centro debe ser un número.");
        }

        // Usar el modelo AmbienteModel para crear el ambiente
        $ambienteModel = new AmbienteModel($this->db);
        if ($ambienteModel->crearAmbiente($nombre, $centro_id)) {
            $_SESSION['success'] = "Ambiente creado exitosamente.";
        } else {
            throw new Exception("Error al crear el ambiente.");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    // Redirigir a la página de gestión de ambientes
    header("Location: /senaAsistencias/sena_asistencias/view/coordinator/index.php");
    exit();
}

// Método para editar un ambiente
public function editarAmbiente($id, $nombre, $centro_id)
{
    try {
        // Validar que los campos no estén vacíos
        if (empty($nombre) || empty($centro_id)) {
            throw new Exception("Todos los campos son requeridos.");
        }

        // Validar que el centro_id sea un número
        if (!is_numeric($centro_id)) {
            throw new Exception("El ID del centro debe ser un número.");
        }

        // Usar el modelo AmbienteModel para editar el ambiente
        $ambienteModel = new AmbienteModel($this->db);
        if ($ambienteModel->editarAmbiente($id, $nombre, $centro_id)) {
            $_SESSION['success'] = "Ambiente actualizado exitosamente.";
        } else {
            throw new Exception("Error al actualizar el ambiente.");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    // Redirigir a la página de gestión de ambientes
    header("Location: /senaAsistencias/sena_asistencias/view/coordinator/index.php");
    exit();
}

// Método para eliminar un ambiente
public function eliminarAmbiente($id)
{
    try {
        // Validar que el ID sea un número
        if (!is_numeric($id)) {
            throw new Exception("El ID del ambiente debe ser un número.");
        }

        // Usar el modelo AmbienteModel para eliminar el ambiente
        $ambienteModel = new AmbienteModel($this->db);
        if ($ambienteModel->eliminarAmbiente($id)) {
            $_SESSION['success'] = "Ambiente eliminado exitosamente.";
        } else {
            throw new Exception("Error al eliminar el ambiente.");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    // Redirigir a la página de gestión de ambientes
    header("Location: /senaAsistencias/sena_asistencias/view/coordinator/index.php");
    exit();
}

    // Método para crear un programa
    public function createProgram($nombre, $centro_id)
    {
        try {
            // Validar que los campos no estén vacíos
            if (empty($nombre) || empty($centro_id)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            // Validar que el centro_id sea un número
            if (!is_numeric($centro_id)) {
                throw new Exception("El ID del centro debe ser un número.");
            }

            // Usar el modelo ProgramaFormacionModel para crear el programa
            $programaFormacionModel = new ProgramaFormacionModel($this->db);
            if ($programaFormacionModel->crearPrograma($nombre, $centro_id)) {
                $_SESSION['success'] = "Programa creado exitosamente.";
                header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_program.php");
                exit();
            } else {
                throw new Exception("Error al crear el programa.");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_program.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_program.php");
            exit();
        }
    }

    // Método para crear una ficha
    public function crearFicha($numero, $programa_id)
{
    try {
        $fichaModel = new FichaModel($this->db);
        if ($fichaModel->crearFicha($numero, $programa_id)) {
            $_SESSION['success'] = "Ficha creada exitosamente.";
        } else {
            throw new Exception("Error al crear la ficha.");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    // Redirigir a la página de creación de fichas
    header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_ficha.php");
    exit();
}

    // Obtener todas las fichas
    public function obtenerFichas()
    {
        try {
            $stmt = $this->db->prepare("SELECT f.*, p.nombre as programa_nombre FROM fichas f JOIN programas_formacion p ON f.programa_formacion_id = p.id");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener las fichas: " . $e->getMessage());
        }
    }

    // Obtener una ficha por su ID
    public function obtenerFichaPorId($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM fichas WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error al obtener la ficha: " . $e->getMessage());
        }
    }

    // Editar una ficha
    public function editarFicha($id, $numero, $programa_id)
    {
        try {
            $fichaModel = new FichaModel($this->db);
            if ($fichaModel->editarFicha($id, $numero, $programa_id)) {
                $_SESSION['success'] = "Ficha actualizada exitosamente.";
            } else {
                throw new Exception("Error al actualizar la ficha.");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
    
        // Redirigir a la página de creación de fichas
        header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_ficha.php");
        exit();
    }

    // Eliminar una ficha
    public function eliminarFicha($id)
{
    try {
        $fichaModel = new FichaModel($this->db);
        if ($fichaModel->eliminarFicha($id)) {
            $_SESSION['success'] = "Ficha eliminada exitosamente.";
        } else {
            throw new Exception("Error al eliminar la ficha.");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    // Redirigir a la página de creación de fichas
    header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_ficha.php");
    exit();
}
    // Crear un instructor
   // Crear un instructor
public function crearInstructor($nombre, $correo, $numero_identificacion, $centro_id) {
    try {
        if (empty($nombre) || empty($correo) || empty($numero_identificacion) || empty($centro_id)) {
            throw new Exception("Todos los campos son requeridos.");
        }

        $instructorModel = new InstructorModel($this->db);
        if ($instructorModel->crearInstructor($nombre, $correo, $numero_identificacion, $centro_id)) {
            $_SESSION['success'] = "Instructor creado exitosamente.";
        } else {
            throw new Exception("Error al crear el instructor.");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_instructor.php");
    exit();
}

// Actualizar un instructor
public function actualizarInstructor($id, $nombre, $correo, $numero_identificacion, $centro_id) {
    try {
        if (empty($nombre) || empty($correo) || empty($numero_identificacion) || empty($centro_id)) {
            throw new Exception("Todos los campos son requeridos.");
        }

        $instructorModel = new InstructorModel($this->db);
        if ($instructorModel->actualizarInstructor($id, $nombre, $correo, $numero_identificacion, $centro_id)) {
            $_SESSION['success'] = "Instructor actualizado exitosamente.";
        } else {
            throw new Exception("Error al actualizar el instructor.");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_instructor.php");
    exit();
}

// Eliminar un instructor
public function eliminarInstructor($id) {
    try {
        $instructorModel = new InstructorModel($this->db);
        if ($instructorModel->eliminarInstructor($id)) {
            $_SESSION['success'] = "Instructor eliminado exitosamente.";
        } else {
            throw new Exception("Error al eliminar el instructor.");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_instructor.php");
    exit();
}


    // Obtener todos los centros
    public function obtenerCentros()
    {
        $centroModel = new CentroModel($this->db);
        return $centroModel->obtenerCentros();
    }
    // Método para eliminar un programa
public function deleteProgram($id)
{
    try {
        // Validar que el ID sea un número
        if (!is_numeric($id)) {
            throw new Exception("El ID del programa debe ser un número.");
        }

        // Usar el modelo ProgramaFormacionModel para eliminar el programa
        $programaModel = new ProgramaFormacionModel($this->db);
        if ($programaModel->eliminarPrograma($id)) {
            $_SESSION['success'] = "Programa eliminado exitosamente.";
        } else {
            throw new Exception("Error al eliminar el programa.");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }

    // Redirigir a la página principal
    header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_program.php");
    exit();
}
    // Método para actualizar un programa
// Método para actualizar un programa
public function updateProgram($id, $nombre, $centro_id)
{
    try {
        // Validar que los campos no estén vacíos
        if (empty($nombre) || empty($centro_id)) {
            throw new Exception("Todos los campos son requeridos.");
        }

        // Validar que el centro_id sea un número
        if (!is_numeric($centro_id)) {
            throw new Exception("El ID del centro debe ser un número.");
        }

        // Usar el modelo ProgramaFormacionModel para actualizar el programa
        $programaModel = new ProgramaFormacionModel($this->db);
        if ($programaModel->editarPrograma($id, $nombre, $centro_id)) {
            $_SESSION['success'] = "Programa actualizado exitosamente.";
            header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_program.php");
            exit();
        } else {
            throw new Exception("Error al actualizar el programa.");
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
        header("Location: /senaAsistencias/sena_asistencias/view/coordinator/edit_program.php?id=" . $id);
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: /senaAsistencias/sena_asistencias/view/coordinator/edit_program.php?id=" . $id);
        exit();
    }
}
    // Método para crear un aprendiz
    public function crearAprendiz($nombre, $numero_identificacion, $ficha_id, $centro_id) {
        try {
            if (empty($nombre) || empty($numero_identificacion) || empty($ficha_id) || empty($centro_id)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            $aprendizModel = new AprendizModel($this->db);
            if ($aprendizModel->crearAprendiz($nombre, $numero_identificacion, $ficha_id, $centro_id)) {
                $_SESSION['success'] = "Aprendiz creado exitosamente.";
            } else {
                throw new Exception("Error al crear el aprendiz.");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_aprendices.php");
        exit();
    }

    // Método para actualizar un aprendiz
    public function actualizarAprendiz($id, $nombre, $numero_identificacion, $ficha_id, $centro_id) {
        try {
            if (empty($nombre) || empty($numero_identificacion) || empty($ficha_id) || empty($centro_id)) {
                throw new Exception("Todos los campos son requeridos.");
            }

            $aprendizModel = new AprendizModel($this->db);
            if ($aprendizModel->actualizarAprendiz($id, $nombre, $numero_identificacion, $ficha_id, $centro_id)) {
                $_SESSION['success'] = "Aprendiz actualizado exitosamente.";
            } else {
                throw new Exception("Error al actualizar el aprendiz.");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_aprendices.php");
        exit();
    }

    // Método para eliminar un aprendiz
    public function eliminarAprendiz($id) {
        try {
            $aprendizModel = new AprendizModel($this->db);
            if ($aprendizModel->eliminarAprendiz($id)) {
                $_SESSION['success'] = "Aprendiz eliminado exitosamente.";
            } else {
                throw new Exception("Error al eliminar el aprendiz.");
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error de base de datos: " . $e->getMessage();
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header("Location: /senaAsistencias/sena_asistencias/view/coordinator/create_aprendices.php");
        exit();
    }


// Eliminar un aprendiz

}

// Manejar las acciones del controlador
// Manejar las acciones del controlador
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $coordinadorController = new CoordinadorController();

    // Acción para crear un ambiente
    if ($action === 'create_ambiente' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $centro_id = $_POST['centro_id'];
        $coordinadorController->crearAmbiente($nombre, $centro_id);
    }

    // Acción para editar un ambiente
    if ($action === 'edit_ambiente' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_GET['id'];
        $nombre = $_POST['nombre'];
        $centro_id = $_POST['centro_id'];
        $coordinadorController->editarAmbiente($id, $nombre, $centro_id);
    }

    // Acción para eliminar un ambiente
    if ($action === 'delete_ambiente' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $coordinadorController->eliminarAmbiente($id);
    }

    // Acción para crear un programa
    if ($action === 'create_program' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $centro_id = $_POST['centro_id'];
        $coordinadorController->createProgram($nombre, $centro_id);
    }

    // Acción para editar un programa
    if ($action === 'update_program' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_GET['id'];
        $nombre = $_POST['nombre'];
        $centro_id = $_POST['centro_id'];
        $coordinadorController->updateProgram($id, $nombre, $centro_id);
    }

    // Acción para eliminar un programa
    if ($action === 'delete_program' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $coordinadorController->deleteProgram($id);
    }

    // Acción para crear una ficha
    // Acción para crear una ficha
    if ($action === 'create_ficha' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $numero = $_POST['numero'];
        $programa_id = $_POST['programa_id'];

        // Llamar al método crearFicha
        $coordinadorController->crearFicha($numero, $programa_id);
    }

    // Acción para editar una ficha
    if ($action === 'edit_ficha' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_GET['id']; // Obtener el ID de la ficha desde la URL
        $numero = $_POST['numero'];
        $programa_id = $_POST['programa_id'];

        // Llamar al método editarFicha
        $coordinadorController->editarFicha($id, $numero, $programa_id);
    }


    // Acción para eliminar una ficha
    // Acción para eliminar una ficha
    if ($action === 'delete_ficha' && isset($_GET['id'])) {
        $id = $_GET['id']; // Obtener el ID de la ficha desde la URL

        // Llamar al método eliminarFicha
        $coordinadorController->eliminarFicha($id);
    }

       // Acción para crear un instructor
       if ($action === 'create_instructor' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $numero_identificacion = $_POST['numero_identificacion'];
        $centro_id = $_POST['centro_id'];
        $coordinadorController->crearInstructor($nombre, $correo, $numero_identificacion, $centro_id);
    }

    // Acción para actualizar un instructor
    if ($action === 'update_instructor' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_GET['id'];
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $numero_identificacion = $_POST['numero_identificacion'];
        $centro_id = $_POST['centro_id'];
        $coordinadorController->actualizarInstructor($id, $nombre, $correo, $numero_identificacion, $centro_id);
    }

    // Acción para eliminar un instructor
    if ($action === 'delete_instructor' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $coordinadorController->eliminarInstructor($id);
    }
        // Acción para crear un aprendiz
        if ($action === 'create_aprendiz' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'];
            $numero_identificacion = $_POST['numero_identificacion'];
            $ficha_id = $_POST['ficha_id'];
            $centro_id = $_POST['centro_id']; // Obtener el centro_id del formulario
    
            // Crear el aprendiz
            $coordinadorController->crearAprendiz($nombre, $numero_identificacion, $ficha_id, $centro_id);
        }
    
        // Acción para actualizar un aprendiz
        if ($action === 'update_aprendiz' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_GET['id'];
            $nombre = $_POST['nombre'];
            $numero_identificacion = $_POST['numero_identificacion'];
            $ficha_id = $_POST['ficha_id'];
            $centro_id = $_POST['centro_id']; // Obtener el centro_id del formulario
    
            // Actualizar el aprendiz
            $coordinadorController->actualizarAprendiz($id, $nombre, $numero_identificacion, $ficha_id, $centro_id);
        }
    
        // Acción para eliminar un aprendiz
        if ($action === 'delete_aprendiz' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $coordinadorController->eliminarAprendiz($id);
        }
}