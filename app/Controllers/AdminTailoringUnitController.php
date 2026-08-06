<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/TailoringUnit.php';

class AdminTailoringUnitController extends Controller {

    private function requireAuth() {
        if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
            $this->redirect(BASE_URL . '/admin/login');
        }
    }

    private function logActivity($actionType, $description) {
        if (isset($_SESSION['user_id'])) {
            $db = Database::getInstance();
            $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action_type, description) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $actionType, $description]);
        }
    }

    public function index() {
        $this->requireAuth();
        $unitModel = new TailoringUnit();
        $units = $unitModel->getAllUnits();

        $data = [
            'pageTitle' => 'Tailoring Units | Dar Jana Fashion',
            'units' => $units
        ];

        $this->render('admin/tailoring_units_index', $data, 'admin');
    }

    public function create() {
        $this->requireAuth();
        $data = [
            'pageTitle' => 'Add Tailoring Unit | Dar Jana Fashion',
        ];

        $this->render('admin/tailoring_units_create', $data, 'admin');
    }

    public function store() {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $unitModel = new TailoringUnit();
            
            $data = [
                'unit_name' => $_POST['unit_name'] ?? '',
                'contact_person' => $_POST['contact_person'] ?? '',
                'contact_number' => $_POST['contact_number'] ?? '',
                'email_id' => $_POST['email_id'] ?? '',
                'unique_unit_code' => trim($_POST['unique_unit_code'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if (empty($data['unit_name']) || empty($data['unique_unit_code'])) {
                $_SESSION['admin_error'] = "Unit Name and Unique Code are required.";
                $this->redirect(BASE_URL . '/admin/tailoring-units/create');
                return;
            }

            try {
                if ($unitModel->checkCodeExists($data['unique_unit_code'])) {
                    $_SESSION['admin_error'] = "The Unique Unit Code '{$data['unique_unit_code']}' already exists. Please choose a different code.";
                    $this->redirect(BASE_URL . '/admin/tailoring-units/create');
                    return;
                }
                
                $unitModel->createUnit($data);
                $_SESSION['admin_success'] = "Tailoring Unit added successfully.";
                $this->redirect(BASE_URL . '/admin/tailoring-units');
            } catch (Exception $e) {
                $_SESSION['admin_error'] = "Error: " . $e->getMessage();
                $this->redirect(BASE_URL . '/admin/tailoring-units/create');
            }
        }
    }

    public function edit($id) {
        $this->requireAuth();
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
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $unitModel = new TailoringUnit();
            
            $data = [
                'unit_name' => $_POST['unit_name'] ?? '',
                'contact_person' => $_POST['contact_person'] ?? '',
                'contact_number' => $_POST['contact_number'] ?? '',
                'email_id' => $_POST['email_id'] ?? '',
                'unique_unit_code' => trim($_POST['unique_unit_code'] ?? ''),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];

            if (empty($data['unit_name'])) {
                $_SESSION['admin_error'] = "Unit Name is required.";
                $this->redirect(BASE_URL . '/admin/tailoring-units/edit/' . $id);
                return;
            }

            try {
                $unitModel->updateUnit($id, $data);
                $_SESSION['admin_success'] = "Tailoring Unit updated successfully.";
                $this->redirect(BASE_URL . '/admin/tailoring-units');
            } catch (Exception $e) {
                $_SESSION['admin_error'] = "Error: " . $e->getMessage();
                $this->redirect(BASE_URL . '/admin/tailoring-units/edit/' . $id);
            }
        }
    }

    public function delete($id) {
        $this->requireAuth();
        $unitModel = new TailoringUnit();
        $unit = $unitModel->getUnitById($id);
        if ($unit) {
            $unitModel->deleteUnit($id);
            $this->logActivity('DELETE_TAILORING_UNIT', "Deleted Tailoring Unit: {$unit['unit_name']} ({$unit['unique_unit_code']})");
            $_SESSION['admin_success'] = "Tailoring Unit '{$unit['unit_name']}' deleted successfully.";
        }
        $this->redirect(BASE_URL . '/admin/tailoring-units');
    }

    public function processingJobs() {
        $this->requireAuth();
        require_once __DIR__ . '/../Models/Order.php';
        $orderModel = new Order();
        
        $statusFilter = $_GET['status'] ?? null;
        $startDate = $_GET['start_date'] ?? null;
        $endDate = $_GET['end_date'] ?? null;
        
        // Pass filters to the model
        $assignments = $orderModel->getAllAssignments($statusFilter, $startDate, $endDate);
        
        $data = [
            'pageTitle' => 'Processing Job Orders | Dar Jana Fashion',
            'assignments' => $assignments,
            'statusFilter' => $statusFilter,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
        
        $this->render('admin/processing_jobs', $data, 'admin');
    }

    public function completeJob($id) {
        $this->requireAuth();
        require_once __DIR__ . '/../Models/Order.php';
        $orderModel = new Order();
        
        $assignment = $orderModel->getAssignmentById($id);
        if ($assignment) {
            $orderModel->markAssignmentCompleted($id);
            $this->logActivity('COMPLETE_JOB', "Marked job order assignment PR: {$assignment['process_number']} as Completed.");
            $_SESSION['admin_success'] = "Job marked as Completed successfully.";
        }
        
        $this->redirect(BASE_URL . '/admin/tailoring-units/processing-jobs');
    }
    
    public function checkCode() {
        $this->requireAuth();
        header('Content-Type: application/json');
        
        $code = $_GET['code'] ?? '';
        $excludeId = $_GET['exclude_id'] ?? null;
        
        if (empty($code)) {
            echo json_encode(['exists' => false]);
            return;
        }
        
        $unitModel = new TailoringUnit();
        $exists = $unitModel->checkCodeExists($code, $excludeId);
        
        echo json_encode(['exists' => $exists]);
    }
}
