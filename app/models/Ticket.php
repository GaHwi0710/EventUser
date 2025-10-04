<?php
// app/models/Ticket.php
require_once __DIR__ . '/../core/Model.php';

/**
 * Bảng event_tickets:
 * id (PK), event_id (FK), name, price (DECIMAL/INT), quantity (INT)
 */
class Ticket extends Model
{
    public function listByEvent(int $eventId): array {
        return $this->fetchAll(
            "SELECT * FROM event_tickets WHERE event_id = ? ORDER BY price ASC, id ASC",
            [$eventId]
        );
    }

    public function minPriceByEvent(int $eventId): ?float {
        $row = $this->fetch("SELECT MIN(price) AS p FROM event_tickets WHERE event_id = ?", [$eventId]);
        if (!$row || $row['p'] === null) return null;
        return (float)$row['p'];
    }

    public function find(int $id): ?array {
        return $this->fetch("SELECT * FROM event_tickets WHERE id = ? LIMIT 1", [$id]);
    }

    /**
     * $data: ['event_id','name','price','quantity']
     */
    public function create(array $data): int {
        $this->run(
            "INSERT INTO event_tickets (event_id, name, price, quantity) VALUES (?, ?, ?, ?)",
            [(int)$data['event_id'], (string)$data['name'], (float)$data['price'], (int)$data['quantity']]
        );
        return (int)$this->lastId();
    }

    public function update(int $id, array $data): bool {
        $sets = []; $vals = [];
        if (array_key_exists('name', $data))     { $sets[] = "name = ?";     $vals[] = $data['name']; }
        if (array_key_exists('price', $data))    { $sets[] = "price = ?";    $vals[] = (float)$data['price']; }
        if (array_key_exists('quantity', $data)) { $sets[] = "quantity = ?"; $vals[] = (int)$data['quantity']; }

        if (!$sets) return true;
        $vals[] = $id;
        $this->run("UPDATE event_tickets SET ".implode(',', $sets)." WHERE id = ?", $vals);
        return true;
    }

    public function delete(int $id): bool {
        $this->run("DELETE FROM event_tickets WHERE id = ?", [$id]);
        return true;
    }

    /** Trừ tồn khi mua (nếu bạn quản lý kho vé). */
    public function decrementStock(int $ticketId, int $qty): bool {
        // Đảm bảo quantity không âm
        $this->run(
            "UPDATE event_tickets SET quantity = CASE WHEN quantity >= ? THEN quantity - ? ELSE quantity END WHERE id = ?",
            [$qty, $qty, $ticketId]
        );
        return true;
    }
}
