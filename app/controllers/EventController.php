<?php
// app/controllers/EventController.php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Ticket.php';        // nếu chưa có model này, có thể bỏ dòng này
require_once __DIR__ . '/../models/Registration.php'; // dùng khi mua vé (nếu có luồng)

class EventController extends Controller
{
    protected function renderView(string $view, array $vars = []): void {
        // nếu Controller đã có ->render(), có thể thay bằng: $this->render($view, $vars); return;
        $base = dirname(__DIR__) . '/views';
        extract($vars, EXTR_SKIP);
        include $base . '/layouts/header.php';
        include $base . '/' . ltrim($view, '/');
        include $base . '/layouts/footer.php';
    }

    /** Danh sách (cho route r=event/list) */
    public function index(): void {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 9;
        $filters = [
            'q'      => trim($_GET['q'] ?? ''),
            'from'   => trim($_GET['from'] ?? ''),
            'to'     => trim($_GET['to'] ?? ''),
            'status' => trim($_GET['status'] ?? '')
        ];

        $event  = new Event();
        $result = $event->paginate($filters, $page, $perPage);

        $this->renderView('event/list.php', [
            'events'   => $result['data'],
            'total'    => $result['total'],
            'page'     => $page,
            'perPage'  => $perPage,
            'filters'  => $filters
        ]);
    }

    /** Chi tiết (r=event/detail&id=...) */
    public function detail(): void {
        $id = max(0, (int)($_GET['id'] ?? 0));
        if ($id <= 0) { http_response_code(404); echo 'Not found'; return; }

        $eventModel  = new Event();
        $ticketModel = class_exists('Ticket') ? new Ticket() : null;

        $event   = $eventModel->findById($id);
        $tickets = $ticketModel ? $ticketModel->listByEvent($id) : [];

        if (!$event) { http_response_code(404); echo 'Not found'; return; }

        $this->renderView('event/detail.php', [
            'event'   => $event,
            'tickets' => $tickets
        ]);
    }

    /** Thêm sự kiện (r=event/add) */
    public function add(): void {
        Auth::requireLogin();
        $errors = []; $ok = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title       = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $location    = trim($_POST['location'] ?? '');
            $image_url   = trim($_POST['image_url'] ?? '');
            $start_time  = trim($_POST['start_time'] ?? '');
            $end_time    = trim($_POST['end_time'] ?? '');

            if ($title === '')      $errors[] = 'Vui lòng nhập tiêu đề.';
            if ($start_time === '') $errors[] = 'Vui lòng nhập thời gian bắt đầu.';
            if ($end_time === '')   $errors[] = 'Vui lòng nhập thời gian kết thúc.';

            if (!$errors) {
                $id = (new Event())->create([
                    'title'       => $title,
                    'description' => $description,
                    'location'    => $location,
                    'image_url'   => $image_url,
                    'start_time'  => $start_time,
                    'end_time'    => $end_time,
                    'status'      => 'active',
                    'created_by'  => Auth::id()
                ]);
                $ok = true;
                header('Location: /public/index.php?r=event/detail&id='.(int)$id);
                exit;
            }
        }

        $this->renderView('event/add.php', [
            'errors' => $errors,
            'ok'     => $ok
        ]);
    }

    /** Sửa sự kiện (r=event/edit&id=...) */
    public function edit(): void {
        Auth::requireLogin();
        $id = max(0, (int)($_GET['id'] ?? 0));
        if ($id <= 0) { http_response_code(404); echo 'Not found'; return; }

        $eventModel = new Event();
        $event      = $eventModel->findById($id);
        if (!$event) { http_response_code(404); echo 'Not found'; return; }

        if (!$this->canManage($event)) { http_response_code(403); echo 'Forbidden'; return; }

        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'title'       => trim($_POST['title'] ?? $event['title']),
                'description' => trim($_POST['description'] ?? $event['description']),
                'location'    => trim($_POST['location'] ?? $event['location']),
                'image_url'   => trim($_POST['image_url'] ?? $event['image_url']),
                'start_time'  => trim($_POST['start_time'] ?? $event['start_time']),
                'end_time'    => trim($_POST['end_time'] ?? $event['end_time']),
                'status'      => trim($_POST['status'] ?? $event['status']),
            ];
            (new Event())->update($id, $data);
            header('Location: /public/index.php?r=event/detail&id='.$id);
            exit;
        }

        $this->renderView('event/edit.php', [
            'event' => $event,
            'errors'=> $errors
        ]);
    }

    /** Xoá (soft-delete) – POST tới r=event/delete */
    public function delete(): void {
        Auth::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }

        $id = max(0, (int)($_POST['id'] ?? 0));
        $event = (new Event())->findById($id);
        if (!$event) { http_response_code(404); echo 'Not found'; return; }

        if (!$this->canManage($event)) { http_response_code(403); echo 'Forbidden'; return; }

        (new Event())->delete($id, true);
        header('Location: /public/index.php?r=user/my-events');
        exit;
    }

    private function canManage(array $event): bool {
        if (Auth::isAdmin()) return true;
        return Auth::check() && (int)($event['created_by'] ?? 0) === Auth::id();
    }
}
