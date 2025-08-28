<?php
/**
 * Data Models
 * GeoPortfolio Pro - PHP Version
 */

class WorkModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAll($category = null, $limit = null) {
        $query = "SELECT * FROM works WHERE 1=1";
        $params = [];
        
        if ($category) {
            $query .= " AND category = ?";
            $params[] = $category;
        }
        
        $query .= " ORDER BY created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = $limit;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM works WHERE id = ?");
        $stmt->execute([$id]);
        $work = $stmt->fetch();
        
        if ($work && $work['tags']) {
            $work['tags'] = json_decode($work['tags'], true);
        }
        
        return $work;
    }
    
    public function create($data) {
        $tags = isset($data['tags']) ? json_encode($data['tags']) : null;
        
        $stmt = $this->db->prepare("
            INSERT INTO works (title, description, long_description, image_url, category, tags, link, image_style, place) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['title'], $data['description'], $data['long_description'],
            $data['image_url'], $data['category'], $tags, $data['link'],
            $data['image_style'], $data['place']
        ]);
    }
    
    public function update($id, $data) {
        $tags = isset($data['tags']) ? json_encode($data['tags']) : null;
        
        $stmt = $this->db->prepare("
            UPDATE works SET title=?, description=?, long_description=?, image_url=?, 
            category=?, tags=?, link=?, image_style=?, place=?, updated_at=NOW() 
            WHERE id=?
        ");
        
        return $stmt->execute([
            $data['title'], $data['description'], $data['long_description'],
            $data['image_url'], $data['category'], $tags, $data['link'],
            $data['image_style'], $data['place'], $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM works WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

class BlogModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAll($category = null, $limit = null) {
        $query = "SELECT * FROM blog_posts WHERE 1=1";
        $params = [];
        
        if ($category) {
            $query .= " AND category = ?";
            $params[] = $category;
        }
        
        $query .= " ORDER BY publish_date DESC, created_at DESC";
        
        if ($limit) {
            $query .= " LIMIT ?";
            $params[] = $limit;
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM blog_posts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO blog_posts (title, summary, content, image_url, publish_date, author, category, image_style, place) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['title'], $data['summary'], $data['content'], $data['image_url'],
            $data['publish_date'], $data['author'], $data['category'],
            $data['image_style'], $data['place']
        ]);
    }
    
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE blog_posts SET title=?, summary=?, content=?, image_url=?, 
            publish_date=?, author=?, category=?, image_style=?, place=?, updated_at=NOW() 
            WHERE id=?
        ");
        
        return $stmt->execute([
            $data['title'], $data['summary'], $data['content'], $data['image_url'],
            $data['publish_date'], $data['author'], $data['category'],
            $data['image_style'], $data['place'], $id
        ]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM blog_posts WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

class CategoryModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAll($type = null) {
        $query = "SELECT * FROM categories WHERE 1=1";
        $params = [];
        
        if ($type) {
            $query .= " AND type = ?";
            $params[] = $type;
        }
        
        $query .= " ORDER BY name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function create($name, $type) {
        $stmt = $this->db->prepare("INSERT INTO categories (name, type) VALUES (?, ?)");
        return $stmt->execute([$name, $type]);
    }
    
    public function update($id, $name, $type) {
        $stmt = $this->db->prepare("UPDATE categories SET name=?, type=?, updated_at=NOW() WHERE id=?");
        return $stmt->execute([$name, $type, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT id, name, email, role, status, last_login, created_at, updated_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, name, email, role, status, last_login, created_at, updated_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($name, $email, $password, $role = 'Editor') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, 'active')");
        return $stmt->execute([$name, $email, $hashedPassword, $role]);
    }
    
    public function update($id, $data) {
        $query = "UPDATE users SET name=?, email=?, role=?, status=?, updated_at=NOW() WHERE id=?";
        return $this->db->prepare($query)->execute([
            $data['name'], $data['email'], $data['role'], $data['status'], $id
        ]);
    }
    
    public function updatePassword($id, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password=?, updated_at=NOW() WHERE id=?");
        return $stmt->execute([$hashedPassword, $id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function toggleStatus($id) {
        $stmt = $this->db->prepare("UPDATE users SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END, updated_at=NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

class MessageModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAll($unreadOnly = false) {
        $query = "SELECT * FROM messages WHERE 1=1";
        if ($unreadOnly) {
            $query .= " AND is_read = 0";
        }
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->query($query);
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM messages WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO messages (name, email, institution, address, message) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['name'], $data['email'], $data['institution'], 
            $data['address'], $data['message']
        ]);
    }
    
    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM messages WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getUnreadCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM messages WHERE is_read = 0");
        return $stmt->fetch()['count'];
    }
}

class SkillModel {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    public function getAllWithItems() {
        $skills = $this->db->query("SELECT * FROM skills ORDER BY sort_order, id")->fetchAll();
        
        foreach ($skills as &$skill) {
            $stmt = $this->db->prepare("SELECT * FROM skill_items WHERE skill_id = ? ORDER BY sort_order, id");
            $stmt->execute([$skill['id']]);
            $skill['items'] = $stmt->fetchAll();
        }
        
        return $skills;
    }
    
    public function create($category, $items = []) {
        $this->db->beginTransaction();
        
        try {
            // Create skill category
            $stmt = $this->db->prepare("INSERT INTO skills (category) VALUES (?)");
            $stmt->execute([$category]);
            $skillId = $this->db->lastInsertId();
            
            // Add skill items
            foreach ($items as $item) {
                $stmt = $this->db->prepare("INSERT INTO skill_items (skill_id, name, percentage) VALUES (?, ?, ?)");
                $stmt->execute([$skillId, $item['name'], $item['percentage']]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    public function update($id, $category, $items = []) {
        $this->db->beginTransaction();
        
        try {
            // Update skill category
            $stmt = $this->db->prepare("UPDATE skills SET category = ? WHERE id = ?");
            $stmt->execute([$category, $id]);
            
            // Delete existing items
            $stmt = $this->db->prepare("DELETE FROM skill_items WHERE skill_id = ?");
            $stmt->execute([$id]);
            
            // Add new items
            foreach ($items as $item) {
                $stmt = $this->db->prepare("INSERT INTO skill_items (skill_id, name, percentage) VALUES (?, ?, ?)");
                $stmt->execute([$id, $item['name'], $item['percentage']]);
            }
            
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM skills WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>