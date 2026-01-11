<?php

namespace Libs\Database;

use PDO;
use PDOException;

class ProductsTable
{
    private $db = null;
    public function __construct(MySQL $db)
    {
        $this->db = $db->connect();
    }

    public function store($data, $files)
    {
        try {
            $this->db->beginTransaction();

            // 1️⃣ insert products table
            $stmt = $this->db->prepare("
            INSERT INTO products 
            (name, slug, sku, price, category_id, short_description, description, status)
            VALUES 
            (:name, :slug, :sku, :price, :category_id, :short_desc, :description, :status)
        ");

            $stmt->execute([
                ':name'        => $data['name'],
                ':slug'        => $data['slug'],
                ':sku'         => $data['sku'],
                ':price'       => $data['price'],
                ':category_id' => $data['category_id'],
                ':short_desc'  => $data['short_description'],
                ':description' => $data['description'],
                ':status'      => $data['status'],
            ]);

            // ✅ product id
            $productId = $this->db->lastInsertId();

            // 2️⃣ insert product_images (multiple)
            if (!empty($files['images']['name'][0])) {

                foreach ($files['images']['name'] as $key => $img) {

                    $ext  = pathinfo($img, PATHINFO_EXTENSION);
                    //$file = uniqid() . '.' . $ext;
                    $imageName = uniqid('prod_') . '.' . $ext;
                    $file      = $imageName;

                    move_uploaded_file(
                        $files['images']['tmp_name'][$key],
                        '../../public/uploads/products/' . $file
                    );

                    $imgStmt = $this->db->prepare("
                    INSERT INTO product_images 
                    (product_id, image, is_primary)
                    VALUES 
                    (:product_id, :image, :is_primary)
                ");

                    $imgStmt->execute([
                        ':product_id' => $productId,
                        ':image'      => $file,
                        ':is_primary' => $key === 0 ? 1 : 0
                    ]);
                }
            }

            // 3️⃣ insert inventories table  ✅ NEW
            $invStmt = $this->db->prepare("
            INSERT INTO inventories 
            (product_id, quantity)
            VALUES 
            (:product_id, :quantity)
        ");

            $invStmt->execute([
                ':product_id' => $productId,
                ':quantity'   => $data['quantity']
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }


    public function getAll()
    {
        $sql = "SELECT 
                p.*,
                c.name AS category,
                pi.image,
                i.quantity
            FROM products p
            JOIN categories c ON c.id = p.category_id
            LEFT JOIN product_images pi 
                ON pi.product_id = p.id AND pi.is_primary = 1
            LEFT JOIN inventories i ON i.product_id = p.id
            WHERE p.deleted_at IS NULL
            
            ORDER BY p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findById($id)
    {
        // product
        $stmt = $this->db->prepare("
        SELECT *
        FROM products
        WHERE id = :id
        AND deleted_at IS NULL
        LIMIT 1
    ");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();

        if (!$product) {
            return null;
        }

        // images (all)
        $imgStmt = $this->db->prepare("
        SELECT *
        FROM product_images
        WHERE product_id = :id
    ");
        $imgStmt->execute([':id' => $id]);
        $product->images = $imgStmt->fetchAll();

        // inventory
        $invStmt = $this->db->prepare("
        SELECT quantity
        FROM inventories
        WHERE product_id = :id
        LIMIT 1
    ");
        $invStmt->execute([':id' => $id]);
        $product->quantity = $invStmt->fetchColumn();

        return $product;
    }

    public function update($id, $data, $files)
    {
        try {
            $this->db->beginTransaction();

            // 1️⃣ update products
            $stmt = $this->db->prepare("
            UPDATE products SET
                name = :name,
                slug = :slug,
                sku = :sku,
                price = :price,
                category_id = :category_id,
                short_description = :short_desc,
                description = :description,
                status = :status
            WHERE id = :id
        ");

            $stmt->execute([
                ':name'        => $data['name'],
                ':slug'        => $data['slug'],
                ':sku'         => $data['sku'],
                ':price'       => $data['price'],
                ':category_id' => $data['category_id'],
                ':short_desc'  => $data['short_description'],
                ':description' => $data['description'],
                ':status'      => $data['status'],
                ':id'          => $id,
            ]);

            // 2️⃣ update inventory
            $invStmt = $this->db->prepare("
            UPDATE inventories
            SET quantity = :qty
            WHERE product_id = :id
        ");
            $invStmt->execute([
                ':qty' => $data['quantity'],
                ':id'  => $id,
            ]);

            // 3️⃣ add new images (optional)
            if (!empty($files['images']['name'][0])) {

                foreach ($files['images']['name'] as $key => $img) {

                    $ext  = pathinfo($img, PATHINFO_EXTENSION);
                    $imageName = uniqid('prod_') . '.' . $ext;
                    $file      = $imageName;

                    move_uploaded_file(
                        $files['images']['tmp_name'][$key],
                        '../../public/uploads/products/' . $file
                    );

                    $imgStmt = $this->db->prepare("
                    INSERT INTO product_images
                    (product_id, image, is_primary)
                    VALUES
                    (:product_id, :image, 0)
                ");

                    $imgStmt->execute([
                        ':product_id' => $id,
                        ':image'      => $file,
                    ]);
                }
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }


    public function delete($id)
    {
        try {
            $this->db->beginTransaction();

            // 1️⃣ soft delete product
            $stmt = $this->db->prepare("
            UPDATE products
            SET deleted_at = NOW()
            WHERE id = :id
        ");
            $stmt->execute([':id' => $id]);

            // 2️⃣ delete product images (DB only)
            $imgStmt = $this->db->prepare("
            DELETE FROM product_images
            WHERE product_id = :id
        ");
            $imgStmt->execute([':id' => $id]);

            // 3️⃣ delete inventory
            $invStmt = $this->db->prepare("
            DELETE FROM inventories
            WHERE product_id = :id
        ");
            $invStmt->execute([':id' => $id]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function filter($filters = [])
    {
        $sql = "
        SELECT 
        p.*,
        c.name AS category,
        pi.image AS image,
        i.quantity
        FROM products p
        JOIN categories c 
        ON c.id = p.category_id
        LEFT JOIN product_images pi 
        ON pi.product_id = p.id
        AND pi.is_primary = 1
        LEFT JOIN inventories i 
        ON i.product_id = p.id
        WHERE p.deleted_at IS NULL
    ";

        $params = [];

        // 🔍 Search (name / sku)
        if (!empty($filters['q'])) {
            $sql .= " AND (p.name LIKE :q OR p.sku LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        // 🧰 Status
        if ($filters['status'] !== null && $filters['status'] !== '') {
            $sql .= " AND p.status = :status";
            $params[':status'] = $filters['status'];
        }

        // 📦 Stock
        if ($filters['stock'] === 'in') {
            $sql .= " AND i.quantity > 0";
        }

        if ($filters['stock'] === 'out') {
            $sql .= " AND (i.quantity IS NULL OR i.quantity = 0)";
        }

        // 💰 Price From
        if (!empty($filters['price_from'])) {
            $sql .= " AND p.price >= :price_from";
            $params[':price_from'] = $filters['price_from'];
        }

        // 💰 Price To
        if (!empty($filters['price_to'])) {
            $sql .= " AND p.price <= :price_to";
            $params[':price_to'] = $filters['price_to'];
        }

        $sql .= " ORDER BY p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}
