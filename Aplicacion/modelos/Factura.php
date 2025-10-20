<?php
// =============================
// 💳 Modelo: Factura
// =============================
require_once __DIR__ . "/../../nucleo/BaseDatos.php";

class Factura
{
    private $id;
    private $paciente_id;
    private $cita_id;
    private $monto;
    private $estado;
    private $emitida_en;

    // =============================
    // 🏗️ Constructor
    // =============================
    public function __construct($id = null, $paciente_id = null, $cita_id = null, $monto = null, $estado = null, $emitida_en = null)
    {
        $this->id = $id;
        $this->paciente_id = $paciente_id;
        $this->cita_id = $cita_id;
        $this->monto = $monto;
        $this->estado = $estado;
        $this->emitida_en = $emitida_en;
    }

    // =============================
    // ✨ Getters
    // =============================
    public function getId() { return $this->id; }
    public function getPacienteId() { return $this->paciente_id; }
    public function getCitaId() { return $this->cita_id; }
    public function getMonto() { return $this->monto; }
    public function getEstado() { return $this->estado; }
    public function getEmitidaEn() { return $this->emitida_en; }

    // =============================
    // 🛠️ Setters
    // =============================
    public function setId($id) { $this->id = $id; }
    public function setPacienteId($paciente_id) { $this->paciente_id = $paciente_id; }
    public function setCitaId($cita_id) { $this->cita_id = $cita_id; }
    public function setMonto($monto) { $this->monto = $monto; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setEmitidaEn($emitida_en) { $this->emitida_en = $emitida_en; }

    // =============================
    // 🧩 Métodos de Base de Datos
    // =============================

    public static function todos()
    {
        try {
            $db = BaseDatos::pdo();
            $stmt = $db->query("SELECT * FROM facturas ORDER BY emitida_en DESC");
            $facturas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $facturas[] = new Factura(
                    $row['id'],
                    $row['paciente_id'],
                    $row['cita_id'],
                    $row['monto'],
                    $row['estado'],
                    $row['emitida_en']
                );
            }
            return $facturas;
        } catch (PDOException $e) {
            error_log("Factura::todos error: " . $e->getMessage());
            return [];
        }
    }

    public static function buscarPorId($id)
    {
        try {
            $db = BaseDatos::pdo();
            $stmt = $db->prepare("SELECT * FROM facturas WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? new Factura(
                $row['id'],
                $row['paciente_id'],
                $row['cita_id'],
                $row['monto'],
                $row['estado'],
                $row['emitida_en']
            ) : null;
        } catch (PDOException $e) {
            error_log("Factura::buscarPorId error: " . $e->getMessage());
            return null;
        }
    }

    public static function obtenerPorPaciente($paciente_id)
    {
        try {
            $db = BaseDatos::pdo();
            $stmt = $db->prepare("SELECT * FROM facturas WHERE paciente_id = :pid ORDER BY emitida_en DESC");
            $stmt->execute([':pid' => $paciente_id]);
            $facturas = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $facturas[] = new Factura(
                    $row['id'],
                    $row['paciente_id'],
                    $row['cita_id'],
                    $row['monto'],
                    $row['estado'],
                    $row['emitida_en']
                );
            }
            return $facturas;
        } catch (PDOException $e) {
            error_log("Factura::obtenerPorPaciente error: " . $e->getMessage());
            return [];
        }
    }

    public static function crear($paciente_id, $cita_id, $monto, $estado = 'Pendiente', $emitida_en = null)
    {
        try {
            $db = BaseDatos::pdo();
            $emitida_en = $emitida_en ?? date('Y-m-d H:i:s');
            $stmt = $db->prepare(
                "INSERT INTO facturas (paciente_id, cita_id, monto, estado, emitida_en)
                 VALUES (:paciente_id, :cita_id, :monto, :estado, :emitida_en)"
            );
            $ok = $stmt->execute([
                ':paciente_id' => $paciente_id,
                ':cita_id'     => $cita_id,
                ':monto'       => $monto,
                ':estado'      => $estado,
                ':emitida_en'  => $emitida_en
            ]);
            return $ok ? (int)$db->lastInsertId() : 0;
        } catch (PDOException $e) {
            error_log("Factura::crear error: " . $e->getMessage());
            return 0;
        }
    }

    public static function actualizar($id, $paciente_id, $cita_id, $monto, $estado, $emitida_en = null)
    {
        try {
            $db = BaseDatos::pdo();
            $sql = "UPDATE facturas SET paciente_id = :paciente_id, cita_id = :cita_id, monto = :monto, estado = :estado";
            $params = [
                ':paciente_id' => $paciente_id,
                ':cita_id'     => $cita_id,
                ':monto'       => $monto,
                ':estado'      => $estado,
                ':id'          => $id
            ];
            if ($emitida_en !== null) {
                $sql .= ", emitida_en = :emitida_en";
                $params[':emitida_en'] = $emitida_en;
            }
            $sql .= " WHERE id = :id";
            $stmt = $db->prepare($sql);
            return (bool)$stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Factura::actualizar error: " . $e->getMessage());
            return false;
        }
    }

    public function guardar()
    {
        try {
            $db = BaseDatos::pdo();
            if ($this->id) {
                $stmt = $db->prepare(
                    "UPDATE facturas SET paciente_id = ?, cita_id = ?, monto = ?, estado = ?, emitida_en = ? WHERE id = ?"
                );
                return (bool)$stmt->execute([
                    $this->paciente_id,
                    $this->cita_id,
                    $this->monto,
                    $this->estado,
                    $this->emitida_en,
                    $this->id
                ]);
            } else {
                $stmt = $db->prepare(
                    "INSERT INTO facturas (paciente_id, cita_id, monto, estado, emitida_en) VALUES (?, ?, ?, ?, ?)"
                );
                $ok = $stmt->execute([
                    $this->paciente_id,
                    $this->cita_id,
                    $this->monto,
                    $this->estado,
                    $this->emitida_en
                ]);
                if ($ok) $this->id = (int)$db->lastInsertId();
                return $ok;
            }
        } catch (PDOException $e) {
            error_log("Factura::guardar error: " . $e->getMessage());
            return false;
        }
    }

    public static function eliminar($id)
    {
        try {
            $db = BaseDatos::pdo();
            $stmt = $db->prepare("DELETE FROM facturas WHERE id = ?");
            return (bool)$stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Factura::eliminar error: " . $e->getMessage());
            return false;
        }
    }

    public static function contarFacturas()
    {
        $db = BaseDatos::pdo();
        $stmt = $db->query("SELECT COUNT(*) FROM facturas");
        return (int)$stmt->fetchColumn();
    }

    public static function sumarRecaudado()
    {
        $db = BaseDatos::pdo();
        $stmt = $db->query("SELECT SUM(monto) FROM facturas");
        return (float)($stmt->fetchColumn() ?: 0);
    }

    public static function obtenerUltimas($limite = 10)
    {
        $db = BaseDatos::pdo();
        $stmt = $db->prepare("SELECT * FROM facturas ORDER BY emitida_en DESC LIMIT ?");
        $stmt->bindValue(1, (int)$limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
