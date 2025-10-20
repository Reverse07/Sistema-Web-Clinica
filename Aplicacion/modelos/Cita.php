<?php

require_once __DIR__ . "/../../nucleo/BaseDatos.php";

class Cita
{
    private $id;
    private $paciente_id;
    private $doctor_id;
    private $fecha;
    private $estado;

    // 🧭 Constructor
    public function __construct($id = null, $paciente_id = null, $doctor_id = null, $fecha = null, $estado = null)
    {
        $this->id = $id;
        $this->paciente_id = $paciente_id;
        $this->doctor_id = $doctor_id;
        $this->fecha = $fecha;
        $this->estado = $estado;
    }

    // 🧾 Getters y Setters
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getPacienteId() { return $this->paciente_id; }
    public function setPacienteId($paciente_id) { $this->paciente_id = $paciente_id; }

    public function getDoctorId() { return $this->doctor_id; }
    public function setDoctorId($doctor_id) { $this->doctor_id = $doctor_id; }

    public function getFecha() { return $this->fecha; }
    public function setFecha($fecha) { $this->fecha = $fecha; }

    public function getEstado() { return $this->estado; }
    public function setEstado($estado) { $this->estado = $estado; }

    // 📥 Crear nueva cita
    public function crear()
    {
         $db = BaseDatos::pdo();
        $sql = "INSERT INTO citas (paciente_id, doctor_id, fecha, estado) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$this->paciente_id, $this->doctor_id, $this->fecha, $this->estado]);
    }

    // ✏️ Actualizar cita
    public function actualizar()
    {
         $db = BaseDatos::pdo();
        $sql = "UPDATE citas SET paciente_id = ?, doctor_id = ?, fecha = ?, estado = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$this->paciente_id, $this->doctor_id, $this->fecha, $this->estado, $this->id]);
    }

    // 🗑️ Eliminar cita
    public static function eliminar($id)
    {
         $db = BaseDatos::pdo();
        $sql = "DELETE FROM citas WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // 🔍 Obtener cita por ID
    public static function obtenerPorId($id)
    {
         $db = BaseDatos::pdo();
        $sql = "SELECT * FROM citas WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($fila) {
            return new Cita($fila['id'], $fila['paciente_id'], $fila['doctor_id'], $fila['fecha'], $fila['estado']);
        }
        return null;
    }

    // 📜 Obtener todas las citas
    public static function obtenerTodos()
    {
         $db = BaseDatos::pdo();
        $sql = "SELECT * FROM citas ORDER BY fecha DESC";
        $stmt = $db->query($sql);
        $resultados = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = new Cita($fila['id'], $fila['paciente_id'], $fila['doctor_id'], $fila['fecha'], $fila['estado']);
        }

        return $resultados;
    }

    // 🧍 Obtener citas por paciente
    public static function obtenerPorPaciente($paciente_id)
    {
         $db = BaseDatos::pdo();
        $sql = "SELECT * FROM citas WHERE paciente_id = ? ORDER BY fecha DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$paciente_id]);
        $resultados = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = new Cita($fila['id'], $fila['paciente_id'], $fila['doctor_id'], $fila['fecha'], $fila['estado']);
        }

        return $resultados;
    }

    // 🩺 Obtener citas por doctor
    public static function obtenerPorDoctor($doctor_id)
    {
         $db = BaseDatos::pdo();
        $sql = "SELECT * FROM citas WHERE doctor_id = ? ORDER BY fecha DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute([$doctor_id]);
        $resultados = [];

        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultados[] = new Cita($fila['id'], $fila['paciente_id'], $fila['doctor_id'], $fila['fecha'], $fila['estado']);
        }

        return $resultados;
    }

    public static function contarCitas() {
    $pdo = BaseDatos::pdo();
    $stmt = $pdo->query("SELECT COUNT(*) FROM citas");
    return $stmt->fetchColumn();
}

}
