<?php
/**
 * @var Magento_Db_Adapter_Pdo_Mysql $this
 */
$this->startSetup();

$this->run(
    "CREATE TABLE IF NOT EXISTS `{$this->getTable('impgrid/grid_group')}`(
      `entity_id` INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT(8) UNSIGNED NOT NULL,
      `title` VARCHAR(255) NOT NULL,
      `attributes` VARCHAR(255) DEFAULT '',
      `additional_columns` VARCHAR(255) DEFAULT '',
      `is_default` TINYINT(5) DEFAULT 0,
      PRIMARY KEY  (`entity_id`),
      KEY `IND_IM_PGRID_ORDER_USER_ID` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$this->run(
    "CREATE TABLE IF NOT EXISTS `{$this->getTable('impgrid/grid_column')}`(
      `entity_id` INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
      `code` VARCHAR(255) NOT NULL,
      `title` VARCHAR(255) NOT NULL,
      `column_type` VARCHAR(255) NOT NULL,
      `visible` TINYINT(5) DEFAULT 1,
      PRIMARY KEY  (`entity_id`),
      KEY `IND_IM_PGRID_ORDER_CODE_COLUMN` (`code`),
      KEY `IND_IM_PGRID_ORDER_TYPE_COLUMN` (`column_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$this->run(
    "CREATE TABLE IF NOT EXISTS `{$this->getTable('impgrid/grid_group_column')}`(
      `group_column_id` INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
      `column_id` int(8) UNSIGNED NOT NULL,
      `group_id` INT(8) UNSIGNED NOT NULL,
      `custom_title` VARCHAR(255) DEFAULT NULL,
      `is_visible` TINYINT(5) DEFAULT 1,
      PRIMARY KEY  (`group_column_id`),
      UNIQUE KEY `VTI_COLUMN_GROUP_ID` (`column_id`, `group_id`),
      KEY `IND_IM_PGRID_ORDER_COLUMN_VISIBLE` (`is_visible`),
      CONSTRAINT `FK_VTI_PRODUCT_GRID_COLUMN_ID` FOREIGN KEY (`column_id`) REFERENCES `{$this->getTable('impgrid/grid_column')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_VTI_PRODUCT_GRID_GROUP_ID` FOREIGN KEY (`group_id`) REFERENCES `{$this->getTable('impgrid/grid_group')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$this->run(
    "CREATE TABLE IF NOT EXISTS `{$this->getTable('impgrid/grid_group_attribute')}`(
      `group_attribute_id` INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
      `attribute_id` int(8) UNSIGNED NOT NULL,
      `group_id` INT(8) UNSIGNED NOT NULL,
      `custom_title` VARCHAR(255) DEFAULT NULL,
      PRIMARY KEY  (`group_attribute_id`),
      CONSTRAINT `FK_VTI_PRODUCT_GRID_ATTRIBUTE_GROUP_ID` FOREIGN KEY (`group_id`) REFERENCES `{$this->getTable('impgrid/grid_group')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
);

$conn = $this->getConnection();
$conn->insertMultiple(
    $this->getTable('impgrid/grid_column'), array(
        array(
            'code'        => 'entity_id',
            'title'       => 'ID',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'name',
            'title'       => 'Name',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'second_name',
            'title'       => 'Second Name',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'type',
            'title'       => 'Type',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'set_name',
            'title'       => 'Attrib. Set Name',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'sku',
            'title'       => 'SKU',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'price',
            'title'       => 'Price',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'qty',
            'title'       => 'Qty',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'visibility',
            'title'       => 'Visibility',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'status',
            'title'       => 'Status',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'websites',
            'title'       => 'Websites',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'season',
            'title'       => 'Season',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'product_type',
            'title'       => 'SKU Type',
            'column_type' => 'standard',
            'visible'     => 1,
        ), array(
            'code'        => 'action',
            'title'       => 'Action',
            'column_type' => 'standard',
            'visible'     => 1,
        ),
    )
);

$this->endSetup();

/**
 * Setup data
 */
$admins = Mage::getModel('admin/user')->getCollection();
$columns = Mage::getModel('impgrid/column')->getCollection();

/**
 * Init default
 */
$defaultGroup = Mage::getModel('impgrid/group');
$defaultGroup->setData('title', 'Default')
    ->setData('user_id', '1')
    ->save();
Mage::getConfig()->saveConfig('impgrid/attributes/ongrid', $defaultGroup->getId());

foreach ($columns as $columnData) {
    $defColumnModel = Mage::getModel('impgrid/groupcolumn');
    $defColumnModel->setData('column_id', $columnData['entity_id'])
        ->setData('group_id', $defaultGroup->getId())
        ->setData('is_visible', $columnData['visible'])
        ->setData('custom_title', $columnData['title'])
        ->save();
}

/**
 * Set for each admin user
 */
foreach ($admins as $admin) {
    $currentGroup = Mage::getModel('impgrid/group');
    $currentGroup->setData('title', 'Default');
    $currentGroup->setData('user_id', $admin->getId());
    $currentGroup->save();

    Mage::getConfig()->saveConfig('impgrid/attributes/ongrid' . $admin->getId(), $currentGroup->getId());

    foreach ($columns as $columnData) {
        $columnModel = Mage::getModel('impgrid/groupcolumn');
        $columnModel->setData('column_id', $columnData['entity_id']);
        $columnModel->setData('group_id', $currentGroup->getId());
        $columnModel->setData('is_visible', $columnData['visible']);
        $columnModel->setData('custom_title', $columnData['title']);
        $columnModel->save();
    }
}
