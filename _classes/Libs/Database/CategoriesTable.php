<?php

namespace Libs\Database;

use PDOException;

class CategoriesTable
{
    private $db = null;
    public function __construct(MySQL $db)
    {
        $this->db = $db->connect();
    }

    public function insert($data)
    {
        try {
            $query = " INSERT INTO categories (name,description,image,created_at,updated_at) VALUES (:name,:description,:image,NOW(),NOW()) ";

            $statement = $this->db->prepare($query);
            $statement->execute($data);

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return $e->getMessage()();
        }
    }

    public function getAll()
    {
        $statement = $this->db->query(" SELECT * FROM categories ");

        return $statement->fetchAll();
    }

    public function getById($id)
    {
        $statement = $this->db->prepare(" SELECT * FROM categories WHERE id = :id ");
        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function update($id, $data)
    {
        try {
            $data['id'] = $id;

            $query = " UPDATE categories SET name = :name, description = :description, image = :image, updated_at = NOW() WHERE id = :id ";

            $statement = $this->db->prepare($query);
            $statement->execute($data);

            return true;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function delete($id)
    {
        try {
            $statement = $this->db->prepare(" DELETE FROM categories WHERE id = :id ");
            $statement->execute(['id' => $id]);

            return true;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }

    public function countAll()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM categories");
        return $stmt->fetchColumn();
    }
}
