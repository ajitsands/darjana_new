<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/TailoringUnit.php';

class AdminTailoringUnitController extends Controller {

    public function __construct() {
        $this->requireAuth();
    }

    public function index() {
        $unitModel = new TailoringUnit();
        $units = $unitModel->getAllUnits();

        $data = [
            'pageTitle' => 'Tailoring Units | Dar Jana Fashion',
            'units' => $units
        ];

        $this->render('admin/tailoring_units_index', $data, 'admin');
    }

    public function create() {
        $data = [
            'pageTitle' => 'Add Tailoring Unit | Dar Jana Fashion',
        ];

        $this->render('admin/tailoring_units_create', $data, 'admin');
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $unitModel = new TailoringUnit();
            
            $data = [
                'unit_name' => $_POST['unit_name'] ?? '',
                'contact_person' => $_POST['contact_person'] ?? '',
                'contact_number' => $_POST['contact_number'] ?? '',
                'email_id' => $_POST['email_id'] ?? '',
                'unique_unit_code' => trim($_POST['unique_unit_code'] ?? '')
            ];

            if (empty($data['unit_name']) || empty($data['unique_unit_code'])) {
                $_SESSION['admin_error'] = "Unit Name and Unique Code are required.";
                $this->redirect(BASE_URL . '/admin/tailoring-units/create');
                return;
            }

            try {
                $unitModel->createUnit($data);
                $this->logActivity('CREATE_TAILORING_UNIT', "Created tailoring unit: " . $data['unit_name']);
                $_SESSION['admin_success'] = "Tailoring Unit added successfully.";
                $this->redirect(BASE_URL . '/admin/tailoring-units');
            } catch (Exception $e) {
                $_SESSION['admin_error'] = "Error: " . $e->getMessage() . " (Perhaps the code is not unique?)";
                $this->redirect(BASE_URL . '/admin/tailoring-units/create');
            }
        }
    }

    public function edit($id) {
        $unitModel = new TailoringUnit();
        $unit = $unitModel->getUnitById($id);

        if (!$unit) {
            $this->redirect(BASE_URL . '/admin/tailoring-units');
            return;
        }

        $data = [
            'pageTitle' => 'Edit Tailoring Unit | Dar Jana Fashion',
            'unit' => $unit
        ];

        $this->render('admin/tailoring_units_edit', $data, 'admin');
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $unitModel = new TailoringUnit();
            
            $data = [
                'unit_name' => $_POST['unit_name'] ?? '',
                'contact_person' => $_POST['contact_person'] ?? '',
                'contact_number' => $_POST['contact_number'] ?? '',
                'email_id' => $_POST['email_id'] ?? '',
                'unique_unit_code' => trim($_POST['unique_unit_code'] ?? '')
            ];

            if (empty($data['unit_name']) || empty($data['unique_unit_code'])) {
                $_SESSION['admin_error'] = "Unit Name and Unique Code are required.";
                $this->redirect(BASE_URL . '/admin/tailoring-units/edit/' . $id);
                return;
            }

            try {
                $unitModel->updateUnit($id, $data);
                $this->logActivity('UPDATE_TAILORING_UNIT', "Updated tailoring unit ID: " . $id);
                $_SESSION['admin_success'] = "Tailoring Unit updated successfully.";
                $this->redirect(BASE_URL . '/admin/tailoring-units');
            } catch (Exception $e) {
                $_SESSION['admin_error'] = "Error: " . $e->getMessage();
                $this->redirect(BASE_URL . '/admin/tailoring-units/edit/' . $id);
            }
        }
    }

    public function delete($id) {
        $unitModel = new TailoringUnit();
        $unit = $unitModel->getUnitById($id);
        
        if ($unit) {
            $unitModel->deleteUnit($id);
            $this->logActivity('DELETE_TAILORING_UNIT', "Deleted tailoring unit: " . $unit['unit_name']);
            $_SESSION['admin_success'] = "Tailoring Unit deleted successfully.";
        }
        $this->redirect(BASE_URL . '/admin/tailoring-units');
    }
}
