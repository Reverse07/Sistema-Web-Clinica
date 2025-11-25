<?php

require_once __DIR__ . "/../../nucleo/BaseDatos.php";

class Historia
{
    private int $id = 0;
    private int $cita_id = 0;
    private int $doctor_id = 0;
    private int $paciente_id = 0;
    private string $motivo = '';
    private string $diagnostico = '';
    private string $indicaciones = '';
    private string $observaciones = '';
    private string $creada_en = '';

    // =============================
    // 🏗️ Constructor
    // =============================
    public function __construct(array $data = [])
    {
        $this->id = (int)($data['id'] ?? 0);
        $this->cita_id = (int)($data['cita_id'] ?? 0);
        $this->doctor_id = (int)($data['doctor_id'] ?? 0);
        $this->paciente_id = (int)($data['paciente_id'] ?? 0);

        $this->motivo = $data['motivo'] ?? '';
        $this->diagnostico = $data['diagnostico'] ?? '';
        $this->indicaciones = $data['indicaciones'] ?? '';
        $this->observaciones = $data['observaciones'] ?? '';

        $this->creada_en = $data['creada_en'] ?? date('Y-m-d H:i:s');
    }

    // =============================
    // 🧾 Getters y Setters
    // =============================

    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }

    public function getCitaId(): int { return $this->cita_id; }
    public function setCitaId(int $cita_id): void { $this->cita_id = $cita_id; }

    public function getDoctorId(): int { return $this->doctor_id; }
    public function setDoctorId(int $doctor_id): void { $this->doctor_id = $doctor_id; }

    public function getPacienteId(): int { return $this->paciente_id; }
    public function setPacienteId(int $paciente_id): void { $this->paciente_id = $paciente_id; }

    public function getMotivo(): string { return $this->motivo; }
    public function setMotivo(string $motivo): void { $this->motivo = $motivo; }

    public function getDiagnostico(): string { return $this->diagnostico; }
    public function setDiagnostico(string $diagnostico): void { $this->diagnostico = $diagnostico; }

    public function getIndicaciones(): string { return $this->indicaciones; }
    public function setIndicaciones(string $indicaciones): void { $this->indicaciones = $indicaciones; }

    public function getObservaciones(): string { return $this->observaciones; }
    public function setObservaciones(string $observaciones): void { $this->observaciones = $observaciones; }

    public function getCreadaEn(): string { return $this->creada_en; }
    public function setCreadaEn(string $creada_en): void { $this->creada_en = $creada_en; }
}
