<?php
// app/controllers/UserController.php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Registration.php';

class UserController extends Controller
{
    protected function renderView(string $view, array $vars = []): void {
        $base = dirname(__DIR__) . '/views';
        extract($vars, EXTR_SKIP);
        include $base . '/layouts/header.php';
        include $base . '/' . ltrim($view, '/');
        include $base . '/layouts/footer.php';
    }

    public function profile(): void {
        Auth::requireLogin();

        $userModel = new User();
        $regModel  = new Registration();

        $user    = $userModel->findById(Auth::id());
        $tickets = $regModel->listByUser(Auth::id(), 50);

        $this->renderView('user/profile.php', [
            'user'    => $user,
            'tickets' => $tickets
        ]);
    }

    public function myEvents(): void {
        Auth::requireLogin();

        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 8;
        $filters = [
            'q'      => trim($_GET['q'] ?? ''),
            'status' => trim($_GET['status'] ?? '')
        ];

        $event   = new Event();
        $result  = $event->listByCreator(Auth::id(), $filters, $page, $perPage);

        $totalPages = (int)ceil(max(1, $result['total']) / $perPage);

        $this->renderView('user/my-events.php', [
            'events'     => $result['data'],
            'page'       => $page,
            'totalPages' => $totalPages,
            'filters'    => $filters
        ]);
    }
}
