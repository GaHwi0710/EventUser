<?php
class Event {
    private $conn;
    private $table_name = "events";

    public $id;
    public $title;
    public $slug;
    public $description;
    public $short_description;
    public $date;
    public $time;
    public $start_date;
    public $end_date;
    public $location;
    public $category_name;
    public $organizer_name;
    public $max_attendees;
    public $price;
    public $image;
    public $banner;
    public $status;
    public $featured;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO {$this->table_name} 
            (title, slug, description, short_description, date, time, start_date, end_date,
             location, category_name, organizer_name, max_attendees, price, image, status, featured, created_at)
            VALUES 
            (:title, :slug, :description, :short_description, :date, :time, :start_date, :end_date,
             :location, :category_name, :organizer_name, :max_attendees, :price, :image, :status, :featured, NOW())";

        $stmt = $this->conn->prepare($query);

        foreach (['title','slug','description','short_description','date','time','start_date','end_date',
                  'location','category_name','organizer_name','max_attendees','price','image','status','featured'] as $f) {
            $this->$f = htmlspecialchars(strip_tags($this->$f ?? ''));
        }

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":slug", $this->slug);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":short_description", $this->short_description);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":time", $this->time);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":category_name", $this->category_name);
        $stmt->bindParam(":organizer_name", $this->organizer_name);
        $stmt->bindParam(":max_attendees", $this->max_attendees);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":featured", $this->featured);

        return $stmt->execute();
    }

    public function readAll() {
        $query = "SELECT * FROM {$this->table_name} ORDER BY start_date ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readUpcoming() {
        $query = "
            SELECT * FROM {$this->table_name}
            WHERE 
                (
                    (start_date IS NOT NULL AND start_date >= CURDATE()) 
                    OR 
                    (start_date IS NULL AND date >= CURDATE())
                )
                AND (status IS NULL OR status = '' OR status = 'published' OR status = 'Hiển thị')
            ORDER BY start_date ASC, date ASC
            LIMIT 6
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readOne() {
        $query = "SELECT * FROM {$this->table_name} WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE {$this->table_name}
                  SET title = :title, slug = :slug, description = :description, short_description = :short_description,
                      date = :date, time = :time, start_date = :start_date, end_date = :end_date,
                      location = :location, category_name = :category_name, organizer_name = :organizer_name,
                      max_attendees = :max_attendees, price = :price, image = :image, 
                      status = :status, featured = :featured
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        foreach (['title','slug','description','short_description','date','time','start_date','end_date',
                  'location','category_name','organizer_name','max_attendees','price','image','status','featured'] as $f) {
            $this->$f = htmlspecialchars(strip_tags($this->$f ?? ''));
        }

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":slug", $this->slug);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":short_description", $this->short_description);
        $stmt->bindParam(":date", $this->date);
        $stmt->bindParam(":time", $this->time);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":location", $this->location);
        $stmt->bindParam(":category_name", $this->category_name);
        $stmt->bindParam(":organizer_name", $this->organizer_name);
        $stmt->bindParam(":max_attendees", $this->max_attendees);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":featured", $this->featured);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM {$this->table_name} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        return $stmt->execute();
    }
}
?>
`