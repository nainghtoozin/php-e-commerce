<?php

namespace Libs\Database;

use PDO;
use PDOException;

class OrdersTable
{
    private $db = null;
    
    public function __construct(MySQL $db)
    {
        $this->db = $db->connect();
    }
    
    public function getAll($limit = null, $offset = 0)
    {
        $sql = "
            SELECT 
                o.id,
                o.customer_name,
                o.phone,
                o.total_amount,
                o.status,
                o.created_at,
                COUNT(oi.id) as item_count
            FROM orders o
            LEFT JOIN order_items oi ON o.id = oi.order_id
            GROUP BY o.id
            ORDER BY o.created_at DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        
        if ($limit) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getLatest($limit = 10)
    {
        return $this->getAll($limit, 0);
    }
    
    public function findById($id)
    {
        // Get order details
        $stmt = $this->db->prepare("
            SELECT *
            FROM orders
            WHERE id = :id
        ");
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch();
        
        if (!$order) {
            return null;
        }
        
        // Get order items
        $items_stmt = $this->db->prepare("
            SELECT 
                oi.*,
                p.name as product_name,
                p.sku
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :id
        ");
        $items_stmt->execute([':id' => $id]);
        $order->items = $items_stmt->fetchAll();
        
        return $order;
    }
    
    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE orders
            SET status = :status
            WHERE id = :id
        ");
        
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }
    
    public function countAll()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM orders");
        return $stmt->fetchColumn();
    }
    
    public function getTotalRevenue()
    {
        $stmt = $this->db->query("
            SELECT SUM(total_amount) as total
            FROM orders
            WHERE status IN ('completed', 'pending')
        ");
        $result = $stmt->fetch();
        return $result->total ?? 0;
    }
    
    public function getRecentOrdersCount($days = 7)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM orders
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
        ");
        $stmt->bindValue(':days', (int)$days, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result->count ?? 0;
    }
}