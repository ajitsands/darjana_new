<?php
require_once __DIR__ . '/../../core/Model.php';

class TailoringUnit extends Model {

    public function getAllUnits() {
        return $this->fetchAll("SELECT * FROM tailoring_units ORDER BY id DESC");
    }

    public function getUnitById($id) {
        return $this->fetchOne("SELECT * FROM tailoring_units WHERE id = ?", [$id]);
    }

    public function createUnit($data) {
        $sql = "INSERT INTO tailoring_units (unit_name, contact_person, contact_number, email_id, unique_unit_code) VALUES (?, ?, ?, ?, ?)";
        return $this->query($sql, [
            $data['unit_name'],
            $data['contact_person'] ?? null,
            $data['contact_number'] ?? null,
            $data['email_id'] ?? null,
            $data['unique_unit_code']
        ]);
    }

    public function updateUnit($id, $data) {
        $sql = "UPDATE tailoring_units SET unit_name = ?, contact_person = ?, contact_number = ?, email_id = ?, unique_unit_code = ? WHERE id = ?";
        return $this->query($sql, [
            $data['unit_name'],
            $data['contact_person'] ?? null,
            $data['contact_number'] ?? null,
            $data['email_id'] ?? null,
            $data['unique_unit_code'],
            $id
        ]);
    }

    public function deleteUnit($id) {
        return $this->query("DELETE FROM tailoring_units WHERE id = ?", [$id]);
    }
}
