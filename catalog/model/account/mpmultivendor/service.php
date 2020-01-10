<?php
class ModelAccountMpmultivendorProduct extends Model
{
    public function addProduct($data, $mpseller_id)
    {
        $sql = "INSERT INTO " . DB_PREFIX . "service SET model = '" . $this->db->escape($data['model']) . "', 
        sku = '" . $this->db->escape($data['sku']) . "', 
        upc = '" . $this->db->escape($data['upc']) . "', 
        ean = '" . $this->db->escape($data['ean']) . "', 
        jan = '" . $this->db->escape($data['jan']) . "', 
        isbn = '" . $this->db->escape($data['isbn']) . "', 
        mpn = '" . $this->db->escape($data['mpn']) . "', 
        location = '" . $this->db->escape($data['location']) . "', 
        quantity = '" . (int)$data['quantity'] . "', 
        minimum = '" . (int)$data['minimum'] . "'";


        $this->db->query($sql);

        $product_id = $this->db->getLastId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
        }

        $this->cache->delete('product');

        return $product_id;
    }
}