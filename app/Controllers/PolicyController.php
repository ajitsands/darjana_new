<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../Models/Setting.php';
require_once __DIR__ . '/../Models/Category.php';

class PolicyController extends Controller {
    public function index($tab = 'shipping') {
        $settingModel = new Setting();
        $categoryModel = new Category();

        $settings = $settingModel->getAll();
        $categories = $categoryModel->getAllActive();

        $allowedTabs = ['shipping', 'returns', 'terms', 'privacy'];
        $activeTab = strtolower(trim($tab));
        if (!in_array($activeTab, $allowedTabs)) {
            $activeTab = 'shipping';
        }

        $tabTitles = [
            'shipping' => 'Shipping & GCC Delivery',
            'returns'  => 'Returns & Exchanges',
            'terms'    => 'Terms & Conditions',
            'privacy'  => 'Privacy Policy'
        ];

        $data = [
            'pageTitle' => $tabTitles[$activeTab] . ' | Dar Jana Fashion',
            'activeTab' => $activeTab,
            'settings'  => $settings,
            'headerCategories' => $categories
        ];

        $this->render('policies/index', $data);
    }
}
