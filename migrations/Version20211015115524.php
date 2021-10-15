<?php

declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211015115524 extends AbstractMigration
{
    /**
     * @inheritDoc
     */
    public function up(Schema $schema): void
    {
        $this->addSql(
            "
            RENAME TABLE tblProductData to products;
            ALTER TABLE products DROP intProductDataId;
            ALTER TABLE products ADD id CHAR(36) PRIMARY KEY FIRST;
            ALTER TABLE products CHANGE strProductName name VARCHAR (50) NOT NULL;
            ALTER TABLE products CHANGE strProductDesc description VARCHAR(255) NOT NULL;
            ALTER TABLE products DROP INDEX strProductCode;
            ALTER TABLE products CHANGE strProductCode code VARCHAR(10) NOT NULL;
            ALTER TABLE products ADD UNIQUE(code);
            ALTER TABLE products ADD price DECIMAL (10, 2) NOT NULL AFTER code;
            ALTER TABLE products ADD stock VARCHAR(10) NOT NULL AFTER price;
            ALTER TABLE products CHANGE dtmDiscontinued discontinued_at datetime DEFAULT NULL;
            ALTER TABLE products CHANGE dtmAdded created_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
            ALTER TABLE products CHANGE stmTimestamp updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
        "
        );
    }

    /**
     * @inheritDoc
     */
    public function down(Schema $schema): void
    {
        $this->addSql("
            RENAME TABLE products to tblProductData;
            ALTER TABLE tblProductData DROP id;
            ALTER TABLE tblProductData ADD intProductDataId int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;
            ALTER TABLE tblProductData CHANGE name strProductName varchar(50) NOT NULL;
            ALTER TABLE tblProductData CHANGE description strProductDesc varchar(255) NOT NULL;
            ALTER TABLE tblProductData DROP INDEX code;
            ALTER TABLE tblProductData CHANGE code strProductCode varchar(10) NOT NULL;
            ALTER TABLE tblProductData ADD UNIQUE(strProductCode);
            ALTER TABLE tblProductData DROP price;
            ALTER TABLE tblProductData DROP stock;
            ALTER TABLE tblProductData CHANGE discontinued_at dtmDiscontinued datetime DEFAULT NULL;
            ALTER TABLE tblProductData CHANGE created_at dtmAdded datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
            ALTER TABLE tblProductData CHANGE updated_at stmTimestamp  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
            ");
    }
}
