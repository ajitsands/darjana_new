<?php
require_once __DIR__ . '/../../core/Model.php';

class TailoringUnit extends Model {

    public function getAllUnits() {
        return $this->fetchAll("SELECT * FROM tailoring_units ORDER BY id DESC");
    }

    public function getUnitById($id) {
        return $this->fetchOne("SELECT * FROM tailoring_units WHERE id = ?", [$id]);
    }

    public function getActiveUnits() {
        return $this->fetchAll("SELECT * FROM tailoring_units WHERE is_active = 1 ORDER BY unit_name ASC");
    }

    public function createUnit($data) {
        $sql = "INSERT INTO tailoring_units (unit_name, contact_person, contact_number, email_id, unique_unit_code, is_active) VALUES (?, ?, ?, ?, ?, ?)";
        return $this->query($sql, [
            $data['unit_name'],
            $data['contact_person'] ?? null,
            $data['contact_number'] ?? null,
            $data['email_id'] ?? null,
            $data['unique_unit_code'],
            isset($data['is_active']) ? (int)$data['is_active'] : 1
        ]);
    }

    public function updateUnit($id, $data) {
        $sql = "UPDATE tailoring_units SET unit_name = ?, contact_person = ?, contact_number = ?, email_id = ?, is_active = ? WHERE id = ?";
        return $this->query($sql, [
            $data['unit_name'],
            $data['contact_person'] ?? null,
            $data['contact_number'] ?? null,
            $data['email_id'] ?? null,
            isset($data['is_active']) ? (int)$data['is_active'] : 1,
            $id
        ]);
    }

    public function checkCodeExists($code, $excludeId = null) {
        if ($excludeId) {
            $result = $this->fetchOne("SELECT id FROM tailoring_units WHERE unique_unit_code = ? AND id != ?", [$code, $excludeId]);
        } else {
            $result = $this->fetchOne("SELECT id FROM tailoring_units WHERE unique_unit_code = ?", [$code]);
        }
        return $result ? true : false;
    }

    public function deleteUnit($id) {
        return $this->query("DELETE FROM tailoring_units WHERE id = ?", [$id]);
    }
}
