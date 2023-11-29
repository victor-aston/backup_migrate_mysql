<?php

namespace Drupal\backup_migrate_mysql;

use Drupal\backup_migrate\Core\Source\MySQLiSource;

/**
 * Class BackupMigrateMySQLSource. Provides wrapper class for the MySQL source plugin.
 */
class BackupMigrateMySQLSource extends MySQLiSource {

  /**
   * {@inheritdoc}
   */
  protected function _getTableCreateSql(array $table) {
    $out = parent::_getTableCreateSql($table);

    $convert = $this->confGet('convert') ?? 'mysql';
    switch ($convert) {
      case 'mariadb':
        $out = str_replace('DEFAULT CHARSET=utf8mb4_unicode_ci', 'DEFAULT CHARSET=utf8mb4_0900_ai_ci', $out);
        break;

      case 'mysql':
        $out = str_replace('DEFAULT CHARSET=utf8mb4_0900_ai_ci', 'DEFAULT CHARSET=utf8mb4_unicode_ci', $out);
        break;

    }

    return $out;
  }

  /**
   * {@inheritdoc}
   */
  public function configSchema(array $params = []) {
    $schema = parent::configSchema($params);

    $schema['fields']['convert'] = [
      'type' => 'enum',
      'title' => $this->t('Convert charset'),
      'options' => [
        'mysql' => $this->t('Convert for MySQL'),
        'mariadb' => $this->t('Convert for MariaDB'),
      ],
    ];

    return $schema;
  }

}
