-- 1. Create document_categories to map with Report Sections
CREATE TABLE IF NOT EXISTS `document_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(255) NOT NULL,
  `report_section` varchar(100) DEFAULT NULL, -- e.g., '1. Company Profile', '4. Deforestation'
  `description` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add category_id and supplier_id to supplier_documents for better grouping
-- (Checking if columns exist before adding is safer in some environments, but here we'll use direct ALTER)
ALTER TABLE `supplier_documents` 
ADD COLUMN `category_id` int(10) unsigned DEFAULT NULL AFTER `id`,
ADD COLUMN `supplier_id` int(10) unsigned DEFAULT NULL AFTER `category_id`;

-- 3. Add category_id to company_documents
ALTER TABLE `company_documents` 
ADD COLUMN `category_id` int(10) unsigned DEFAULT NULL AFTER `id`;

-- 4. Initial categories based on Invoice.blade.php structure
INSERT INTO `document_categories` (category_name, report_section) VALUES 
('Business Registration', '1. Company Profile'),
('Supply Chain Mapping', '2. Supply Chain'),
('Deforestation-free Verification', '4. Deforestation'),
('Legal Compliance', '5. Legal Compliance'),
('Employment Conditions', '5. Legal Compliance');
